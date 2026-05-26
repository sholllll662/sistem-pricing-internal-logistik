<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_away_from_admin_panel(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_user_without_operational_role_is_forbidden_from_admin_panel(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertForbidden();
    }

    public function test_authenticated_admin_user_can_access_admin_panel(): void
    {
        $adminRole = Role::query()->create([
            'code' => 'admin',
            'name' => 'Admin',
        ]);

        $adminUser = User::factory()->create();
        $adminUser->roles()->attach($adminRole);

        $response = $this->actingAs($adminUser)->get('/admin');

        $response->assertOk();
    }

    public function test_authenticated_sales_user_can_access_admin_panel(): void
    {
        $salesRole = Role::query()->create([
            'code' => 'sales',
            'name' => 'Sales',
        ]);

        $salesUser = User::factory()->create();
        $salesUser->roles()->attach($salesRole);

        $response = $this->actingAs($salesUser)->get('/admin');

        $response->assertOk();
    }
}
