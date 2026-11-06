<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductTest extends AdminTestCase
{
    private Category $category;

    private Subcategory $subcategory;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->category = Category::factory()->create();
        $this->subcategory = Subcategory::factory()->for($this->category)->create();
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'category_id' => $this->category->id,
            'subcategory_id' => $this->subcategory->id,
            'name' => 'Wireless Headphones',
            'description' => 'Great sound, long battery life.',
            'image' => UploadedFile::fake()->image('headphones.jpg', 400, 400),
            'old_price' => 5000,
            'new_price' => 3999.5,
            'is_active' => 1,
        ], $overrides);
    }

    public function test_product_can_be_created_with_image(): void
    {
        $this->post(route('admin.products.store'), $this->validPayload())
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('success');

        $product = Product::firstOrFail();
        $this->assertSame('wireless-headphones', $product->slug);
        $this->assertSame('3999.50', $product->new_price);
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_image_is_required_on_create_and_must_be_an_image(): void
    {
        $this->post(route('admin.products.store'), $this->validPayload(['image' => null]))
            ->assertSessionHasErrors('image');

        $this->post(route('admin.products.store'), $this->validPayload([
            'image' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
        ]))->assertSessionHasErrors('image');
    }

    public function test_subcategory_must_belong_to_selected_category(): void
    {
        $other = Subcategory::factory()->create(); // under a different category

        $this->post(route('admin.products.store'), $this->validPayload(['subcategory_id' => $other->id]))
            ->assertSessionHasErrors('subcategory_id');
    }

    public function test_old_price_must_not_be_lower_than_new_price(): void
    {
        $this->post(route('admin.products.store'), $this->validPayload(['old_price' => 100, 'new_price' => 200]))
            ->assertSessionHasErrors('old_price');

        $this->post(route('admin.products.store'), $this->validPayload(['old_price' => '', 'new_price' => 200]))
            ->assertSessionHasNoErrors();
    }

    public function test_required_fields_are_validated(): void
    {
        $this->post(route('admin.products.store'), [])
            ->assertSessionHasErrors(['category_id', 'subcategory_id', 'name', 'description', 'image', 'new_price']);
    }

    public function test_updating_with_new_image_replaces_old_file(): void
    {
        $product = Product::factory()->for($this->category)->for($this->subcategory)->create([
            'image' => UploadedFile::fake()->image('old.jpg')->store('products', 'public'),
        ]);
        $oldPath = $product->image;

        $this->put(route('admin.products.update', $product), $this->validPayload([
            'name' => 'Renamed Product',
            'image' => UploadedFile::fake()->image('new.jpg'),
        ]))->assertRedirect();

        $product->refresh();
        $this->assertSame('renamed-product', $product->slug);
        $this->assertNotSame($oldPath, $product->image);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_updating_without_image_keeps_existing_image(): void
    {
        $product = Product::factory()->for($this->category)->for($this->subcategory)->create([
            'image' => UploadedFile::fake()->image('keep.jpg')->store('products', 'public'),
        ]);

        $this->put(route('admin.products.update', $product), $this->validPayload(['image' => null]))
            ->assertSessionHasNoErrors();

        $this->assertSame($product->image, $product->fresh()->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_deleting_product_removes_image_file(): void
    {
        $product = Product::factory()->for($this->category)->for($this->subcategory)->create([
            'image' => UploadedFile::fake()->image('gone.jpg')->store('products', 'public'),
        ]);

        $this->delete(route('admin.products.destroy', $product))->assertRedirect();

        $this->assertModelMissing($product);
        Storage::disk('public')->assertMissing($product->image);
    }

    public function test_discount_helpers(): void
    {
        $product = Product::factory()->make(['old_price' => 200, 'new_price' => 150]);
        $this->assertTrue($product->has_discount);
        $this->assertSame(25, $product->discount_percent);

        $noDiscount = Product::factory()->make(['old_price' => null, 'new_price' => 150]);
        $this->assertFalse($noDiscount->has_discount);
        $this->assertNull($noDiscount->discount_percent);
    }
}
