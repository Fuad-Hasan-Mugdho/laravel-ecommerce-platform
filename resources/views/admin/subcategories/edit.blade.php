@extends('layouts.admin')
@section('title', 'Edit Subcategory')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.subcategories.index') }}">Subcategories</a></li>
    <li class="breadcrumb-item active">{{ $subcategory->name }}</li>
@endsection

@section('content')
<div class="card col-lg-7">
    <div class="card-body">
        @if ($subcategory->products()->exists())
            <div class="alert alert-info small"><i class="bi bi-info-circle"></i> Changing the parent category will also move this subcategory's products to the new category.</div>
        @endif
        <form action="{{ route('admin.subcategories.update', $subcategory) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.subcategories._form')
        </form>
    </div>
</div>
@endsection
