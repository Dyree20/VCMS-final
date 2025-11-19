<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket Verification - VCMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/../../styles/portal.css">
</head>
<body>
    <div class="ticket-card">
        <!-- Success Alert -->
        @if(session('success'))
        <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-weight: 600;">
            ✅ {{ session('success') }}
        </div>
        @endif

        <div class="ticket-header">
            <h2>VCMS Ticket Verification</h2>
            <small>Vehicle Clamping Management System</small>
        </div>

        <div class="ticket-info">
            <p><span class="label">Ticket No:</span> {{ $clamping->ticket_no }}</p>
            <p><span class="label">Plate No:</span> {{ $clamping->plate_no }}</p>
            <p><span class="label">Vehicle Type:</span> {{ $clamping->vehicle_type }}</p>
            <p><span class="label">Reason:</span> {{ $clamping->reason }}</p>
            <p><span class="label">Fine Amount:</span> ₱{{ number_format($clamping->fine_amount, 2) }}</p>
            <p><span class="label">Date Issued:</span> {{ $clamping->created_at->format('M d, Y h:i A') }}</p>
        </div>

        @if($clamping->photo)
        <div class="photo">
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($clamping->photo) }}" alt="Vehicle Photo">
        </div>
        @endif

        <div class="status {{ strtolower($clamping->status) }}">
            {{ strtoupper($clamping->status) }}
        </div>

        <div class="enforcer">
            @if($clamping->user)
                <p>Issued by: <strong>{{ $clamping->user->f_name }} {{ $clamping->user->l_name }}</strong></p>
                <p style="font-size: 12px; color: #666; margin-top: 4px;">Officer ID: {{ $clamping->user->enforcer_id }}</p>
            @else
                <p>Issued by: <strong>Traffic Enforcer</strong></p>
            @endif
        </div>

        

        @if(strtolower($clamping->status) !== 'paid')
            <a href="{{ url('/pay/' . $clamping->ticket_no) }}" class="pay-btn">Pay Online</a>
        @else
            <div style="background-color: #e7f5ff; border: 1px solid #74c0fc; color: #1971c2; padding: 12px 16px; border-radius: 6px; text-align: center; font-weight: 600; margin-top: 10px;">
                ✅ This ticket has been paid. Thank you!
            </div>
        @endif


        <div class="ticket-footer">
            <p>© {{ date('Y') }} City Traffic Department — All Rights Reserved</p>
        </div>
    </div>
</body>
</html>
