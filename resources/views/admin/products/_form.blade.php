@php
    $selectedCategory = old('category_id', $product->category_id);
    $selectedSubcategory = old('subcategory_id', $product->subcategory_id);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
        <select id="category_id" name="category_id" required class="form-select @error('category_id') is-invalid @enderror">
            <option value="">— Select a category —</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected($selectedCategory == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="subcategory_id" class="form-label">Subcategory <span class="text-danger">*</span></label>
        <select id="subcategory_id" name="subcategory_id" required class="form-select @error('subcategory_id') is-invalid @enderror"
                data-url-template="{{ route('admin.categories.subcategories', ['category' => '__ID__']) }}"
                data-selected="{{ $selectedSubcategory }}">
            <option value="">{{ $selectedCategory ? '— Select a subcategory —' : '— Select a category first —' }}</option>
            @foreach ($categories as $category)
                @foreach ($category->subcategories as $subcategory)
                    <option value="{{ $subcategory->id }}" data-category="{{ $category->id }}"
                            @selected($selectedSubcategory == $subcategory->id)
                            @if ($selectedCategory != $category->id) hidden disabled @endif>{{ $subcategory->name }}</option>
                @endforeach
            @endforeach
        </select>
        @error('subcategory_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text" id="subcategory-help"></div>
    </div>

    <div class="col-12">
        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="150"
               class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Wireless Headphones">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($product->exists)
            <div class="form-text">Current URL slug: <code>{{ $product->slug }}</code> (regenerated automatically if the name changes)</div>
        @endif
    </div>

    <div class="col-12">
        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
        <textarea id="description" name="description" rows="5" required maxlength="5000"
                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="old_price" class="form-label">Old Price (৳)</label>
        <input type="number" id="old_price" name="old_price" value="{{ old('old_price', $product->old_price) }}" step="0.01" min="0"
               class="form-control @error('old_price') is-invalid @enderror" placeholder="Optional – shown struck through">
        @error('old_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="new_price" class="form-label">New Price (৳) <span class="text-danger">*</span></label>
        <input type="number" id="new_price" name="new_price" value="{{ old('new_price', $product->new_price) }}" step="0.01" min="0" required
               class="form-control @error('new_price') is-invalid @enderror">
        @error('new_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="image" class="form-label">Product Image @unless ($product->exists)<span class="text-danger">*</span>@endunless</label>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
               class="form-control @error('image') is-invalid @enderror" @required(! $product->exists)>
        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">JPG, PNG or WEBP, max 2 MB.{{ $product->exists ? ' Leave empty to keep the current image.' : '' }}</div>
    </div>

    <div class="col-md-6">
        <label class="form-label d-block">Preview</label>
        <img id="image-preview" src="{{ $product->image_url }}" alt="" class="rounded border" style="width:120px;height:120px;object-fit:cover">
    </div>

    <div class="col-12">
        <div class="form-check form-switch">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                   @checked(old('is_active', $product->is_active))>
            <label class="form-check-label" for="is_active">Active (visible on the storefront)</label>
        </div>
    </div>

    <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ $product->exists ? 'Update Product' : 'Create Product' }}</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-light">Cancel</a>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const categorySelect = document.getElementById('category_id');
    const subSelect = document.getElementById('subcategory_id');
    const help = document.getElementById('subcategory-help');
    const urlTemplate = subSelect.dataset.urlTemplate;

    function render(subcategories, selected) {
        subSelect.innerHTML = '';
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = subcategories.length ? '— Select a subcategory —' : 'No subcategories in this category';
        subSelect.appendChild(placeholder);
        subcategories.forEach(function (s) {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.name;
            if (String(s.id) === String(selected)) opt.selected = true;
            subSelect.appendChild(opt);
        });
        help.textContent = subcategories.length ? '' : 'Create a subcategory for this category first.';
    }

    categorySelect.addEventListener('change', async function () {
        const id = this.value;
        if (!id) {
            render([], null);
            subSelect.firstChild.textContent = '— Select a category first —';
            return;
        }
        subSelect.disabled = true;
        try {
            const response = await fetch(urlTemplate.replace('__ID__', id), { headers: { 'Accept': 'application/json' } });
            render(await response.json(), null);
        } catch (e) {
            help.textContent = 'Could not load subcategories. Please reload the page.';
        } finally {
            subSelect.disabled = false;
        }
    });

    // Live image preview
    const imageInput = document.getElementById('image');
    const preview = document.getElementById('image-preview');
    imageInput.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (file) preview.src = URL.createObjectURL(file);
    });
})();
</script>
@endpush
