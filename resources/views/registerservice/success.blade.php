<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Registered</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/registerservice.css') }}">
</head>
<body>
    <h1>Service Hours Registered Successfully</h1>

    <div>
        <a href="{{ route('registerservice') }}">
            <button>Register Another Service</button>
        </a>
        <a href="{{ route('servicehours') }}">
            <button>Go to start page</button>
        </a>
    </div>
</body>

@include('layouts.footer')

</html>
