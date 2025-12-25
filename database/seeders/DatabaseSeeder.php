<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Creates roles
        $adminRole = Role::create(['name' => RolesEnum::ADMIN->value]);
        $userRole = Role::create(['name' => RolesEnum::USER->value]);

        // Creates users with different roles
        User::factory()->create([
            'email' => 'admin@email.com',
            'password' => 'admin123',
        ])->assignRole($adminRole);

        User::factory()->create([
            'email' => 'user@email.com',
            'password' => 'user123',
        ])->assignRole($userRole);
    }
}
