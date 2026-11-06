@extends('layouts.shop')
@section('title', $category->name)
@section('meta_description', Str::limit($category->description ?: 'Shop '.$category->name.' products at '.config('app.name'), 155))
@section('breadcrumb')<li class="breadcrumb-item active">{{ $category->name }}</li>@endsection

@section('content')
<h1 class="h3 mb-1">{{ $category->name }}</h1>
@if ($category->description)<p class="text-muted">{{ $category->description }}</p>@endif
<hr>

@forelse ($category->subcategories as $subcategory)
    @include('shop.partials.subcategory-section', ['subcategory' => $subcategory])
@empty
    <p class="text-muted py-5 text-center">No products in this category yet.</p>
@endforelse
@endsection
