<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled - VCMS</title>
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

        .cancel-container {
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

        .cancel-header {
            background: linear-gradient(135deg, #f87171 0%, #dc2626 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }

        .cancel-icon {
            font-size: 80px;
            color: #fca5a5;
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

        .cancel-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #fff;
        }

        .cancel-subtitle {
            font-size: 14px;
            opacity: 0.9;
            color: rgba(255, 255, 255, 0.9);
        }

        .cancel-body {
            padding: 40px 20px;
            text-align: center;
        }

        .cancel-message {
            font-size: 16px;
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .cancel-message strong {
            color: #1f2937;
        }

        .info-card {
            background: #f3f4f6;
            border-left: 4px solid #f87171;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: left;
        }

        .info-card p {
            font-size: 14px;
            color: #374151;
            line-height: 1.6;
        }

        .info-card i {
            color: #dc2626;
            margin-right: 8px;
            font-weight: bold;
        }

        .button-group {
            display: flex;
            gap: 12px;
            flex-direction: column;
        }

        .btn {
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

        @media (max-width: 480px) {
            .cancel-container {
                max-width: 100%;
                border-radius: 16px;
            }

            .cancel-header {
                padding: 30px 16px;
            }

            .cancel-icon {
                font-size: 60px;
            }

            .cancel-title {
                font-size: 24px;
            }

            .cancel-body {
                padding: 24px 16px;
            }

            .cancel-message {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="cancel-container">
        <!-- Header -->
        <div class="cancel-header">
            <div class="cancel-icon">
                <i class="fas fa-circle-xmark"></i>
            </div>
            <div class="cancel-title">Payment Cancelled</div>
            <div class="cancel-subtitle">Your payment was not completed</div>
        </div>

        <!-- Body -->
        <div class="cancel-body">
            <div class="cancel-message">
                Your payment transaction has been <strong>cancelled</strong>. The amount was not charged to your account.
            </div>

            <div class="info-card">
                <p>
                    <i class="fas fa-info-circle"></i>
                    <strong>What happens next?</strong> You can retry your payment anytime from the payments section or contact support if you need assistance.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="button-group">
                <a href="{{ route('payments') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Payments
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Go to Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>
