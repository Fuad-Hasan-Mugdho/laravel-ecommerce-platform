@extends('layouts.admin')
@section('title', 'New Product')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
<div class="card col-lg-9">
    <div class="card-body">
        @if ($categories->isEmpty())
            <div class="alert alert-warning">You need at least one category and subcategory before adding products. <a href="{{ route('admin.categories.create') }}">Create a category.</a></div>
        @endif
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.products._form')
        </form>
    </div>
</div>
@endsection
