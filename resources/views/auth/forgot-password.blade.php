<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Forgot Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/../../styles/authentication.css">
    <link rel="stylesheet" href="/../../styles/auth-overlay.css">
    <link rel="stylesheet" href="/../../styles/overlay.css">
    <style>
        .forgot-password-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }

        .forgot-password-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 450px;
            width: 100%;
            padding: 40px 35px;
        }

        .forgot-password-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .forgot-password-header img {
            width: 250px;
            height: auto;
            margin-bottom: 20px;
        }

        .forgot-password-header h2 {
            font-size: 28px;
            color: #2c3e50;
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .forgot-password-header p {
            color: #7f8c8d;
            font-size: 14px;
            margin: 0;
            line-height: 1.6;
        }

        .forgot-password-form {
            margin-top: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.6;
        }

        .success-message a {
            color: #155724;
            font-weight: 600;
            text-decoration: underline;
        }

        .error-message {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-size: 13px;
            color: #004085;
            line-height: 1.6;
        }

        @media (max-width: 480px) {
            .forgot-password-container {
                padding: 30px 20px;
            }

            .forgot-password-header h2 {
                font-size: 24px;
            }

            .forgot-password-header p {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="forgot-password-page">
        <div class="forgot-password-container">
            <!-- Header -->
            <div class="forgot-password-header">
                <img src="/images/VCMSlogo.png" alt="VCMS Logo">
                <h2>Forgot Password?</h2>
                <p>No worries! Enter your email address and we'll send you a link to reset your password.</p>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <strong>📧 How it works:</strong><br>
                We'll send a password reset link to your email. The link will expire in 1 hour for your security.
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="success-message">
                    {!! session('success') !!}
                </div>
            @endif

            <!-- Error Message -->
            @if($errors->any())
                <div class="error-message">
                    <strong>❌ {{ $errors->first() }}</strong>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('forgot-password.email') }}" method="POST" class="forgot-password-form">
                @csrf
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="admin@example.com"
                        value="{{ old('email') }}"
                        required
                        autofocus
                    >
                    @error('email')
                        <small style="color: #dc3545; margin-top: 5px; display: block;">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="submit-btn">Send Reset Link</button>

                <!-- Back Link -->
                <div class="back-link">
                    <a href="{{ route('login.form') }}">← Back to Login</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Optional: Add loading state to submit button
        document.querySelector('.forgot-password-form').addEventListener('submit', function() {
            const btn = document.querySelector('.submit-btn');
            const originalText = btn.textContent;
            btn.textContent = 'Sending...';
            btn.disabled = true;
            setTimeout(() => {
                btn.textContent = originalText;
                btn.disabled = false;
            }, 3000);
        });
    </script>
</body>
</html>
