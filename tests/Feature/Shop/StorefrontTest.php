<?php

namespace Tests\Feature\Shop;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_groups_products_by_subcategory(): void
    {
        $category = Category::factory()->create(['name' => 'Electronics']);
        $phones = Subcategory::factory()->for($category)->create(['name' => 'Smartphones']);
        $laptops = Subcategory::factory()->for($category)->create(['name' => 'Laptops']);
        Product::factory()->for($category)->for($phones)->create(['name' => 'Phone One']);
        Product::factory()->for($category)->for($laptops)->create(['name' => 'Laptop One']);

        $this->get(route('shop.index'))
            ->assertOk()
            ->assertSeeInOrder(['Electronics', 'Laptops', 'Laptop One', 'Smartphones', 'Phone One']);
    }

    public function test_inactive_products_and_empty_subcategories_are_hidden(): void
    {
        $category = Category::factory()->create();
        $visible = Subcategory::factory()->for($category)->create(['name' => 'Visible Sub']);
        $empty = Subcategory::factory()->for($category)->create(['name' => 'Empty Sub']);
        Product::factory()->for($category)->for($visible)->create(['name' => 'Shown Product']);
        Product::factory()->for($category)->for($visible)->inactive()->create(['name' => 'Hidden Product']);

        $this->get(route('shop.index'))
            ->assertSee('Shown Product')
            ->assertDontSee('Hidden Product')
            ->assertDontSee('Empty Sub');
    }

    public function test_product_detail_page_uses_slug_url(): void
    {
        $product = Product::factory()->create(['name' => 'Super Blender']);

        $this->get('/products/super-blender')
            ->assertOk()
            ->assertSee('Super Blender')
            ->assertSee($product->subcategory->name);
    }

    public function test_inactive_or_unknown_product_returns_404(): void
    {
        Product::factory()->inactive()->create(['name' => 'Secret Item']);

        $this->get('/products/secret-item')->assertNotFound();
        $this->get('/products/does-not-exist')->assertNotFound();
    }

    public function test_category_and_subcategory_pages(): void
    {
        $category = Category::factory()->create(['name' => 'Fashion']);
        $subcategory = Subcategory::factory()->for($category)->create(['name' => 'Footwear']);
        Product::factory()->for($category)->for($subcategory)->create(['name' => 'Running Shoes']);

        $this->get(route('shop.category', $category->slug))->assertOk()->assertSee('Running Shoes');
        $this->get(route('shop.subcategory', $subcategory->slug))->assertOk()->assertSee('Running Shoes');
    }
}
