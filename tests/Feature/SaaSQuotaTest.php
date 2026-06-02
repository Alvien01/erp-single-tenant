<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaaSQuotaTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a plan with strict limits
        $this->plan = Plan::create([
            'name' => 'Micro Plan',
            'price_monthly' => 19000,
            'price_yearly' => 199000,
            'max_users' => 1,
            'max_products' => 2,
            'max_warehouses' => 1,
            'max_stores' => 1,
            'features' => ['inventory'],
            'is_active' => true,
        ]);

        // Create tenant on this plan
        $this->tenant = Tenant::create([
            'name' => 'Acme Test Corp',
            'slug' => 'acme-test',
            'plan_id' => $this->plan->id,
            'is_active' => true,
        ]);

        // Set the active tenant context
        app(TenantContext::class)->set($this->tenant);
    }

    public function test_can_create_resources_within_plan_limit(): void
    {
        // First product creation should succeed
        $product1 = Product::create([
            'name' => 'Product 1',
            'code' => 'PROD-1',
            'price' => 10000,
            'stock' => 10,
        ]);

        // Second product creation should succeed
        $product2 = Product::create([
            'name' => 'Product 2',
            'code' => 'PROD-2',
            'price' => 20000,
            'stock' => 5,
        ]);

        $this->assertDatabaseCount('products', 2);
    }

    public function test_exceeding_product_limit_throws_exception(): void
    {
        // Add 2 products to reach the limit
        Product::create(['name' => 'Product 1', 'code' => 'PROD-1', 'price' => 10000]);
        Product::create(['name' => 'Product 2', 'code' => 'PROD-2', 'price' => 20000]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Batas kuota rencana langganan Anda untuk products telah tercapai');

        // Third product creation should fail
        Product::create(['name' => 'Product 3', 'code' => 'PROD-3', 'price' => 30000]);
    }

    public function test_exceeding_warehouse_limit_throws_exception(): void
    {
        // Add 1 warehouse to reach the limit
        Warehouse::create(['warehouse_name' => 'Main Warehouse', 'warehouse_code' => 'WH-MAIN']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Batas kuota rencana langganan Anda untuk warehouses telah tercapai');

        // Second warehouse creation should fail
        Warehouse::create(['warehouse_name' => 'Branch Warehouse', 'warehouse_code' => 'WH-BRANCH']);
    }
}
