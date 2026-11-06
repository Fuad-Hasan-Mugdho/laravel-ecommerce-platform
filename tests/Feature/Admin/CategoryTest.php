<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CategoryTest extends AdminTestCase
{
    public function test_index_lists_categories(): void
    {
        Category::factory()->create(['name' => 'Electronics']);

        $this->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('Electronics');
    }

    public function test_category_can_be_created_with_generated_slug(): void
    {
        $this->post(route('admin.categories.store'), [
            'name' => 'Home & Kitchen',
            'description' => 'Things for home',
            'is_active' => 1,
        ])->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Home & Kitchen', 'slug' => 'home-kitchen']);
    }

    public function test_validation_rejects_missing_and_duplicate_names(): void
    {
        Category::factory()->create(['name' => 'Fashion']);

        $this->from(route('admin.categories.create'))
            ->post(route('admin.categories.store'), ['name' => ''])
            ->assertRedirect(route('admin.categories.create'))
            ->assertSessionHasErrors('name');

        $this->post(route('admin.categories.store'), ['name' => 'Fashion'])
            ->assertSessionHasErrors('name');
    }

    public function test_slug_is_unique_when_names_collide_after_slugging(): void
    {
        Category::create(['name' => 'Men Shoes']);
        $second = Category::create(['name' => 'Men-Shoes']);

        $this->assertSame('men-shoes-2', $second->slug);
    }

    public function test_category_can_be_updated_and_slug_follows_name(): void
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $this->put(route('admin.categories.update', $category), [
            'name' => 'New Name',
            'is_active' => 0,
        ])->assertRedirect();

        $category->refresh();
        $this->assertSame('New Name', $category->name);
        $this->assertSame('new-name', $category->slug);
        $this->assertFalse($category->is_active);
    }

    public function test_deleting_category_cascades_to_subcategories_products_and_images(): void
    {
        Storage::fake('public');
        $path = UploadedFile::fake()->image('p.jpg')->store('products', 'public');

        $category = Category::factory()->create();
        $subcategory = Subcategory::factory()->for($category)->create();
        $product = Product::factory()->for($category)->for($subcategory)->create(['image' => $path]);

        $this->delete(route('admin.categories.destroy', $category))->assertRedirect();

        $this->assertModelMissing($category);
        $this->assertModelMissing($subcategory);
        $this->assertModelMissing($product);
        Storage::disk('public')->assertMissing($path);
    }
}
