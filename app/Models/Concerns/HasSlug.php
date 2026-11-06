<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Generates a unique, SEO-friendly slug from the model's `name` attribute.
 *
 * - On create: slug is always generated from the name.
 * - On update: slug is regenerated only when the name changes, so existing
 *   URLs stay stable unless the product/category is actually renamed.
 * - Collisions are resolved with a numeric suffix (e.g. "t-shirt-2").
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function (Model $model) {
            $model->slug = $model->generateUniqueSlug($model->name);
        });

        static::updating(function (Model $model) {
            if ($model->isDirty('name') && ! $model->isDirty('slug')) {
                $model->slug = $model->generateUniqueSlug($model->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function generateUniqueSlug(string $source): string
    {
        $base = Str::slug($source) ?: Str::lower(Str::random(8));
        $slug = $base;
        $counter = 2;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($this->exists, fn ($q) => $q->whereKeyNot($this->getKey()))
                ->exists()
        ) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
