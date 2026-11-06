<div class="mb-3">
    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
    <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required maxlength="100"
           class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Electronics">
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if ($category->exists)
        <div class="form-text">Current URL slug: <code>{{ $category->slug }}</code> (regenerated automatically if the name changes)</div>
    @endif
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea id="description" name="description" rows="4" maxlength="2000"
              class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="form-check form-switch mb-4">
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
           @checked(old('is_active', $category->is_active))>
    <label class="form-check-label" for="is_active">Active (visible on the storefront)</label>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ $category->exists ? 'Update Category' : 'Create Category' }}</button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-light">Cancel</a>
</div>
