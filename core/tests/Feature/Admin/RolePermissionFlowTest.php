<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;               // ← your guard model
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Illuminate\Support\Facades\Route;

class RolePermissionFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_with_role_and_permission_can_pass_middlewares()
    {
        /* -----------------------------------------------------------------
         |  1. Seed data exactly the way the UI does
         * -----------------------------------------------------------------*/
        $viewDb     = Permission::create(['name' => 'view customers',   'guard_name' => 'admin']);
        $deleteDb   = Permission::create(['name' => 'delete customers', 'guard_name' => 'admin']);

        $crmRole    = Role::create(['name' => 'customer manager', 'guard_name' => 'admin']);
        $crmRole->givePermissionTo($viewDb, $deleteDb);

        $admin = Admin::create([
            'name'     => 'Alice',
            'email'    => 'alice@example.com',
            'username' => 'alice',
            'password' => bcrypt('secret'),
        ]);
        $admin->assignRole('customer manager');
        // (extra explicit:) $admin->givePermissionTo('view customers');

        /* -----------------------------------------------------------------
         |  2. Fake routes to hit inside the test
         * -----------------------------------------------------------------*/
        Route::middleware(['role:customer manager'])                // role-based
             ->get('/dummy-role', fn () => 'OK');

        Route::middleware(['permission:view customers'])            // permission-based
             ->get('/dummy-perm', fn () => 'OK');

        /* -----------------------------------------------------------------
         |  3. Assertions
         * -----------------------------------------------------------------*/
        // Acting as *guest* → 403
        $this->get('/dummy-role')->assertForbidden();
        $this->get('/dummy-perm')->assertForbidden();

        // Acting as our admin → 200
        $this->actingAs($admin, 'admin')
             ->get('/dummy-role')
             ->assertOk()
             ->assertSee('OK');

        $this->actingAs($admin, 'admin')
             ->get('/dummy-perm')
             ->assertOk()
             ->assertSee('OK');
    }
}
