<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ID Logistics - Register Service Hours</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <script src="{{ asset('js/registerservice.js') }}" defer></script>
</head>

<body>
    <div class="main-container">
        <a href="{{ url('/servicehours') }}" class="back-arrow">&#8592;</a>
        <div class="position-absolute top-0 end-0 p-3">
            <a href="{{ url('/account') }}" class="bi bi-person fs-3 text-white"></a>
            <a href="{{ url('/servicehours') }}" class="bi bi-house fs-3 text-white"></a>
        </div>
        
         <div class="welcome-card">
            <div class="grid-container">
                <!-- <img src="{{ asset('images/IDlogo.png') }}" alt="Logo"> -->
                <div class="title">
                    <h2>Register Service Hours</h2>
                    <p>Fill in the details below</p>
                </div>
            </div>

            <!-- Success message -->
            @if(session('success'))
                <div class="popup-alert">
                    <div class="icon">✓</div>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('registerservice.store') }}">
                @csrf

                <!-- Department -->
                <div class="mb-3">
                    <select id="department" name="department" class="form-select" required>
                        <option value="">-- Select Department --</option>
                        @foreach(App\Models\Department::all() as $department)
                            <option value="{{ $department->name }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                @php
                    $user = auth()->user();
                @endphp

                @if(in_array($user->role, ['teamleader', 'admin']))
                    <!-- Reason -->
                    <div class="mb-3">
                        <select id="reason" name="reason" class="form-select" required>
                            <option value="">-- Select reason --</option>
                            @foreach(App\Models\Reason::all() as $reason)
                                <option value="{{ $reason->reason }}">{{ $reason->reason }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Assign Users -->
                    <div class="mb-3">
                        <select name="assigned_users[]" id="assigned_users" class="form-select" multiple>
                            @foreach(App\Models\User::where('role', 'operator')->get() as $operator)
                                <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Hold Ctrl (Windows) or Command (Mac) to select multiple users.</div>
                    </div>
                @endif

                <!-- Conditional Fields -->
                <div id="shipmentField" class="mb-3" style="display: none;">
                    <input type="text" id="shipment" name="shipment" class="form-control" placeholder="Shipment ID">
                </div>

                <div id="boxField" class="mb-3" style="display: none;">
                    <input type="text" id="box_number" name="box_number" class="form-control" placeholder="Box Number">
                </div>

                <div id="ul" class="mb-3" style="display: none;">
                    <input type="text" id="ul" name="ul" class="form-control" placeholder="U.L.">
                </div>

                <div id="supplier" class="mb-3" style="display: none;">
                    <select id="supplier" name="supplier" class="form-select">
                        <option value="">-- Select Supplier --</option>
                        @foreach(App\Models\Supplier::all() as $supplier)
                            <option value="{{ $supplier->name }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="ATField" class="mb-3" style="display: none;">
                    <input type="text" id="AT_number" name="AT_number" class="form-control" placeholder="AT Number">
                </div>

                <!-- Zone -->
                <div class="mb-3">
                    <select id="zone" name="zone" class="form-select" required>
                        <option value="">-- Select Zone --</option>
                        @foreach(App\Models\Zone::all() as $zone)
                            <option value="{{ $zone->zoneName }}">{{ $zone->zoneName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <input type="text" id="extra_info" name="extra_info" class="form-control" placeholder="Enter additional info">
                </div>

                <!-- Submit Button -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Submit</button>
                </div>
            </form>
        </div>

    </div>
   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
