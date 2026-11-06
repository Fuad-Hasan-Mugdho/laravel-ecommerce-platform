<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login · {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>body { background: #1f2937; }</style>
</head>
<body class="d-flex align-items-center min-vh-100">
<div class="container" style="max-width: 420px">
    <div class="text-center text-white mb-4">
        <i class="bi bi-bag-check-fill fs-1"></i>
        <h1 class="h4 mt-2">{{ config('app.name') }} Admin</h1>
    </div>

    <div class="card shadow border-0">
        <div class="card-body p-4">
            <x-flash />

            <form method="POST" action="{{ route('admin.login.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="form-control @error('email') is-invalid @enderror">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1" @checked(old('remember'))>
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right"></i> Sign in</button>
            </form>
        </div>
    </div>

    <p class="text-center text-white-50 small mt-3">
        <a href="{{ route('shop.index') }}" class="text-white-50">&larr; Back to storefront</a>
    </p>
</div>
</body>
</html>
