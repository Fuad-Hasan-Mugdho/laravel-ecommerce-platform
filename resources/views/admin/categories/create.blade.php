@extends('layouts.admin')
@section('title', 'New Category')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
    <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
<div class="card col-lg-7">
    <div class="card-body">
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            @include('admin.categories._form')
        </form>
    </div>
</div>
@endsection
