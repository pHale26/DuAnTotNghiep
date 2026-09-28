<?php

use App\Models\User;
use App\Models\UserIdentity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('encrypts front_image_url and back_image_url when saving via model', function () {
    $user = User::factory()->create();

    $identity = UserIdentity::create([
        'user_id' => $user->id,
        'identity_number' => '012345678901',
        'full_name' => 'Nguyễn Văn A',
        'date_of_birth' => '1990-01-01',
        'issue_date' => '2020-01-01',
        'expiry_date' => '2030-01-01',
        'issue_place' => 'Hà Nội',
        'front_image_url' => '/storage/identities/front.jpg',
        'back_image_url' => '/storage/identities/back.jpg',
    ]);

    // Đọc giá trị thô trong DB — phải là chuỗi encrypted, không phải plaintext
    $rawFront = DB::table('user_identities')->where('id', $identity->id)->value('front_image_url');
    $rawBack = DB::table('user_identities')->where('id', $identity->id)->value('back_image_url');

    expect($rawFront)->not->toBe('/storage/identities/front.jpg');
    expect($rawBack)->not->toBe('/storage/identities/back.jpg');

    // Giải mã phải trả về giá trị gốc
    expect(Crypt::decryptString($rawFront))->toBe('/storage/identities/front.jpg');
    expect(Crypt::decryptString($rawBack))->toBe('/storage/identities/back.jpg');
});

it('auto-decrypts when reading front_image_url and back_image_url via model', function () {
    $user = User::factory()->create();

    $identity = UserIdentity::create([
        'user_id' => $user->id,
        'identity_number' => '012345678902',
        'full_name' => 'Trần Thị B',
        'date_of_birth' => '1995-05-15',
        'issue_date' => '2021-06-01',
        'expiry_date' => '2031-06-01',
        'issue_place' => 'TP. Hồ Chí Minh',
        'front_image_url' => '/storage/identities/front_b.jpg',
        'back_image_url' => '/storage/identities/back_b.jpg',
    ]);

    // Đọc lại qua model — phải tự giải mã về plaintext
    $fresh = UserIdentity::find($identity->id);

    expect($fresh->front_image_url)->toBe('/storage/identities/front_b.jpg');
    expect($fresh->back_image_url)->toBe('/storage/identities/back_b.jpg');
});

it('encrypts existing plaintext data via identity:encrypt-images command', function () {
    $user = User::factory()->create();

    // Lưu plaintext trực tiếp vào DB (bypass model cast)
    DB::table('user_identities')->insert([
        'user_id' => $user->id,
        'identity_number' => '012345678903',
        'identity_number_hash' => hash('sha256', '012345678903'),
        'full_name' => 'Lê Văn C',
        'date_of_birth' => '1985-03-20',
        'issue_date' => '2019-01-01',
        'expiry_date' => '2029-01-01',
        'issue_place' => 'Đà Nẵng',
        'front_image_url' => '/storage/identities/old_front.jpg',
        'back_image_url' => '/storage/identities/old_back.jpg',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Chạy command
    $this->artisan('identity:encrypt-images')
        ->assertSuccessful()
        ->expectsOutputToContain('Đã mã hóa: 1 bản ghi');

    // Kiểm tra DB đã được encrypt
    $raw = DB::table('user_identities')->where('user_id', $user->id)->first();
    expect(Crypt::decryptString($raw->front_image_url))->toBe('/storage/identities/old_front.jpg');
    expect(Crypt::decryptString($raw->back_image_url))->toBe('/storage/identities/old_back.jpg');
});
