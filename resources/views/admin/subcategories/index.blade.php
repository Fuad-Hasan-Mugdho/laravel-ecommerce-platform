@extends('layouts.admin')
@section('title', 'Subcategories')
@section('breadcrumb')<li class="breadcrumb-item active">Subcategories</li>@endsection
@section('actions')
    <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Subcategory</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <form method="GET" class="row g-2">
            <div class="col-sm-5 col-md-4">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search subcategories…">
            </div>
            <div class="col-sm-4 col-md-3">
                <select name="category" class="form-select">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-secondary">Filter</button>
                @if (request()->hasAny(['q', 'category']))<a href="{{ route('admin.subcategories.index') }}" class="btn btn-link">Clear</a>@endif
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Name</th><th>Category</th><th>Slug</th><th class="text-center">Products</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
            @forelse ($subcategories as $subcategory)
                <tr>
                    <td><a href="{{ route('admin.subcategories.show', $subcategory) }}" class="fw-semibold text-decoration-none">{{ $subcategory->name }}</a></td>
                    <td><a href="{{ route('admin.categories.show', $subcategory->category) }}" class="text-decoration-none">{{ $subcategory->category->name }}</a></td>
                    <td><code>{{ $subcategory->slug }}</code></td>
                    <td class="text-center">{{ $subcategory->products_count }}</td>
                    <td><x-status-badge :active="$subcategory->is_active" /></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.subcategories.show', $subcategory) }}" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <x-delete-button :action="route('admin.subcategories.destroy', $subcategory)"
                            :confirm="'Delete \''.$subcategory->name.'\'? This will also delete '.$subcategory->products_count.' product(s).'" />
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No subcategories found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($subcategories->hasPages())
        <div class="card-footer bg-white">{{ $subcategories->links() }}</div>
    @endif
</div>
@endsection
