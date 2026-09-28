<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Khởi tạo các permissions cơ bản (nếu cần)
        $permissions = [
            'manage system',
            'manage tours',
            'manage bookings',
            'manage invoices',
            'view guide schedules',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Tạo roles và gán permissions
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo(Permission::all());

        $staff = Role::firstOrCreate(['name' => 'Staff']);
        $staff->givePermissionTo(['manage bookings', 'manage invoices']);

        $guide = Role::firstOrCreate(['name' => 'Guide']);
        $guide->givePermissionTo(['view guide schedules']);

        $cskh = Role::firstOrCreate(['name' => 'cskh', 'guard_name' => 'web']);

        $cskhUser = User::firstOrCreate(
            ['email' => 'cskh@travel.com'],
            [
                'name' => 'Nhân viên CSKH',
                'password' => Hash::make('password'),
                'phone' => '0987654321',
                'role' => 'cskh',
            ]
        );
        $cskhUser->assignRole($cskh);

        // Phân quyền cho một số user có sẵn dựa vào role cũ (chuỗi)
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role == 'admin') {
                $user->assignRole('Admin');
            } elseif ($user->role == 'staff') {
                $user->assignRole('Staff');
            } elseif ($user->role == 'guide') {
                $user->assignRole('Guide');
            } elseif ($user->role == 'cskh') {
                $user->assignRole('cskh');
            }
        }
    }
}
