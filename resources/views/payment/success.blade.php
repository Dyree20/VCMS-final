<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - VCMS</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .success-container {
            width: 100%;
            max-width: 450px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }

        .success-icon {
            font-size: 80px;
            color: #4ade80;
            margin-bottom: 20px;
            animation: scaleIn 0.6s ease-out;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.5);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #fff;
        }

        .success-subtitle {
            font-size: 14px;
            opacity: 0.9;
            color: rgba(255, 255, 255, 0.9);
        }

        .success-body {
            padding: 40px 20px;
        }

        .info-section {
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            font-size: 15px;
        }

        .info-label {
            color: #6b7280;
            font-weight: 500;
        }

        .info-value {
            color: #1f2937;
            font-weight: 600;
            text-align: right;
            word-break: break-word;
            flex: 1;
            margin-left: 12px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .amount-highlight {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: #f9fafb;
            border-radius: 12px;
        }

        .qr-section {
            text-align: center;
            padding: 20px;
            background: #f9fafb;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .qr-section img {
            max-width: 150px;
            height: auto;
            border: 2px solid #e5e7eb;
            padding: 8px;
            border-radius: 8px;
        }

        .qr-label {
            font-size: 12px;
            color: #6b7280;
            margin-top: 8px;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 14px 20px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 2px solid #e5e7eb;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
            border-color: #d1d5db;
        }

        .receipt-note {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 12px;
            border-radius: 6px;
            font-size: 13px;
            color: #1e40af;
            margin-top: 20px;
        }

        .receipt-note i {
            margin-right: 8px;
        }

        @media (max-width: 480px) {
            .success-container {
                max-width: 100%;
                border-radius: 16px;
            }

            .success-header {
                padding: 30px 16px;
            }

            .success-icon {
                font-size: 60px;
            }

            .success-title {
                font-size: 24px;
            }

            .success-body {
                padding: 24px 16px;
            }

            .amount-highlight {
                font-size: 28px;
                padding: 16px;
            }

            .button-group {
                flex-direction: column;
            }

            .info-row {
                font-size: 14px;
            }
        }

        @media print {
            body {
                background: white;
            }

            .success-container {
                box-shadow: none;
                max-width: 100%;
            }

            .button-group {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <!-- Header -->
        <div class="success-header">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="success-title">Payment Successful!</div>
            <div class="success-subtitle">Your payment has been processed</div>
        </div>

        <!-- Body -->
        <div class="success-body">
            <!-- Amount Section -->
            <div class="amount-highlight">
                ₱{{ number_format($clamping->fine_amount, 2) }}
            </div>

            <!-- Payment Details -->
            <div class="info-section">
                <div class="info-row">
                    <span class="info-label">Ticket Number:</span>
                    <span class="info-value">{{ $ticket_no }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Plate Number:</span>
                    <span class="info-value">{{ $clamping->plate_no }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ strtolower($status) }}">
                            {{ ucfirst($status) }}
                        </span>
                    </span>
                </div>
            </div>

            <!-- Clamping Details -->
            <div class="info-section">
                <div class="info-row">
                    <span class="info-label">Reason:</span>
                    <span class="info-value">{{ $clamping->reason }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Location:</span>
                    <span class="info-value">{{ $clamping->location }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date Clamped:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($clamping->date_clamped)->format('M d, Y') }}</span>
                </div>
            </div>

            <!-- QR Code Section -->
            @if(isset($qrCode))
            <div class="qr-section">
                {!! $qrCode !!}
                <div class="qr-label">Scan to verify ticket</div>
            </div>
            @endif

            <!-- Info Note -->
            <div class="receipt-note">
                <i class="fas fa-info-circle"></i>
                Keep this receipt for your records. You can view your receipt anytime from your dashboard.
            </div>

            <!-- Action Buttons -->
            <div class="button-group">
                <a href="{{ route('payments') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Payments
                </a>
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</body>
</html>
