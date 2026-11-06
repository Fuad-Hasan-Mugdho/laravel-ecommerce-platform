<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;
    use HasSlug;

    public const IMAGE_DIR = 'products';

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'name',
        'slug',
        'description',
        'image',
        'old_price',
        'new_price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'old_price' => 'decimal:2',
            'new_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::deleted(function (Product $product) {
            $product->deleteImageFile();
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Public URL of the product image, or a placeholder when none is set. */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->image
            ? Storage::disk('public')->url($this->image)
            : asset('images/placeholder.svg'));
    }

    /** True when old_price is set and higher than new_price. */
    protected function hasDiscount(): Attribute
    {
        return Attribute::get(fn () => $this->old_price !== null && (float) $this->old_price > (float) $this->new_price);
    }

    /** Discount percentage (rounded), or null when there is no discount. */
    protected function discountPercent(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->has_discount) {
                return null;
            }

            return (int) round((1 - ((float) $this->new_price / (float) $this->old_price)) * 100);
        });
    }

    public function deleteImageFile(): void
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            Storage::disk('public')->delete($this->image);
        }
    }
}
