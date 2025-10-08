<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<div class="container">
    <h2 class="mb-4">Activity Overview</h2>

    <div class="list-group">
        @foreach($logs as $log)
            <div class="list-group-item shadow-sm mb-2 rounded">
                <div class="d-flex justify-content-between">
                    <div>
                        <strong>{{ $log->user?->name ?? 'System' }}</strong>
                        <span class="text-muted">
                            {{ ucfirst($log->action) }} 
                            {{ $log->model }} #{{ $log->model_id }}
                        </span>
                    </div>
                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                </div>

                {{-- Show changes --}}
                @if($log->changes)
                    <div class="mt-2 p-2 bg-light rounded">
                        <ul class="mb-0">
                            @foreach($log->changes as $field => $value)
                                @if(is_array($value) && isset($value['old'], $value['new']))
                                    <li>
                                        <strong>{{ ucfirst($field) }}</strong>:
                                        <span class="text-danger">{{ $value['old'] ?? '—' }}</span>
                                        →
                                        <span class="text-success">{{ $value['new'] ?? '—' }}</span>
                                    </li>
                                @else
                                    <li><strong>{{ ucfirst($field) }}</strong>: {{ json_encode($value) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-3">
        {{ $logs->links() }}
    </div>
</div>

</body>
</html>