<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice Task {{ $task->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .header, .footer { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 22px; color: #222; }
        .header p { margin: 4px 0; font-size: 13px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f8f9fa; font-size: 13px; text-align: left; }
        .total-row td { font-weight: bold; background: #f5f5f5; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>ID Logistics</h1>
        <p><strong>Invoice for Task #{{ $task->id }}</strong></p>
    </div>

    <!-- Task Info -->
    <table>
        <tr>
            <th>Department</th>
            <td>{{ ucfirst($task->department) }}</td>
        </tr>
        <tr>
            <th>Reason</th>
            <td>{{ $task->reason }}</td>
        </tr>
        <tr>
            <th>Hours</th>
            <td>
                @if($hours >= 1)
                    {{ number_format($hours, 2, ',', '.') }} h
                @else
                    {{ $minutes }} min
                @endif
            </td>
        </tr>
        <tr>
            <th>Rate (€)</th>
            <td>{{ number_format($rate, 2, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <th>Total (€)</th>
            <td>{{ number_format($totalPrice, 2, ',', '.') }}</td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y H:i') }} by ID Logistics</p>
    </div>
</body>
</html>
