<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Bulk Invoice</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .header, .footer { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 22px; color: #222; }
        .header p { margin: 4px 0; font-size: 13px; color: #666; }
        .company-info, .customer-info {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            margin-bottom: 20px;
        }
        h3.section-title {
            margin-top: 25px;
            font-size: 15px;
            border-bottom: 2px solid #444;
            padding-bottom: 4px;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f8f9fa; font-size: 13px; text-align: left; }
        tr:nth-child(even) { background: #fdfdfd; }
        .subtotal-row td { font-weight: bold; background: #f5f5f5; }
        .grandtotal-row td {
            font-weight: bold;
            font-size: 14px;
            background: #e6e6e6;
        }
        .footer { margin-top: 30px; font-size: 11px; color: #666; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>ID Logistics</h1>
        <p><strong>Invoice</strong> for period {{ $from }} → {{ $to }}</p>
    </div>

    <!-- Company & Customer Info -->
    <div class="company-info">
        <strong>ID Logistics Belgium</strong><br>
        Hoeikensstraat 50/51<br>
        2850 Willebroek<br>
    </div>
    <div class="customer-info">
        <strong>Customer</strong><br>
        Maxeda<br>
        Hoeikensstraat 50/51<br>
        2850 Willebroek<br>
    </div>

    <!-- Department Sections -->
    @php
        $departments = $tasks->groupBy('department');
        $grandTotal = 0;
    @endphp

    @foreach($departments as $department => $deptTasks)
        <h3 class="section-title">{{ ucfirst($department) }}</h3>

        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">Task ID</th>
                    <th style="width: 40%;">Reason</th>
                    <th style="width: 10%;">Duration</th>
                    <th style="width: 15%;">Rate (€)</th>
                    <th style="width: 15%;">Total (€)</th>
                </tr>
            </thead>
            <tbody>
                @php $deptTotal = 0; @endphp
                @foreach ($deptTasks as $task)
                    @php
                        $totalSeconds = 0;
                        foreach ($task->users as $user) {
                            if ($user->pivot->started_at && $user->pivot->stopped_at) {
                                $totalSeconds += \Carbon\Carbon::parse($user->pivot->started_at)
                                    ->diffInSeconds(\Carbon\Carbon::parse($user->pivot->stopped_at));
                            }
                        }

                        // ✅ Calculate per started minute
                        $totalMinutes = ceil($totalSeconds / 60);
                        $hours = $totalMinutes / 60;

                        // Apply 45% rule only for promo %
                        if (strtolower($department) === 'promo %') {
                            $hours = $hours * 0.45;
                        }

                        // ✅ Fetch rate dynamically
                        $deptModel = \App\Models\Department::where('name', $department)->first();
                        $rate = $deptModel?->rate ?? 40.00;

                        $total = $hours * $rate;
                        $deptTotal += $total;
                        $grandTotal += $total;
                    @endphp

                    <tr>
                        <td>{{ $task->id }}</td>
                        <td>{{ $task->reason }}</td>
                        <td>
                            @if($hours >= 1)
                                {{ number_format($hours, 2, ',', '.') }} h
                            @else
                                {{ $totalMinutes }} min
                            @endif
                        </td>
                        <td>{{ number_format($rate, 2, ',', '.') }}</td>
                        <td>{{ number_format($total, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td colspan="4" style="text-align: right;">Subtotal {{ ucfirst($department) }}</td>
                    <td>{{ number_format($deptTotal, 2, ',', '.') }} €</td>
                </tr>
            </tbody>
        </table>
    @endforeach

    <!-- Grand Total -->
    <table style="margin-top: 25px;">
        <tr class="grandtotal-row">
            <td colspan="4" style="text-align: right;">Grand Total</td>
            <td>{{ number_format($grandTotal, 2, ',', '.') }} €</td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y H:i') }} by ID Logistics</p>
    </div>
</body>
</html>
