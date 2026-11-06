<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }} Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f5f6fa; }
        .sidebar { min-height: 100vh; background: #1f2937; }
        .sidebar .nav-link { color: #cbd5e1; border-radius: .5rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #374151; color: #fff; }
        .sidebar .nav-link i { width: 1.5rem; }
        .thumb { width: 56px; height: 56px; object-fit: cover; border-radius: .5rem; }
        .card { border: 0; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 sidebar p-3 d-md-block collapse" id="sidebar">
            <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none d-flex align-items-center mb-4 fs-5 fw-semibold">
                <i class="bi bi-bag-check-fill me-2"></i> {{ config('app.name') }}
            </a>
            <ul class="nav nav-pills flex-column gap-1">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}"><i class="bi bi-folder"></i> Categories</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}" href="{{ route('admin.subcategories.index') }}"><i class="bi bi-folder2-open"></i> Subcategories</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}"><i class="bi bi-box-seam"></i> Products</a></li>
                <li class="nav-item mt-3"><a class="nav-link" href="{{ route('shop.index') }}" target="_blank"><i class="bi bi-shop"></i> View Storefront</a></li>
            </ul>

            <hr class="border-secondary">
            <div class="d-flex align-items-center justify-content-between text-white-50 small px-2">
                <span class="text-truncate" title="{{ auth()->user()->email }}"><i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link btn-sm text-white-50 p-0" title="Log out"><i class="bi bi-box-arrow-right"></i></button>
                </form>
            </div>
        </nav>

        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <button class="btn btn-outline-secondary d-md-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar">
                <i class="bi bi-list"></i> Menu
            </button>

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h1 class="h3 mb-0">@yield('title')</h1>
                    @hasSection('breadcrumb')
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 small">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                                @yield('breadcrumb')
                            </ol>
                        </nav>
                    @endif
                </div>
                <div>@yield('actions')</div>
            </div>

            <x-flash />

            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
