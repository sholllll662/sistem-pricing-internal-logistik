<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roleMap = [
            'sales' => [
                'name' => 'Sales',
                'description' => 'Handle inquiry, scenario, and pricing preparation.',
            ],
            'manager' => [
                'name' => 'Manager',
                'description' => 'Review and approve pricing and quotes.',
            ],
            'admin' => [
                'name' => 'Admin',
                'description' => 'Manage system setup and operational support.',
            ],
        ];

        $roles = collect($roleMap)->mapWithKeys(
            fn (array $attributes, string $code) => [
                $code => Role::query()->updateOrCreate(
                    ['code' => $code],
                    $attributes
                ),
            ]
        );

        if (! app()->environment(['local', 'development', 'uat'])) {
            return;
        }

        $adminUser = User::query()->updateOrCreate([
            'email' => env('ADMIN_EMAIL', 'admin@example.com'),
        ], [
            'name' => env('ADMIN_NAME', 'Admin User'),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
        ]);

        $adminRole = $roles->get('admin');
        $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);

        $this->call(UatSeedDataSeeder::class);
    }
}
