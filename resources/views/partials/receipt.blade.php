<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clamping Receipt</title>
    <link rel="stylesheet" href="/../../styles/receipt.css">
</head>
<body>
    <button class="back-btn" id="backBtn">← Back</button>
    <button class="print-btn" onclick="window.print()">🖨 Print Receipt</button>
    
    <!-- Main Receipt Wrapper (Portal-style) -->
    <div class="receipt-wrapper">
        <div class="ticket-card">
            <!-- Header Section -->
            <div class="ticket-header">
                <h2>VCMS Receipt</h2>
                <small>Vehicle Clamping Management System</small>
            </div>

            <!-- Ticket Info Section -->
            <div class="ticket-info">
                <p><span class="label">Ticket No:</span> {{ $clamping->ticket_no }}</p>
                <p><span class="label">Plate No:</span> {{ $clamping->plate_no }}</p>
                <p><span class="label">Vehicle Type:</span> {{ $clamping->vehicle_type ?? 'N/A' }}</p>
                <p><span class="label">Violation:</span> {{ $clamping->reason }}</p>
                <p><span class="label">Location:</span> {{ $clamping->location }}</p>
                <p><span class="label">Fine Amount:</span> ₱{{ number_format($clamping->fine_amount, 2) }}</p>
                <p><span class="label">Date Issued:</span> {{ $clamping->created_at->format('M d, Y h:i A') }}</p>
            </div>

            <!-- Photo Section -->
            @if($clamping->photo)
            <div class="photo">
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($clamping->photo) }}" alt="Vehicle Photo">
            </div>
            @endif

            <!-- Status Badge -->
            <div class="status {{ strtolower($clamping->status) }}">
                {{ strtoupper($clamping->status) }}
            </div>

            <!-- Enforcer Info -->
            <div class="enforcer">
                @if($clamping->user)
                    <p>Issued by: <strong>{{ $clamping->user->f_name }} {{ $clamping->user->l_name }}</strong></p>
                    <p style="font-size: 12px; opacity: 0.8;">Officer ID: {{ $clamping->user->enforcer_id }}</p>
                @else
                    <p>Issued by: <strong>Traffic Enforcer</strong></p>
                @endif
            </div>

            <!-- QR Code Section -->
            <div class="qr-code-section">
                <div class="qr-code-wrapper">
                    {!! $qrCode !!}
                </div>
                <p style="font-size: 12px; margin-top: 8px; opacity: 0.9;">Scan to verify this ticket online</p>
            </div>

            <!-- Footer Section -->
            <div class="ticket-footer">
                <p>© {{ date('Y') }} City Traffic Department — All Rights Reserved</p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('backBtn').addEventListener('click', function() {
            const userRole = '{{ auth()->user()->role_id ?? 1 }}';
            
            if (userRole === '2') {
                window.location.href = '{{ route("enforcer.dashboard") }}';
            } else if (userRole === '3') {
                window.location.href = '{{ route("front-desk.dashboard") }}';
            } else {
                window.location.href = '{{ route("clampings") }}';
            }
        });
    </script>
</body>
</html>
