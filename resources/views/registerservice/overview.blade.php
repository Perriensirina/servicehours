<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Hours Overview</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>

<body>
    <!-- Navbar -->
    <div class="position-absolute top-0 end-0 p-3">
            <a href="{{ url('/servicehours') }}" class="bi bi-house fs-3 text-white"></a>
            <a href="{{ url('/account') }}" class="bi bi-person fs-3 text-white"></a>
    </div>
    <div class="container py-5">
        <a href="{{ route('servicehours') }}" class="back-arrow">&#8592;</a>
        <h2 class="text-center">Overview of Registered Service Hours</h2>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Admin Filters -->
        <p class="demo"></p>
        <button class="filter hidden-btn btn btn-outline-light btn-sm">X Filter</button>
        <div id="filterContainer" class="filter-container">
            @if(auth()->user()->role === 'admin')
                <form method="GET" action="{{ route('registerservice.overview') }}" class="row g-3 mb-4 align-items-end">
                    <div class="col-md-3">
                        <label for="from_date" class="form-label">From</label>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="to_date" class="form-label">To</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="validated" class="form-label">Validated</label>
                        <select name="validated" class="form-select">
                            <option value="">All</option>
                            <option value="1" {{ request('validated') === '1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ request('validated') === '0' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="invoiced" class="form-label">Invoiced</label>
                        <select name="invoiced" class="form-select">
                            <option value="">All</option>
                            <option value="1" {{ request('invoiced') === '1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ request('invoiced') === '0' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary">🔍 Filter</button>
                    </div>
                    <div class="col-md-2 d-grid">
                        <a href="{{ route('registerservice.exportCsv', request()->query()) }}" class="btn btn-success">Export CSV</a>
                    </div>
                </form>

                <!-- Filter summary -->
                @if(request()->hasAny(['from_date', 'to_date', 'validated', 'invoiced']))
                    <div class="alert alert-info mb-4">
                        <strong>Showing tasks:</strong>
                        @if(request('from_date') && request('to_date'))
                            From <strong>{{ request('from_date') }}</strong>
                            to <strong>{{ request('to_date') }}</strong>;
                        @endif
                        @if(request('validated') !== null && request('validated') !== '')
                            Validated: <strong>{{ request('validated') === '1' ? 'Yes' : 'No' }}</strong>;
                        @endif
                        @if(request('invoiced') !== null && request('invoiced') !== '')
                            Invoiced: <strong>{{ request('invoiced') === '1' ? 'Yes' : 'No' }}</strong>;
                        @endif
                        <a href="{{ route('registerservice.overview') }}" class="btn btn-sm btn-outline-secondary ms-3">
                            Clear Filters
                        </a>
                    </div>
                @endif

                <!-- Bulk invoice -->
                @if(request('from_date') && request('to_date'))
                    <form action="{{ route('tasks.bulkInvoice') }}" method="POST" class="mb-4">
                        @csrf
                        <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                        <input type="hidden" name="to_date" value="{{ request('to_date') }}">
                        <button type="submit" class="btn btn-success">💸 Invoice All Filtered</button>
                    </form>
                @endif
            @endif
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block glassy-table overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="glassy-table-container">
                        <th>Department</th>
                        <th>Shipment</th>
                        <th>Box#</th>
                        <th>U.L.</th>
                        <th>Supplier</th>
                        <th>AT#</th>
                        <th>Zone</th>
                        <th>Reason</th>
                        <th>Time spent</th>
                        <th>Status</th>
                        <th>Assigned Users</th>
                    </tr>
                </thead>
                <tbody class="glassy-table-container"> 
                    @forelse ($registrations as $registration)
                        <tr>
                            <td class="glassy-table-container">{{ $registration->department }}</td>
                            <td class="glassy-table-container">{{ $registration->shipment }}</td>
                            <td class="glassy-table-container">{{ $registration->box_number }}</td>
                            <td class="glassy-table-container">{{ $registration->ul }}</td>
                            <td class="glassy-table-container">{{ $registration->supplier }}</td>
                            <td class="glassy-table-container">{{ $registration->AT_number }}</td>
                            <td class="glassy-table-container">{{ $registration->zone }}</td>
                            <td class="glassy-table-container">{{ $registration->reason }}</td>
                            <td class="glassy-table-container">
                                @php
                                    $totalSeconds = 0;
                                    $hasActiveUsers = false;
                                    foreach ($registration->users as $user) {
                                        $p = $user->pivot;
                                        if ($p->started_at && $p->stopped_at) {
                                            $totalSeconds += \Carbon\Carbon::parse($p->started_at)
                                                            ->diffInSeconds(\Carbon\Carbon::parse($p->stopped_at));
                                        } elseif ($p->started_at && !$p->stopped_at) {
                                            $hasActiveUsers = true;
                                        }
                                    }
                                @endphp
                                @if($hasActiveUsers)
                                    <span class="glassy-badge bl">In Progress</span>
                                @elseif($totalSeconds > 0)
                                    {{ gmdate('H:i:s', $totalSeconds) }}
                                    @if(!$registration->validated)
                                        <span class="glassy-badge gray">Awaiting validation</span>
                                    @endif
                                @endif
                            </td>
                            <td class="glassy-table-container">
                                {{-- Status logic --}}
                                @if(!$registration->validated)
                                    @if(auth()->user()->role === 'teamleader' || auth()->user()->role === 'admin')
                                        @php
                                            $allDone = $registration->users->every(fn($u) => $u->pivot->started_at && $u->pivot->stopped_at);
                                        @endphp
                                        @if($allDone)
                                            <form action="{{ route('tasks.validate', $registration->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Validate</button>
                                            </form>
                                        @else
                                            <span class="glassy-badge ylw">Waiting</span>
                                        @endif
                                    @else
                                        <span class="glassy-badge org">⏳ Pending</span>
                                    @endif
                                @else
                                    <span class="glassy-badge dgrn">Validated</span>
                                    @if(auth()->user()->role === 'admin' && !$registration->invoiced)
                                        <form action="{{ route('tasks.invoice', $registration->id) }}" method="POST" class="mt-2">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm">💰 Invoice</button>
                                        </form>
                                    @endif
                                @endif
                                <div class="mt-2">
                                    @switch($registration->status)
                                        @case('Validated')
                                            <span class="glassy-badge ylw">Validated</span>
                                            @break
                                        @case('🚫 Not Started')
                                            <span class="glassy-badge prpl">Not Started</span>
                                            @break
                                        @case('⏳ Active')
                                            <span class="glassy-badge grn">Active</span>
                                            @break
                                    @endswitch
                                    @if($registration->invoiced)
                                        <span class="glassy-badge grn">Invoiced</span>
                                    @endif
                                </div>
                            </td>
                            <td class="glassy-table-container">
                                <ul class="list-unstyled mb-0">
                                    @foreach($registration->users as $user)
                                        <li class="mb-1 assigned-users-col">
                                            <p class="user-name">{{ $user->name }}</p>
                                            @php
                                                $time_spent = null;
                                                if ($user->pivot->started_at && $user->pivot->stopped_at) {
                                                    $time_spent = \Carbon\Carbon::parse($user->pivot->started_at)
                                                                    ->diffInSeconds(\Carbon\Carbon::parse($user->pivot->stopped_at));
                                                }
                                            @endphp
                                            @if($time_spent)
                                                @if(auth()->user()->role === 'admin' && !$registration->invoiced && !$registration->validated)
                                                    <form action="{{ route('tasks.updateTime', [$registration->id, $user->id]) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <div class="time-btn-container">
                                                            <input type="number" name="time_spent" value="{{ round(($time_spent ?? 0) / 60) }}" min="0" step="1" class="time-input" style="width: 100%;">
                                                            <button type="submit" class="btn btn-info btn-sm">💾</button>
                                                        </div>
                                                    </form>
                                                @endif
                                            @endif
                                            <div class="user-actions">
                                                {{-- Operator controls --}}
                                                @if(auth()->user()->role === 'operator' && auth()->id() === $user->id)
                                                    @if(!$user->pivot->started_at || $user->pivot->stopped_at)
                                                        <form action="{{ route('tasks.start.user', [$registration->id, $user->id]) }}" method="POST">
                                                            @csrf
                                                            @if(!$registration->validated)
                                                                <button type="submit" class="start btn btn-success btn-sm">▶ Start</button>
                                                            @endif
                                                        </form>
                                                    @endif
                                                    @if($user->pivot->started_at && !$user->pivot->stopped_at)
                                                        <form action="{{ route('tasks.stop.user', [$registration->id, $user->id]) }}" method="POST">
                                                            @csrf
                                                            @if(!$registration->validated)
                                                                <button type="submit" class="stop btn btn-danger btn-sm">⏹ Stop</button>
                                                            @endif
                                                        </form>
                                                    @endif
                                                @elseif(auth()->user()->role !== 'operator')
                                                    @if(!$user->pivot->started_at || $user->pivot->stopped_at)
                                                        <form action="{{ route('tasks.start.user', [$registration->id, $user->id]) }}" method="POST">
                                                            @csrf
                                                            @if(!$registration->validated)
                                                                <button type="submit" class="btn btn-success btn-sm start">▶ Start</button>
                                                            @endif
                                                        </form>
                                                    @endif
                                                    @if($user->pivot->started_at && !$user->pivot->stopped_at)
                                                        <form action="{{ route('tasks.stop.user', [$registration->id, $user->id]) }}" method="POST">
                                                            @csrf
                                                            @if(!$registration->validated)
                                                                <button type="submit" class="stop btn btn-danger btn-sm">⏹ Stop</button>
                                                            @endif
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">No service registrations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile stacked cards -->
        <div class="block md:hidden space-y-4">
            @forelse ($registrations as $registration)
                <div class="glassy-table p-4 rounded-lg">
                    <p><strong>Department:</strong> {{ $registration->department }}</p>
                    <p><strong>Shipment:</strong> {{ $registration->shipment }}</p>
                    <p><strong>Box#:</strong> {{ $registration->box_number }}</p>
                    <p><strong>U.L.:</strong> {{ $registration->ul }}</p>
                    <p><strong>Supplier:</strong> {{ $registration->supplier }}</p>
                    <p><strong>AT#:</strong> {{ $registration->AT_number }}</p>
                    <p><strong>Zone:</strong> {{ $registration->zone }}</p>
                    <p><strong>Reason:</strong> {{ $registration->reason }}</p>

                    <div>
                        <strong>Time spent:</strong>
                        @php
                            $totalSeconds = 0;
                            $hasActiveUsers = false;
                            foreach ($registration->users as $user) {
                                $p = $user->pivot;
                                if ($p->started_at && $p->stopped_at) {
                                    $totalSeconds += \Carbon\Carbon::parse($p->started_at)
                                                    ->diffInSeconds(\Carbon\Carbon::parse($p->stopped_at));
                                } elseif ($p->started_at && !$p->stopped_at) {
                                    $hasActiveUsers = true;
                                }
                            }
                        @endphp
                        @if($hasActiveUsers)
                            <span class="glassy-badge bl">In Progress</span>
                        @elseif($totalSeconds > 0)
                            {{ gmdate('H:i:s', $totalSeconds) }}
                            @if(!$registration->validated)
                                <span class="glassy-badge gray">Awaiting validation</span>
                            @endif
                        @endif
                    </div>

                    <div class="mt-2">
                        <strong>Status:</strong><br>
                        @if(!$registration->validated)
                            @if(auth()->user()->role === 'teamleader' || auth()->user()->role === 'admin')
                                @php
                                    $allDone = $registration->users->every(fn($u) => $u->pivot->started_at && $u->pivot->stopped_at);
                                @endphp
                                @if($allDone)
                                    <form action="{{ route('tasks.validate', $registration->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Validate</button>
                                    </form>
                                @else
                                    <span class="glassy-badge ylw">Waiting</span>
                                @endif
                            @else
                                <span class="glassy-badge org">⏳ Pending</span>
                            @endif
                        @else
                            <span class="glassy-badge dgrn">Validated</span>
                            @if(auth()->user()->role === 'admin' && !$registration->invoiced)
                                <form action="{{ route('tasks.invoice', $registration->id) }}" method="POST" class="mt-2">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm">💰 Invoice</button>
                                </form>
                            @endif
                        @endif
                        <div class="mt-2">
                            @switch($registration->status)
                                @case('Validated')
                                    <span class="glassy-badge ylw">Validated</span>
                                    @break
                                @case('🚫 Not Started')
                                    <span class="glassy-badge prpl">Not Started</span>
                                    @break
                                @case('⏳ Active')
                                    <span class="glassy-badge grn">Active</span>
                                    @break
                            @endswitch
                            @if($registration->invoiced)
                                <span class="glassy-badge grn">Invoiced</span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-2">
                        <strong>Assigned Users:</strong>
                        <ul class="list-unstyled">
                            @foreach($registration->users as $user)
                                <li class="mb-1 assigned-users-col">
                                    <p class="user-name">{{ $user->name }}</p>
                                    @php
                                        $time_spent = null;
                                        if ($user->pivot->started_at && $user->pivot->stopped_at) {
                                            $time_spent = \Carbon\Carbon::parse($user->pivot->started_at)
                                                            ->diffInSeconds(\Carbon\Carbon::parse($user->pivot->stopped_at));
                                        }
                                    @endphp
                                    @if($time_spent)
                                        @if(auth()->user()->role === 'admin' && !$registration->invoiced && !$registration->validated)
                                            <form action="{{ route('tasks.updateTime', [$registration->id, $user->id]) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <div class="time-btn-container">
                                                    <input type="number" name="time_spent" value="{{ round(($time_spent ?? 0) / 60) }}" min="0" step="1" class="time-input" style="width: 100%;">
                                                    <button type="submit" class="btn btn-info btn-sm">💾</button>
                                                </div>
                                            </form>
                                        @endif
                                    @endif
                                    <div class="user-actions">
                                        @if(auth()->user()->role === 'operator' && auth()->id() === $user->id)
                                            @if(!$user->pivot->started_at || $user->pivot->stopped_at)
                                                <form action="{{ route('tasks.start.user', [$registration->id, $user->id]) }}" method="POST">
                                                    @csrf
                                                    @if(!$registration->validated)
                                                        <button type="submit" class="start btn btn-success btn-sm">▶ Start</button>
                                                    @endif
                                                </form>
                                            @endif
                                            @if($user->pivot->started_at && !$user->pivot->stopped_at)
                                                <form action="{{ route('tasks.stop.user', [$registration->id, $user->id]) }}" method="POST">
                                                    @csrf
                                                    @if(!$registration->validated)
                                                        <button type="submit" class="stop btn btn-danger btn-sm">⏹ Stop</button>
                                                    @endif
                                                </form>
                                            @endif
                                        @elseif(auth()->user()->role !== 'operator')
                                            @if(!$user->pivot->started_at || $user->pivot->stopped_at)
                                                <form action="{{ route('tasks.start.user', [$registration->id, $user->id]) }}" method="POST">
                                                    @csrf
                                                    @if(!$registration->validated)
                                                        <button type="submit" class="btn btn-success btn-sm start">▶ Start</button>
                                                    @endif
                                                </form>
                                            @endif
                                            @if($user->pivot->started_at && !$user->pivot->stopped_at)
                                                <form action="{{ route('tasks.stop.user', [$registration->id, $user->id]) }}" method="POST">
                                                    @csrf
                                                    @if(!$registration->validated)
                                                        <button type="submit" class="stop btn btn-danger btn-sm">⏹ Stop</button>
                                                    @endif
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul> 
                    </div>
                </div>
            @empty
                <p class="text-center">No service registrations found.</p>
            @endforelse
        </div>

        <div class="mt-3">
            <a href="{{ route('servicehours') }}" class="btn btn-outline-light btn-sm">&#8592;</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="{{ asset('js/overview.js') }}"></script>                                     -->
</body>
</html>
