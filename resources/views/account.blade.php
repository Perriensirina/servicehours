
<!DOCTYPE html>
<html>
<head>
    <title>My Account</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    
</head>

<body class="bg-light">
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg p-4 rounded-4" style="width: 400px;">
            <h1 class="text-center mb-4">Welcome, {{ Auth::user()->name }}</h1>
            <p>Role: {{ Auth::user()->role }}</p>

            <a class="btn btn-primary" href="{{ route('servicehours') }}">Go to Menu</a>
        </div>
    </div>
    
</body>

</html>



