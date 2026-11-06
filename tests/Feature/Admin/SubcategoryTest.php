<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;

class SubcategoryTest extends AdminTestCase
{
    public function test_subcategory_can_be_created_under_a_category(): void
    {
        $category = Category::factory()->create();

        $this->post(route('admin.subcategories.store'), [
            'category_id' => $category->id,
            'name' => 'Smartphones',
            'is_active' => 1,
        ])->assertRedirect(route('admin.subcategories.index'));

        $this->assertDatabaseHas('subcategories', [
            'category_id' => $category->id,
            'name' => 'Smartphones',
            'slug' => 'smartphones',
        ]);
    }

    public function test_requires_an_existing_parent_category(): void
    {
        $this->post(route('admin.subcategories.store'), ['category_id' => 999, 'name' => 'Orphan'])
            ->assertSessionHasErrors('category_id');

        $this->post(route('admin.subcategories.store'), ['name' => 'Orphan'])
            ->assertSessionHasErrors('category_id');
    }

    public function test_name_must_be_unique_per_category_only(): void
    {
        [$a, $b] = Category::factory()->count(2)->create();
        Subcategory::factory()->for($a)->create(['name' => 'Accessories']);

        $this->post(route('admin.subcategories.store'), ['category_id' => $a->id, 'name' => 'Accessories'])
            ->assertSessionHasErrors('name');

        $this->post(route('admin.subcategories.store'), ['category_id' => $b->id, 'name' => 'Accessories'])
            ->assertSessionHasNoErrors();
    }

    public function test_moving_subcategory_to_another_category_moves_its_products(): void
    {
        [$a, $b] = Category::factory()->count(2)->create();
        $subcategory = Subcategory::factory()->for($a)->create();
        $product = Product::factory()->for($a)->for($subcategory)->create();

        $this->put(route('admin.subcategories.update', $subcategory), [
            'category_id' => $b->id,
            'name' => $subcategory->name,
            'is_active' => 1,
        ])->assertRedirect();

        $this->assertSame($b->id, $product->fresh()->category_id);
    }

    public function test_deleting_subcategory_deletes_its_products(): void
    {
        $subcategory = Subcategory::factory()->create();
        $product = Product::factory()->for($subcategory)->for($subcategory->category)->create();

        $this->delete(route('admin.subcategories.destroy', $subcategory))->assertRedirect();

        $this->assertModelMissing($subcategory);
        $this->assertModelMissing($product);
    }

    public function test_json_endpoint_returns_subcategories_of_category(): void
    {
        $category = Category::factory()->create();
        Subcategory::factory()->for($category)->create(['name' => 'Tablets']);
        Subcategory::factory()->create(); // other category

        $this->getJson(route('admin.categories.subcategories', $category))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Tablets']);
    }
}
