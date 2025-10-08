<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Overview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/landing.css') }}" rel="stylesheet">
</head>
<body>
<div class="position-absolute top-0 end-0 p-3">
        <a href="{{ url('/servicehours') }}" class="bi bi-house fs-3 text-white"></a>
        <a href="{{ url('/account') }}" class="bi bi-person fs-3 text-white"></a>
</div>

<div class="container py-5">
    <a href="{{ route('servicehours') }}" class="back-arrow">&#8592;</a>
    <h2 class="text-center text-white mb-4">Activity Overview</h2>

    <div class="glassy-card p-4 rounded-4 shadow-lg">
        @foreach($logs as $log) 
            <div class="p-3 rounded glassy-table-container">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="text-white">{{ $log->user?->name ?? 'System' }}</strong>
                        <span class="text-light ms-2">
                            {{ ucfirst($log->action) }} {{ $log->model }} #{{ $log->model_id }}
                        </span>
                    </div>
                    <small class="">{{ $log->created_at->diffForHumans() }}</small>
                </div>

                @if($log->changes)
                    <div class="mt-3 p-3 rounded bg-white bg-opacity-10">
                        <ul class="mb-0">
                            @foreach($log->changes as $field => $value)
                                @if(is_array($value) && isset($value['old'], $value['new']))
                                    <li>
                                        <strong class="text-white">{{ ucfirst($field) }}</strong>:
                                        <span class="text-danger">{{ $value['old'] ?? '—' }}</span>
                                        →
                                        <span class="text-success">{{ $value['new'] ?? '—' }}</span>
                                    </li>
                                @else
                                    <li class="text-light"><strong>{{ ucfirst($field) }}</strong>: {{ json_encode($value) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

</body>
</html>
