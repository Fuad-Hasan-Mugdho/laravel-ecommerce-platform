@extends('layouts.admin')
@section('title', $category->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
    <li class="breadcrumb-item active">{{ $category->name }}</li>
@endsection
@section('actions')
    <a href="{{ route('admin.subcategories.create', ['category' => $category->id]) }}" class="btn btn-outline-primary"><i class="bi bi-plus-lg"></i> Add Subcategory</a>
    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5">Slug</dt><dd class="col-7"><code>{{ $category->slug }}</code></dd>
                    <dt class="col-5">Status</dt><dd class="col-7"><x-status-badge :active="$category->is_active" /></dd>
                    <dt class="col-5">Products</dt><dd class="col-7">{{ $category->products_count }}</dd>
                    <dt class="col-5">Created</dt><dd class="col-7">{{ $category->created_at->format('d M Y') }}</dd>
                    <dt class="col-5">Public URL</dt><dd class="col-7"><a href="{{ route('shop.category', $category->slug) }}" target="_blank">Open <i class="bi bi-box-arrow-up-right"></i></a></dd>
                </dl>
                @if ($category->description)
                    <hr><p class="mb-0 text-muted">{{ $category->description }}</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Subcategories ({{ $category->subcategories->count() }})</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Name</th><th>Slug</th><th class="text-center">Products</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                    @forelse ($category->subcategories as $subcategory)
                        <tr>
                            <td><a href="{{ route('admin.subcategories.show', $subcategory) }}" class="text-decoration-none fw-semibold">{{ $subcategory->name }}</a></td>
                            <td><code>{{ $subcategory->slug }}</code></td>
                            <td class="text-center">{{ $subcategory->products_count }}</td>
                            <td><x-status-badge :active="$subcategory->is_active" /></td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <x-delete-button :action="route('admin.subcategories.destroy', $subcategory)"
                                    :confirm="'Delete \''.$subcategory->name.'\'? This will also delete '.$subcategory->products_count.' product(s).'" />
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No subcategories yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
