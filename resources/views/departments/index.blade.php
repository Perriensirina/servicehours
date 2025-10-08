<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <title>Settings</title>
</head>

<body>
    <div class="position-absolute top-0 end-0 p-3">
        <a href="{{ url('/servicehours') }}" class="bi bi-house fs-3 text-white"></a>
        <a href="{{ url('/account') }}" class="bi bi-person fs-3 text-white"></a>
    </div>

    <div class="container py-5">
        <a href="{{ route('servicehours') }}" class="back-arrow">&#8592;</a>

        <div class="container-fluid">
            <h2 class="settings-heading middle">Manage data</h2>

            <!-- Navigation -->
            <nav class="settings-nav mb-4">
                <a href="#" class="nav-link-btn active" data-target="departments">Departments</a>
                <a href="#" class="nav-link-btn" data-target="suppliers">Suppliers</a>
                <a href="#" class="nav-link-btn" data-target="zones">Zones</a>
                <a href="#" class="nav-link-btn" data-target="reasons">Reasons</a>
            </nav>

            <!-- Departments Section -->
            <section id="departments" class="content-section">
                <h3 class="settings-heading">Departments</h3>
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('departments.store') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="name" class="form-control" placeholder="Department name" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" step="0.01" name="rate" class="form-control" placeholder="Rate" required>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success">Add</button>
                        </div>
                    </div>
                </form>

                <div class="hidden md:block glassy-table overflow-x-auto">
                    <table class="min-w-full tbl-w-full">
                        <thead class="glassy-table-container">
                            <tr>
                                <th>Name</th>
                                <th>Rate</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="glassy-table-container">
                            @foreach($departments as $department)
                                <tr class="glassy-table-container">
                                    <td>{{ $department->name }}</td>
                                    <form action="{{ route('departments.updateRate', $department) }}" method="POST" class="d-flex">
                                        @csrf
                                        @method('PUT')
                                        <td>
                                            <input type="number" name="rate" class="form-control me-2" value="{{ $department->rate }}" step="0.01" min="0" required>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                    </form>
                                    <form action="{{ route('departments.destroy', $department) }}" method="POST" onsubmit="return confirm('Are you sure?')" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Suppliers Section -->
            <section id="suppliers" class="content-section d-none">
                <h3 class="settings-heading">Suppliers</h3>
                <form action="{{ route('departments.store') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="name" class="form-control" placeholder="Supplier name" required>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success">Add</button>
                        </div>
                    </div>
                </form>

                <div class="hidden md:block glassy-table overflow-x-auto">
                    <table class="min-w-full tbl-w-full">
                        <thead class="glassy-table-container">
                            <tr>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="glassy-table-container">
                            @foreach($suppliers as $supplier)
                                <tr>
                                    <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <td><input type="text" name="name" value="{{ $supplier->name }}" class="form-control"></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary">Update</button>
                                    </form>
                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Zones Section -->
            <section id="zones" class="content-section d-none">
                <h3 class="settings-heading">Zones</h3>
                <form action="{{ route('zones.store') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="zoneName" class="form-control" placeholder="Zone name" required>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success">Add</button>
                        </div>
                    </div>
                </form>

                <div class="hidden md:block glassy-table overflow-x-auto">
                    <table class="min-w-full tbl-w-full">
                        <thead class="glassy-table-container">
                            <tr>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="glassy-table-container">
                            @foreach($zones as $zone)
                                <tr>
                                    <form action="{{ route('zones.update', $zone) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <td><input type="text" name="name" value="{{ $zone->zoneName }}" class="form-control"></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary">Update</button>
                                    </form>
                                    <form action="{{ route('zones.destroy', $zone) }}" method="POST" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Reasons Section -->
            <section id="reasons" class="content-section d-none">
                <h3 class="settings-heading">Reasons</h3>
                <form action="{{ route('reasons.store') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="reason" class="form-control" placeholder="Reason" required>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success">Add</button>
                        </div>
                    </div>
                </form>

                <div class="hidden md:block glassy-table overflow-x-auto">
                    <table class="min-w-full tbl-w-full">
                        <thead class="glassy-table-container">
                            <tr>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="glassy-table-container">
                            @foreach($reasons as $reason)
                                <tr>
                                    <form action="{{ route('reasons.update', $reason) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <td><input type="text" name="reason" value="{{ $reason->reason }}" class="form-control"></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary">Update</button>
                                    </form>
                                    <form action="{{ route('reasons.destroy', $reason) }}" method="POST" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div> 
    </div> 

    <script src="{{ asset('js/nav-menu.js') }}"></script>
</body>
</html>
