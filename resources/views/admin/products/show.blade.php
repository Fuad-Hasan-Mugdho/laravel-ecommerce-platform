@extends('layouts.admin')
@section('title', $product->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active">{{ $product->name }}</li>
@endsection
@section('actions')
    <a href="{{ route('shop.product', $product->slug) }}" target="_blank" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-up-right"></i> View on store</a>
    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
    <x-delete-button :action="route('admin.products.destroy', $product)" :confirm="'Delete product \''.$product->name.'\'?'" size="md" label="Delete" />
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-4">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="img-fluid rounded border w-100" style="aspect-ratio:1/1;object-fit:cover">
            </div>
            <div class="col-md-8">
                <dl class="row">
                    <dt class="col-sm-3">Category</dt><dd class="col-sm-9"><a href="{{ route('admin.categories.show', $product->category) }}">{{ $product->category->name }}</a></dd>
                    <dt class="col-sm-3">Subcategory</dt><dd class="col-sm-9"><a href="{{ route('admin.subcategories.show', $product->subcategory) }}">{{ $product->subcategory->name }}</a></dd>
                    <dt class="col-sm-3">Slug</dt><dd class="col-sm-9"><code>{{ $product->slug }}</code></dd>
                    <dt class="col-sm-3">Price</dt><dd class="col-sm-9"><x-price :product="$product" size="fs-5" /></dd>
                    <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><x-status-badge :active="$product->is_active" /></dd>
                    <dt class="col-sm-3">Created</dt><dd class="col-sm-9">{{ $product->created_at->format('d M Y, h:i A') }}</dd>
                    <dt class="col-sm-3">Updated</dt><dd class="col-sm-9">{{ $product->updated_at->format('d M Y, h:i A') }}</dd>
                </dl>
                <h6>Description</h6>
                <p class="text-muted" style="white-space: pre-line">{{ $product->description }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
