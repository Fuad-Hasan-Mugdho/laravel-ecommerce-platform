@extends('layouts.admin')
@section('title', 'Edit Category')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
    <li class="breadcrumb-item active">{{ $category->name }}</li>
@endsection

@section('content')
<div class="card col-lg-7">
    <div class="card-body">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.categories._form')
        </form>
    </div>
</div>
@endsection
