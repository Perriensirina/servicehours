<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Hours Menu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS (via CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>

<body>
    <!-- Main Content -->
    <div class="main-container">
        <a href="{{ url('/login') }}" class="back-arrow">&#8592;</a>
        <div class="position-absolute top-0 end-0 p-3">
            <a href="{{ url('/account') }}" class="bi bi-person fs-3 text-white"></a>
        </div>
        <div class="welcome-card">
            <div class="grid-container">
                <div class="title">
                    <h2>Service Hours Menu</h2>
                    <p>Select an option</p>
                </div>
            </div>

            <div class="d-grid gap-3">

                @php
                    $user = auth()->user();
                @endphp

                @if($user && ($user->isAdmin() || $user->isTeamleader()))
                    <a href="/registerservice" class="btn btn-primary btn-lg w-100">Register Task</a>
                @endif

                <a href="{{ route('registerservice.overview') }}" class="btn btn-outline-light btn-lg w-100">Overview Task</a>

                @if($user && $user->isAdmin())
                    <a class="btn btn-outline-light btn-lg w-100" href="{{ route('departments.index') }}">Settings</a>
                @endif

                @if($user && ($user->isAdmin() || $user->isTeamleader()))
                    <a class="btn btn-outline-light btn-lg w-100" href="{{ route('activity.logs') }}">Activity Logs</a>
                @endif

                @if($user && ($user->isAdmin() || $user->isTeamleader()))
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">Register new user</a>
                @endif
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
