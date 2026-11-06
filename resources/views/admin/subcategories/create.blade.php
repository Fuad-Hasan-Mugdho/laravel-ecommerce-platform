@extends('layouts.admin')
@section('title', 'New Subcategory')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.subcategories.index') }}">Subcategories</a></li>
    <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
<div class="card col-lg-7">
    <div class="card-body">
        <form action="{{ route('admin.subcategories.store') }}" method="POST">
            @csrf
            @include('admin.subcategories._form')
        </form>
    </div>
</div>
@endsection
