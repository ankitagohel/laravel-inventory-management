<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventorySystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = \App\Models\User::create([
            'name' => 'System Admin',
            'email' => 'admin@test.com',
            'role' => \App\Models\User::ROLE_ADMIN,
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($admin);
    }

    public function test_dashboard_renders_with_kpis(): void
    {
        $category = Category::create(['name' => 'Hardware', 'code' => 'HDW']);
        $product = Product::create([
            'sku' => 'TEST-001',
            'name' => 'Wireless Mouse',
            'cost_price' => 20.00,
            'selling_price' => 35.00,
            'current_stock' => 15,
            'min_stock_alert' => 5,
            'unit' => 'pcs',
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('TEST-001');
        $response->assertSee('Wireless Mouse');
    }

    public function test_can_create_new_product(): void
    {
        $category = Category::create(['name' => 'Cables', 'code' => 'CBL']);

        $response = $this->post(route('products.store'), [
            'name' => 'HDMI 2.1 Ultra High Speed Cable',
            'sku' => 'CBL-HDMI-21',
            'category_id' => $category->id,
            'cost_price' => 5.50,
            'selling_price' => 14.99,
            'initial_stock' => 50,
            'min_stock_alert' => 10,
            'unit' => 'pcs',
            'warehouse_location' => 'Bay 4',
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', [
            'sku' => 'CBL-HDMI-21',
            'current_stock' => 50,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'type' => 'IN',
            'quantity' => 50,
        ]);
    }

    public function test_stock_in_movement_increments_inventory(): void
    {
        $product = Product::create([
            'sku' => 'LAP-001',
            'name' => 'Gaming Laptop',
            'cost_price' => 800.00,
            'selling_price' => 1200.00,
            'current_stock' => 10,
            'min_stock_alert' => 5,
            'unit' => 'pcs',
            'status' => 'active',
        ]);

        $response = $this->post(route('stock.store'), [
            'product_id' => $product->id,
            'type' => 'IN',
            'quantity' => 5,
            'reason' => 'Supplier Shipment',
            'reference_no' => 'PO-9999',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(15, $product->fresh()->current_stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'IN',
            'quantity' => 5,
            'previous_stock' => 10,
            'resulting_stock' => 15,
        ]);
    }

    public function test_stock_out_movement_decrements_inventory(): void
    {
        $product = Product::create([
            'sku' => 'MOU-001',
            'name' => 'Optical Mouse',
            'cost_price' => 10.00,
            'selling_price' => 20.00,
            'current_stock' => 20,
            'min_stock_alert' => 5,
            'unit' => 'pcs',
            'status' => 'active',
        ]);

        $response = $this->post(route('stock.store'), [
            'product_id' => $product->id,
            'type' => 'OUT',
            'quantity' => 8,
            'reason' => 'Sales Order',
            'reference_no' => 'SO-1001',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(12, $product->fresh()->current_stock);
    }

    public function test_stock_out_fails_when_insufficient_stock(): void
    {
        $product = Product::create([
            'sku' => 'MON-001',
            'name' => '4K Monitor',
            'cost_price' => 200.00,
            'selling_price' => 350.00,
            'current_stock' => 3,
            'min_stock_alert' => 2,
            'unit' => 'pcs',
            'status' => 'active',
        ]);

        $response = $this->post(route('stock.store'), [
            'product_id' => $product->id,
            'type' => 'OUT',
            'quantity' => 10, // Exceeds available stock
        ]);

        $response->assertSessionHasErrors('quantity');
        $this->assertEquals(3, $product->fresh()->current_stock);
    }

    public function test_csv_export_returns_stream(): void
    {
        $response = $this->get(route('export.products'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
