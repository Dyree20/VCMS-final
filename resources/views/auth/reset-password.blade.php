<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/../../styles/authentication.css">
    <link rel="stylesheet" href="/../../styles/auth-overlay.css">
    <link rel="stylesheet" href="/../../styles/overlay.css">
</head>
<body>
    <div class="auth-container" id="auth-container">
        <div class="auth-background"></div>
        <div class="auth-bg-logo">
            <img src="/images/VCMSlogo.png" alt="logo">
        </div>

        <!-- Reset Password Form -->
        <div class="auth-form-container auth-login-container" style="display: block;">
            <form action="{{ route('password.reset') }}" method="POST" id="resetForm" class="auth-form">
                @csrf
            
                <h2>Create New Password</h2>
                <p style="text-align: center; color: #666; margin-bottom: 20px; font-size: 13px;">Enter your new password below to reset your account access.</p>
                
                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="token" value="{{ $token }}">
                
                <input type="password" class="auth-input" placeholder="New Password" name="password" required>
                <input type="password" class="auth-input" placeholder="Confirm Password" name="password_confirmation" required>
                
                <button type="submit" class="auth-button">Reset Password</button>

                @if($errors->any())
                    <div id="errorOverlay" class="auth-overlay-v2">
                        <div class="auth-dialog-v2">
                            <div style="color: red; font-size: 28px; margin-bottom: 15px;">❌</div>
                            <div id="errorMessage" class="message">{{ $errors->first() }}</div>
                            <a href="/login" style="margin-top: 15px; display: inline-block; padding: 10px 20px; background: #007bff; color: white; border-radius: 6px; text-decoration: none;">Back to Login</a>
                        </div>
                    </div>
                @endif

                <div class="auth-extra-links">
                    <a href="/login">Back to Login</a>
                </div>
            </form>
        </div>

        <div id="submitOverlay" class="auth-overlay-v2 hidden">
            <div class="auth-dialog-v2">
                <div id="overlaySpinner" class="auth-spinner-v2"></div>
                <div id="overlaySuccess" class="success-icon" style="display:none;">✅</div>
                <div id="overlayMessage" class="message">Resetting password...</div>
            </div>
        </div>

        <!-- Overlay for Slide Effect -->
        <div class="auth-overlay-container">
            <div class="auth-overlay">
                <div class="auth-overlay-panel auth-overlay-left">
                    <h2>Welcome Back!</h2>
                    <p>After resetting your password, you can sign in to your account.</p>
                    <a href="/login" class="auth-button auth-ghost">Go to Login</a>
                </div>
                <div class="auth-overlay-panel auth-overlay-right">
                    <h2>Secure Your Account</h2>
                    <p>Create a strong password to protect your account from unauthorized access.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('resetForm').addEventListener('submit', function() {
            const overlay = document.getElementById('submitOverlay');
            const spinner = document.getElementById('overlaySpinner');
            const message = document.getElementById('overlayMessage');
            
            overlay.classList.remove('hidden');
            spinner.style.display = 'block';
            message.textContent = 'Resetting password...';
        });
    </script>
</body>
</html>
