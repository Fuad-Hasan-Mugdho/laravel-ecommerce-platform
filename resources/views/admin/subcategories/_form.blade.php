<div class="mb-3">
    <label for="category_id" class="form-label">Parent Category <span class="text-danger">*</span></label>
    <select id="category_id" name="category_id" required class="form-select @error('category_id') is-invalid @enderror">
        <option value="">— Select a category —</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $subcategory->category_id) == $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if ($categories->isEmpty())
        <div class="form-text text-danger">No categories exist yet. <a href="{{ route('admin.categories.create') }}">Create a category first.</a></div>
    @endif
</div>

<div class="mb-3">
    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
    <input type="text" id="name" name="name" value="{{ old('name', $subcategory->name) }}" required maxlength="100"
           class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Smartphones">
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if ($subcategory->exists)
        <div class="form-text">Current URL slug: <code>{{ $subcategory->slug }}</code></div>
    @endif
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea id="description" name="description" rows="4" maxlength="2000"
              class="form-control @error('description') is-invalid @enderror">{{ old('description', $subcategory->description) }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="form-check form-switch mb-4">
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
           @checked(old('is_active', $subcategory->is_active))>
    <label class="form-check-label" for="is_active">Active (visible on the storefront)</label>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary" @disabled($categories->isEmpty())><i class="bi bi-check-lg"></i> {{ $subcategory->exists ? 'Update Subcategory' : 'Create Subcategory' }}</button>
    <a href="{{ route('admin.subcategories.index') }}" class="btn btn-light">Cancel</a>
</div>
