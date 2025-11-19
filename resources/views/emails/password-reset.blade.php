<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #4895ef 0%, #3671c6 100%);
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #333;
            font-size: 20px;
            margin-bottom: 15px;
        }
        .content p {
            color: #555;
            line-height: 1.8;
            margin: 15px 0;
        }
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #4895ef 0%, #3671c6 100%);
            color: #ffffff;
            padding: 14px 40px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 25px 0;
            transition: opacity 0.3s ease;
        }
        .reset-button:hover {
            opacity: 0.9;
        }
        .info-box {
            background-color: #f0f4f8;
            border-left: 4px solid #4895ef;
            padding: 15px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 14px;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px 30px;
            text-align: center;
            color: #888;
            font-size: 12px;
            border-top: 1px solid #eee;
        }
        .logo {
            height: 40px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🔐 Password Reset Request</h1>
        </div>
        <div class="content">
            <h2>Hello {{ $userName }},</h2>
            <p>We received a request to reset your password for your VCM System account. Click the button below to proceed with resetting your password.</p>
            
            <center>
                <a href="{{ $resetUrl }}" class="reset-button">Reset Your Password</a>
            </center>

            <p>Or copy and paste this link in your browser:</p>
            <p style="word-break: break-all; background-color: #f5f5f5; padding: 10px; border-radius: 4px;">
                {{ $resetUrl }}
            </p>

            <div class="info-box">
                <p><strong>⏰ Important:</strong> This password reset link will expire in 1 hour for security reasons.</p>
                <p><strong>🛡️ Security:</strong> If you didn't request this password reset, please ignore this email or contact support.</p>
            </div>

            <p>Thank you for using VCM System.</p>
        </div>
        <div class="footer">
            <p>&copy; 2025 Vehicle Clamping Management System. All rights reserved.</p>
            <p>This is an automated email. Please do not reply to this message.</p>
        </div>
    </div>
</body>
</html>
