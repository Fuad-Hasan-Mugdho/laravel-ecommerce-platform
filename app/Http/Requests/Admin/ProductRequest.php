<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'is_active' => $this->boolean('is_active'),
            // Treat an empty old price as "no old price" rather than 0.
            'old_price' => $this->filled('old_price') ? $this->input('old_price') : null,
        ]);
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'subcategory_id' => [
                'required',
                'integer',
                // The subcategory must belong to the chosen category.
                Rule::exists('subcategories', 'id')->where('category_id', $this->input('category_id')),
            ],
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'image' => [
                $isUpdate ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'old_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99', 'gte:new_price'],
            'new_price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Please select a category.',
            'subcategory_id.required' => 'Please select a subcategory.',
            'subcategory_id.exists' => 'The selected subcategory does not belong to the selected category.',
            'image.required' => 'Please upload a product image.',
            'image.max' => 'The image may not be larger than 2 MB.',
            'old_price.gte' => 'The old price must be greater than or equal to the new price.',
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'subcategory_id' => 'subcategory',
            'new_price' => 'new price',
            'old_price' => 'old price',
        ];
    }
}
