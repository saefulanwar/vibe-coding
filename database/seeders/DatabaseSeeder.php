<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $adminFakultasRole = Role::firstOrCreate(['name' => 'admin_fakultas']);
        $memberRole = Role::firstOrCreate(['name' => 'member']);

        // Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        // Admin Fakultas
        $adminFakultas = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Fakultas',
                'password' => Hash::make('password'),
            ]
        );
        $adminFakultas->assignRole($adminFakultasRole);

        // Member
        $member = User::updateOrCreate(
            ['email' => 'member@example.com'],
            [
                'name' => 'Member User',
                'password' => Hash::make('password'),
            ]
        );
        $member->assignRole($memberRole);
    }
}
