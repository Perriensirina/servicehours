<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Hours Overview</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/servicehours.css') }}">
</head>
<body>
    <h1>Overview of active tasks</h1>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Department</th>
                <th>Shipment</th>
                <th>Box Number</th>
                <th>U.L.</th>
                <th>Supplier</th>
                <th>AT Number</th>
                <th>Zone</th>
                <th>Reason</th>
                <th>Assigned Users</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tasks as $task)
                <tr>
                    <td data-label="Department">{{ $task->department }}</td>
                    <td data-label="Shipment">{{ $task->shipment}}</td>
                    <td data-label="Box Number">{{ $task->box_number}}</td>
                    <td data-label="U.L.">{{ $task->ul}}</td>
                    <td data-label="Supplier">{{ $task->supplier}}</td>
                    <td data-label="AT Number">{{ $task->AT_number}}</td>
                    <td data-label="Zone">{{ $task->zone }}</td>
                    <td data-label="Reason">{{ $task->reason }}</td>
                    {{-- New: show assigned users --}}
                    <td>
                        <a href="{{ route('tasks.show', $task->id) }}">
                            View Assigned Users ({{ $task->users->count() }})
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="11">No service registrations found. </td></tr>
            @endforelse
        </tbody>

    </table>

    <a href="{{ route('servicehours') }}">Back to Register Service</a>




</body>
</html>

