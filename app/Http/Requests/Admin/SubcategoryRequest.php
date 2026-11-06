<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubcategoryRequest extends FormRequest
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
        ]);
    }

    public function rules(): array
    {
        $subcategoryId = $this->route('subcategory')?->id;

        return [
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                // Unique per parent category only.
                Rule::unique('subcategories', 'name')
                    ->where('category_id', $this->input('category_id'))
                    ->ignore($subcategoryId),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Please select a parent category.',
            'category_id.exists' => 'The selected category does not exist.',
            'name.unique' => 'This subcategory already exists under the selected category.',
        ];
    }
}
