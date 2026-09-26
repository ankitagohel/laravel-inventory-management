<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRolesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $manager;
    private User $staff;
    private User $auditor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'role' => User::ROLE_ADMIN,
            'password' => bcrypt('password'),
        ]);

        $this->manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@test.com',
            'role' => User::ROLE_MANAGER,
            'password' => bcrypt('password'),
        ]);

        $this->staff = User::create([
            'name' => 'Staff User',
            'email' => 'staff@test.com',
            'role' => User::ROLE_STAFF,
            'password' => bcrypt('password'),
        ]);

        $this->auditor = User::create([
            'name' => 'Auditor User',
            'email' => 'auditor@test.com',
            'role' => User::ROLE_AUDITOR,
            'password' => bcrypt('password'),
        ]);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_users_management(): void
    {
        $response = $this->actingAs($this->admin)->get(route('users.index'));
        $response->assertStatus(200);
        $response->assertSee('Access Control & User Roles');
    }

    public function test_manager_and_staff_cannot_access_users_management(): void
    {
        $resManager = $this->actingAs($this->manager)->get(route('users.index'));
        $resManager->assertStatus(403);

        $resStaff = $this->actingAs($this->staff)->get(route('users.index'));
        $resStaff->assertStatus(403);

        $resAuditor = $this->actingAs($this->auditor)->get(route('users.index'));
        $resAuditor->assertStatus(403);
    }

    public function test_admin_can_delete_product(): void
    {
        $product = Product::create([
            'sku' => 'DEL-01',
            'name' => 'Delete Me',
            'cost_price' => 10,
            'selling_price' => 15,
            'current_stock' => 5,
            'min_stock_alert' => 2,
            'unit' => 'pcs',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('products.destroy', $product));
        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_manager_and_staff_cannot_delete_product(): void
    {
        $product = Product::create([
            'sku' => 'NODELETE-01',
            'name' => 'Protected Item',
            'cost_price' => 10,
            'selling_price' => 15,
            'current_stock' => 5,
            'min_stock_alert' => 2,
            'unit' => 'pcs',
            'status' => 'active',
        ]);

        $resManager = $this->actingAs($this->manager)->delete(route('products.destroy', $product));
        $resManager->assertStatus(403);

        $resStaff = $this->actingAs($this->staff)->delete(route('products.destroy', $product));
        $resStaff->assertStatus(403);

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_manager_can_create_product_but_staff_cannot(): void
    {
        // Manager creates product
        $resManager = $this->actingAs($this->manager)->post(route('products.store'), [
            'name' => 'Manager Item',
            'sku' => 'MGR-ITEM-01',
            'cost_price' => 20,
            'selling_price' => 30,
            'min_stock_alert' => 5,
            'unit' => 'pcs',
        ]);
        $resManager->assertRedirect(route('products.index'));

        // Staff tries to create product -> 403 Forbidden
        $resStaff = $this->actingAs($this->staff)->post(route('products.store'), [
            'name' => 'Staff Item',
            'sku' => 'STF-ITEM-01',
            'cost_price' => 20,
            'selling_price' => 30,
            'min_stock_alert' => 5,
            'unit' => 'pcs',
        ]);
        $resStaff->assertStatus(403);
    }

    public function test_staff_can_record_stock_movement_but_auditor_cannot(): void
    {
        $product = Product::create([
            'sku' => 'STK-01',
            'name' => 'Stock Test Item',
            'cost_price' => 10,
            'selling_price' => 20,
            'current_stock' => 10,
            'min_stock_alert' => 5,
            'unit' => 'pcs',
            'status' => 'active',
        ]);

        // Staff records Stock IN
        $resStaff = $this->actingAs($this->staff)->post(route('stock.store'), [
            'product_id' => $product->id,
            'type' => 'IN',
            'quantity' => 10,
            'reason' => 'Staff Intake',
        ]);
        $resStaff->assertSessionHas('success');
        $this->assertEquals(20, $product->fresh()->current_stock);

        // Auditor tries to record movement -> 403 Forbidden
        $resAuditor = $this->actingAs($this->auditor)->post(route('stock.store'), [
            'product_id' => $product->id,
            'type' => 'OUT',
            'quantity' => 2,
        ]);
        $resAuditor->assertStatus(403);
    }

    public function test_quick_demo_login_authenticates_user(): void
    {
        $response = $this->get(route('login.quick', 'manager'));
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->manager);
    }
}
