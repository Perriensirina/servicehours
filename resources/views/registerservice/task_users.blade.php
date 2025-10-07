<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Details - {{ $task->reason }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/servicehours.css') }}">
    <style>
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1rem;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #4A5568;
            color: white;
        }
        .flash-message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        .flash-message.success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        .flash-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        .action-buttons button {
            padding: 8px 12px;
            border-radius: 5px;
            border: none;
            color: white;
            cursor: pointer;
        }
        .action-buttons button.start {
            background-color: #28a745;
        }
        .action-buttons button.stop {
            background-color: #dc3545;
        }
        .action-buttons button.validate {
            background-color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Task Details: {{ $task->reason }}</h1>
        <p><strong>Department:</strong> {{ $task->department }}</p>
        <p><strong>Zone:</strong> {{ $task->zone }}</p>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="flash-message success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="flash-message error">
                {{ session('error') }}
            </div>
        @endif

        <h2>Assigned Users</h2>
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Time Spent</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($task->users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>
                            @if($user->pivot->started_at && $user->pivot->stopped_at)
                                @php
                                    $time_spent = \Carbon\Carbon::parse($user->pivot->started_at)->diffInSeconds(\Carbon\Carbon::parse($user->pivot->stopped_at));
                                @endphp
                                {{ gmdate('H:i:s', $time_spent) }}
                            @elseif($user->pivot->started_at && !$user->pivot->stopped_at)
                                ⏳ In Progress
                            @else
                                -
                            @endif
                        </td>
                        <td>    
                            @if($task->validated)
                                ✅ Validated
                            @else
                                ⏳ Pending
                            @endif
                        </td>
                        <td>
                            @php
                                $isCurrentUser = auth()->id() === $user->id;
                            @endphp

                            <div class="action-buttons">
                                @if($isCurrentUser && (!$user->pivot->started_at || ($user->pivot->started_at && $user->pivot->stopped_at)))
                                    <form action="{{ route('tasks.start', $task->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="start">▶ Start</button>
                                    </form>
                                @endif

                                @if($isCurrentUser && $user->pivot->started_at && !$user->pivot->stopped_at)
                                    <form action="{{ route('tasks.stop', $task->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="stop">⏹ Stop</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No users assigned to this task.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        {{-- Task-level validation button --}}
        @if(auth()->user()->role === 'teamleader' || auth()->user()->role === 'admin')
            @if(!$task->validated)
                @php
                    $hasActiveUsers = $task->users->contains(fn($u) => $u->pivot->started_at && !$u->pivot->stopped_at);
                @endphp

                @if($hasActiveUsers)
                    <p style="margin-top: 1rem;">⏳ Waiting for all users to stop</p>
                @else
                    <form action="{{ route('tasks.validate', $task->id) }}" method="POST" style="margin-top: 1rem;">
                        @csrf
                        <button type="submit" class="validate">Validate Task</button>
                    </form>
                @endif
            @else
                <p style="margin-top: 1rem;">✅ Task is validated</p>
            @endif
        @endif
        

        <a href="{{ route('registerservice.overview') }}" style="display: block; margin-top: 1rem;">← Back to Overview</a>
    </div>
</body>
</html>
