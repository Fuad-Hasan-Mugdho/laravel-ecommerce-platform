<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Home') · {{ config('app.name') }}</title>
    <meta name="description" content="@yield('meta_description', 'Shop the latest products at '.config('app.name'))">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .product-card img { aspect-ratio: 1 / 1; object-fit: cover; }
        .product-card { transition: transform .15s, box-shadow .15s; }
        .product-card:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.1); }
        .section-title { border-left: 4px solid var(--bs-primary); padding-left: .75rem; }
        footer { background: #1f2937; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="{{ route('shop.index') }}"><i class="bi bi-bag-check-fill me-1"></i>{{ config('app.name') }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('shop.index') ? 'active' : '' }}" href="{{ route('shop.index') }}">All Products</a></li>
                @foreach ($navCategories as $navCategory)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">{{ $navCategory->name }}</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item fw-semibold" href="{{ route('shop.category', $navCategory->slug) }}">All {{ $navCategory->name }}</a></li>
                            @if ($navCategory->subcategories->isNotEmpty())
                                <li><hr class="dropdown-divider"></li>
                                @foreach ($navCategory->subcategories as $navSub)
                                    <li><a class="dropdown-item" href="{{ route('shop.subcategory', $navSub->slug) }}">{{ $navSub->name }}</a></li>
                                @endforeach
                            @endif
                        </ul>
                    </li>
                @endforeach
            </ul>
            <a class="btn btn-outline-light btn-sm" href="{{ route('admin.dashboard') }}"><i class="bi bi-gear"></i> {{ auth()->check() ? 'Admin Panel' : 'Admin Login' }}</a>
        </div>
    </div>
</nav>

<main class="container py-4 flex-grow-1">
    @hasSection('breadcrumb')
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Home</a></li>
                @yield('breadcrumb')
            </ol>
        </nav>
    @endif
    @yield('content')
</main>

<footer class="text-white-50 py-4 mt-auto">
    <div class="container small d-flex justify-content-between flex-wrap gap-2">
        <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
        <span>Built with Laravel {{ app()->version() }}</span>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
