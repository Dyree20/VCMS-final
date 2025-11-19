@extends('layouts.front-desk')

@section('title', 'Record Walk-In Payment')

@section('content')
<div style="max-width: 800px; margin: 40px auto; padding: 20px;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="margin: 0; font-size: 28px; color: #333; font-weight: 600;">Record Payment</h2>
            <p style="margin: 5px 0 0 0; color: #999;">Walk-in Payment Processing</p>
        </div>
        <a href="{{ route('front-desk.payments') }}" style="padding: 10px 20px; background: #6c757d; color: white; border-radius: 6px; text-decoration: none; font-weight: 600;">← Back</a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 25px; border-left: 4px solid #28a745;">
            <strong>✓ Success!</strong> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 25px; border-left: 4px solid #dc3545;">
            <strong>⚠ Error!</strong> {{ session('error') }}
        </div>
    @endif

    <!-- Main Form Card -->
    <div style="background: white; border: 1px solid #e0e0e0; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;">
        <!-- Form Header -->
        <div style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); padding: 25px; color: white;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 600;">Payment Details</h3>
            <p style="margin: 5px 0 0 0; color: rgba(255,255,255,0.9); font-size: 14px;">Enter customer and violation information</p>
        </div>

        <!-- Form Body -->
        <form method="POST" action="{{ route('front-desk.payment.store') }}" style="padding: 30px;">
            @csrf

            <!-- Violation Selection -->
            <div style="margin-bottom: 25px;">
                <label for="ticket_no" style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Violation / Ticket Number *
                </label>
                <input type="text" name="ticket_no" id="ticket_no" placeholder="Enter ticket number (e.g., TK-001)" value="{{ old('ticket_no') }}" required onkeyup="updateViolationInfo()" style="width: 100%; padding: 12px 15px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 15px; color: #333; background: white; transition: border-color 0.3s;">
                <small style="color: #999; display: block; margin-top: 5px;">Type the ticket number to search</small>
                @error('ticket_no')
                    <span style="color: #dc3545; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Violation Info Cards -->
            <div id="violationInfo" style="display: none; margin-bottom: 25px; padding: 20px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #007bff;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display: block; font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 3px;">Plate No.</label>
                        <p id="plateDisplay" style="margin: 0; font-size: 16px; font-weight: 600; color: #333;">-</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 3px;">Fine Amount</label>
                        <p id="amountDisplay" style="margin: 0; font-size: 16px; font-weight: 600; color: #28a745;">-</p>
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <label style="display: block; font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 3px;">Reason</label>
                        <p id="reasonDisplay" style="margin: 0; font-size: 14px; color: #555;">-</p>
                    </div>
                </div>
            </div>

            <!-- Customer Name -->
            <div style="margin-bottom: 25px;">
                <label for="name" style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Customer Name *
                </label>
                <input type="text" name="name" id="name" placeholder="Enter full name" value="{{ old('name') }}" required style="width: 100%; padding: 12px 15px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 15px; box-sizing: border-box; transition: border-color 0.3s;">
                <small style="color: #999; display: block; margin-top: 5px;">e.g., Juan Dela Cruz</small>
                @error('name')
                    <span style="color: #dc3545; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Contact Number -->
            <div style="margin-bottom: 25px;">
                <label for="contact_number" style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Contact Number (Optional)
                </label>
                <input type="text" name="contact_number" id="contact_number" placeholder="09XX-XXX-XXXX" value="{{ old('contact_number') }}" style="width: 100%; padding: 12px 15px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 15px; box-sizing: border-box; transition: border-color 0.3s;">
                @error('contact_number')
                    <span style="color: #dc3545; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Amount Paid -->
            <div style="margin-bottom: 25px;">
                <label for="amount_paid" style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Amount Paid *
                </label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); font-size: 16px; font-weight: 600; color: #999;">₱</span>
                    <input type="number" name="amount_paid" id="amount_paid" placeholder="0.00" value="{{ old('amount_paid') }}" step="0.01" min="0" required style="width: 100%; padding: 12px 15px 12px 35px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 15px; box-sizing: border-box; transition: border-color 0.3s;">
                </div>
                <small style="color: #999; display: block; margin-top: 5px;" id="amountHint">Enter the amount paid by the customer</small>
                @error('amount_paid')
                    <span style="color: #dc3545; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 12px; margin-top: 30px; padding-top: 25px; border-top: 1px solid #e0e0e0;">
                <button type="submit" style="flex: 1; padding: 14px 24px; background: #28a745; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 16px; cursor: pointer; transition: background 0.3s;">
                    ✓ Process Payment
                </button>
                <a href="{{ route('front-desk.payments') }}" style="flex: 1; padding: 14px 24px; background: #6c757d; color: white; border-radius: 6px; font-weight: 600; font-size: 16px; text-decoration: none; text-align: center; transition: background 0.3s;">
                    ✕ Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Info Box -->
    <div style="background: #e7f3ff; border: 1px solid #b3d9ff; color: #004085; padding: 15px; border-radius: 6px; margin-top: 25px; border-left: 4px solid #0056b3;">
        <strong>ℹ Payment Info:</strong> Walk-in payments are processed immediately. Customer will receive a receipt upon completion. All transactions are recorded in the system.
    </div>
</div>

<script>
    const unpaidClampings = {!! json_encode($unpaidClampings) !!};

    function updateViolationInfo() {
        const ticketInput = document.getElementById('ticket_no').value.trim().toUpperCase();
        const violationInfo = document.getElementById('violationInfo');

        if (!ticketInput) {
            violationInfo.style.display = 'none';
            return;
        }

        const violation = unpaidClampings.find(c => c.ticket_no.toUpperCase() === ticketInput);

        if (violation) {
            document.getElementById('plateDisplay').textContent = violation.plate_no;
            document.getElementById('amountDisplay').textContent = '₱' + parseFloat(violation.fine_amount).toFixed(2);
            document.getElementById('reasonDisplay').textContent = violation.reason;
            document.getElementById('amountHint').textContent = 'Must be at least ₱' + parseFloat(violation.fine_amount).toFixed(2);
            document.getElementById('amount_paid').min = violation.fine_amount;
            document.getElementById('amount_paid').value = violation.fine_amount;
            violationInfo.style.display = 'block';
        } else {
            violationInfo.style.display = 'none';
        }
    }
</script>

@endsection
