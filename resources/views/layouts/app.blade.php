<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Service hours')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>

    <header>
        <!-- maybe your nav here -->
    </header>

    <main>
        @yield('content')
    </main>

    @include('layouts.footer')
    <!-- Bootstrap Bundle JS (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>



<!-- resources/views/account.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>My Account</title>
</head>
<body>
    <h1>Welcome, {{ Auth::user()->name }}</h1>
    <p>Role: {{ Auth::user()->role }}</p>

    <a href="{{ route('servicehours') }}">Go to Menu</a>
</body>
</html>