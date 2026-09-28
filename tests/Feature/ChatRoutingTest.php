<?php

use App\Models\Conversation;
use App\Models\User;
use App\Services\ChatDistributionService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

test('middleware updates last_seen_at for logged in users', function () {
    $user = User::factory()->create();

    expect($user->last_seen_at)->toBeNull();

    $this->actingAs($user)->get('/');

    $user->refresh();
    expect($user->last_seen_at)->not->toBeNull();
});

test('least connections algorithm routes to cskh with fewer chats', function () {
    // 1. Create two CSKH agents
    $cskh1 = User::factory()->create(['last_seen_at' => now(), 'is_active' => true, 'role' => 'cskh']);
    $cskh1->assignRole('cskh');

    $cskh2 = User::factory()->create(['last_seen_at' => now(), 'is_active' => true, 'role' => 'cskh']);
    $cskh2->assignRole('cskh');

    // 2. Assign 2 open conversations to cskh1
    Conversation::create([
        'user_id' => User::factory()->create()->id,
        'cskh_id' => $cskh1->id,
        'status' => 'open',
    ]);
    Conversation::create([
        'user_id' => User::factory()->create()->id,
        'cskh_id' => $cskh1->id,
        'status' => 'open',
    ]);

    // 3. Assign 1 open conversation to cskh2
    Conversation::create([
        'user_id' => User::factory()->create()->id,
        'cskh_id' => $cskh2->id,
        'status' => 'open',
    ]);

    // 4. Create a new conversation that needs routing
    $customer = User::factory()->create();
    $conversation = Conversation::create([
        'user_id' => $customer->id,
        'status' => 'open',
    ]);

    // 5. Run distribution service
    $service = app(ChatDistributionService::class);
    $assignedAgent = $service->assign($conversation);

    // Should assign to cskh2 since they have 1 chat while cskh1 has 2 chats
    expect($assignedAgent->id)->toBe($cskh2->id);
    expect($conversation->fresh()->cskh_id)->toBe($cskh2->id);
    expect($conversation->fresh()->routing_status)->toBe('assigned');
});

test('chats assigned to offline cskh are re-routed', function () {
    // 1. Create one online agent and one offline agent
    $onlineCskh = User::factory()->create(['last_seen_at' => now(), 'is_active' => true, 'role' => 'cskh']);
    $onlineCskh->assignRole('cskh');

    $offlineCskh = User::factory()->create(['last_seen_at' => now()->subMinutes(10), 'is_active' => true, 'role' => 'cskh']);
    $offlineCskh->assignRole('cskh');

    // 2. Assign conversation to offline CSKH
    $customer = User::factory()->create();
    $conversation = Conversation::create([
        'user_id' => $customer->id,
        'status' => 'open',
        'cskh_id' => $offlineCskh->id,
        'routing_status' => 'assigned',
    ]);

    // 3. Run re-routing artisan command
    Artisan::call('chat:re-route-offline');

    // 4. Check if the conversation has been re-routed to the online CSKH
    $conversation->refresh();
    expect($conversation->cskh_id)->toBe($onlineCskh->id);
    expect($conversation->routing_status)->toBe('assigned');
});

test('cskh sees conversations assigned to them or unassigned', function () {
    // 1. Create two CSKH agents
    $cskh1 = User::factory()->create(['last_seen_at' => now(), 'is_active' => true, 'role' => 'cskh']);
    $cskh1->assignRole('cskh');

    $cskh2 = User::factory()->create(['last_seen_at' => now(), 'is_active' => true, 'role' => 'cskh']);
    $cskh2->assignRole('cskh');

    // 2. Create conversations: one for cskh1, one for cskh2, one unassigned
    Conversation::create([
        'user_id' => User::factory()->create()->id,
        'cskh_id' => $cskh1->id,
        'status' => 'open',
        'routing_status' => 'assigned',
    ]);
    Conversation::create([
        'user_id' => User::factory()->create()->id,
        'cskh_id' => $cskh2->id,
        'status' => 'open',
        'routing_status' => 'assigned',
    ]);
    Conversation::create([
        'user_id' => User::factory()->create()->id,
        'cskh_id' => null,
        'status' => 'open',
        'routing_status' => 'unassigned',
    ]);

    // 3. Request conversations list as cskh1
    $response = $this->actingAs($cskh1)->getJson('/chat/conversations');

    // 4. Verify cskh1 receives their own conversation AND the unassigned one (total = 2)
    $response->assertStatus(200);
    $data = $response->json();
    expect(count($data))->toBe(2);
});

test('logging out clears last_seen_at timestamp', function () {
    $user = User::factory()->create(['last_seen_at' => now()]);

    $response = $this->actingAs($user)->post('/logout');

    $user->refresh();
    expect($user->last_seen_at)->toBeNull();
});

test('cskh can mark conversations as read and update unread count', function () {
    // 1. Create CSKH and Customer
    $cskh = User::factory()->create(['last_seen_at' => now(), 'is_active' => true, 'role' => 'cskh']);
    $cskh->assignRole('cskh');

    $customer = User::factory()->create(['role' => 'customer']);

    // 2. Create conversation assigned to CSKH
    $conversation = Conversation::create([
        'user_id' => $customer->id,
        'cskh_id' => $cskh->id,
        'status' => 'open',
        'routing_status' => 'assigned',
    ]);

    // 3. Create an unread message from the Customer
    $conversation->messages()->create([
        'sender_id' => $customer->id,
        'message' => 'Hello support',
        'read_at' => null,
    ]);

    // 4. Request unread count as CSKH - should be 1
    $response = $this->actingAs($cskh)->getJson('/chat/unread-count');
    $response->assertStatus(200);
    expect($response->json()['count'])->toBe(1);

    // 5. Mark as read
    $response = $this->actingAs($cskh)->postJson("/chat/{$conversation->id}/mark-as-read");
    $response->assertStatus(200);

    // 6. Request unread count as CSKH - should be 0
    $response = $this->actingAs($cskh)->getJson('/chat/unread-count');
    $response->assertStatus(200);
    expect($response->json()['count'])->toBe(0);
});
