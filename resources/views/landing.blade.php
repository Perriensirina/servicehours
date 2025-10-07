<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>
    <div class="main-container">
        <div class="welcome-card">
            <div class="grid-container">
                <img src="{{ asset('images/IDlogo.png') }}" alt="Logo">
                <div class="title">
                    <h2>Welcome</h2>
                    <p>Service hours</p>
                </div>
            </div>

            <!-- Responsive buttons -->
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100">Log In</a>
                </div>
                <div class="col-12 col-md-6">
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg w-100">Register</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
