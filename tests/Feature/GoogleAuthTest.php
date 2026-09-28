<?php

use App\Models\User;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Two\User as SocialiteUser;

beforeEach(function () {
    $this->googleUser = (new SocialiteUser)->map([
        'id' => '123456789',
        'name' => 'Nguyen Van A',
        'email' => 'nguyenvana@gmail.com',
        'avatar' => 'https://lh3.googleusercontent.com/photo.jpg',
        'token' => 'google-token',
        'refreshToken' => null,
        'expiresIn' => 3600,
    ]);

    $mockProvider = Mockery::mock(Provider::class);
    $mockProvider->shouldReceive('stateless')->andReturnSelf();
    $mockProvider->shouldReceive('user')->andReturn($this->googleUser);
    $mockProvider->shouldReceive('redirect')->andReturn(redirect('https://accounts.google.com/oauth'));

    $mockSocialite = Mockery::mock(SocialiteFactory::class);
    $mockSocialite->shouldReceive('driver')->with('google')->andReturn($mockProvider);

    app()->instance(SocialiteFactory::class, $mockSocialite);
});

test('google redirect route resolves correctly', function () {
    $response = $this->get(route('auth.google'));

    $response->assertRedirect();
});

test('callback creates new user when not found', function () {
    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect('/');

    $this->assertDatabaseHas('users', [
        'email' => 'nguyenvana@gmail.com',
        'google_id' => '123456789',
        'name' => 'Nguyen Van A',
    ]);

    $this->assertAuthenticatedAs(User::where('email', 'nguyenvana@gmail.com')->first());
});

test('callback logs in existing user by google_id', function () {
    $user = User::factory()->create([
        'email' => 'nguyenvana@gmail.com',
        'google_id' => '123456789',
    ]);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
    $this->assertDatabaseCount('users', 1);
});

test('callback merges google_id into existing account found by email', function () {
    $user = User::factory()->create([
        'email' => 'nguyenvana@gmail.com',
        'google_id' => null,
    ]);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);

    $this->assertDatabaseHas('users', [
        'email' => 'nguyenvana@gmail.com',
        'google_id' => '123456789',
    ]);

    $this->assertDatabaseCount('users', 1);
});

test('callback redirects admin to admin dashboard', function () {
    User::factory()->create([
        'email' => 'nguyenvana@gmail.com',
        'google_id' => '123456789',
        'role' => 'admin',
    ]);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect('/admin/dashboard');
});
