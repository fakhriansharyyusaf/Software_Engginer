<?php

namespace Tests\Feature;

use Tests\TestCase;

class DemoSeederCreatesProductsTest extends TestCase
{
    public function test_demo_seeder_creates_sample_products(): void
    {
        $this->artisan('migrate:fresh');
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'DemoSeeder']);

        // 5 products for seller1's store ("Toko Lari Cepat") + 2 products for
        // multi1's second store ("TechGear ID") = 7 total.
        $this->assertDatabaseCount('products', 7);
        $this->assertDatabaseHas('products', ['name' => 'Sepatu Lari Mekanik']);
    }
}
