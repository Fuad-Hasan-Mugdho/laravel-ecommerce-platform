@extends('layouts.admin')
@section('title', 'Categories')
@section('breadcrumb')<li class="breadcrumb-item active">Categories</li>@endsection
@section('actions')
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Category</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <form method="GET" class="row g-2">
            <div class="col-sm-6 col-md-4">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search categories…">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-secondary">Search</button>
                @if (request('q'))<a href="{{ route('admin.categories.index') }}" class="btn btn-link">Clear</a>@endif
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Name</th><th>Slug</th><th class="text-center">Subcategories</th><th class="text-center">Products</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td><a href="{{ route('admin.categories.show', $category) }}" class="fw-semibold text-decoration-none">{{ $category->name }}</a></td>
                    <td><code>{{ $category->slug }}</code></td>
                    <td class="text-center">{{ $category->subcategories_count }}</td>
                    <td class="text-center">{{ $category->products_count }}</td>
                    <td><x-status-badge :active="$category->is_active" /></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <x-delete-button :action="route('admin.categories.destroy', $category)"
                            :confirm="'Delete \''.$category->name.'\'? This will also delete '.$category->subcategories_count.' subcategor(ies) and '.$category->products_count.' product(s).'" />
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No categories found. <a href="{{ route('admin.categories.create') }}">Create the first one.</a></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($categories->hasPages())
        <div class="card-footer bg-white">{{ $categories->links() }}</div>
    @endif
</div>
@endsection
