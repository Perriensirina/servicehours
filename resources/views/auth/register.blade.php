<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>
    <div class="main-container">
        <a href="{{ url('/servicehours') }}" class="back-arrow">&#8592;</a>
        <div class="welcome-card">
            <div class="grid-container">
                <img src="{{ asset('images/IDlogo.png') }}" alt="Logo">
                <div class="title">
                    <h2>Register User</h2>
                    <p>Create your account</p>
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger py-2">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Register Form -->
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <input 
                        id="name" 
                        name="name" 
                        type="text" 
                        class="form-control @error('name') is-invalid @enderror" 
                        placeholder="Full Name" 
                        value="{{ old('name') }}" 
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        placeholder="E-mail Address" 
                        value="{{ old('email') }}" 
                        required
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        placeholder="Password" 
                        required
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                    <input 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        type="password" 
                        class="form-control" 
                        placeholder="Confirm Password" 
                        required
                    >
                </div>

                <!-- Role -->
                <div class="mb-3">
                    <select 
                        id="role" 
                        name="role" 
                        class="form-select @error('role') is-invalid @enderror" 
                        required
                    >
                        <option disabled selected>Select Account Type</option>
                        <option value="operator" {{ old('role') === 'operator' ? 'selected' : '' }}>Operator</option>
                        <option value="teamleader" {{ old('role') === 'teamleader' ? 'selected' : '' }}>Teamleader</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">Register</button>
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
