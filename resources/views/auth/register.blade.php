<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/../../styles/authentication.css">
    <link rel="stylesheet" href="/../../styles/auth-overlay.css">
    <link rel="stylesheet" href="/../../styles/overlay.css">
    <script src="/../../js/registration.js" defer></script>
</head>
<body>


    <div class="auth-container register-page" id="auth-container" style="height:auto;">
        <div class="auth-background"></div>
        <!-- Only one faint background logo, not in normal flow -->
        <div class="auth-bg-logo">
            <img src="/images/VCMSlogo.png" alt="logo">
        </div>

        <div class="register-inner">
            <div class="register-hero">
                <div class="hero-content">
                    <h2>Welcome to Clamping Management System</h2>
                    <p>Manage clamping operations efficiently and effectively with our user-friendly platform.</p>
                
                </div>
            </div>
            <div class="register-panel">
                <div class="auth-register-container">
                    <form action="{{ route('register') }}" id="registerForm" method="POST" class="auth-form">
                        @csrf
                        <h2>Create Account</h2>
                        <input type="text" class="auth-input" placeholder="First Name" name="f_name" required>
                        <input type="text" class="auth-input" placeholder="Last Name" name="l_name" required>
                        <input type="email" class="auth-input" placeholder="Email" name="email" required>
                        <input type="text" class="auth-input" placeholder="Username" name="username" required>
                        <input type="password" class="auth-input" placeholder="Password" name="password" required>
                        <input type="password" class="auth-input" placeholder="Confirm Password" name="password_confirmation" required>
                        <select name="role_id" class="auth-input" required>
                            <option value="" disabled selected>Select Role</option>
                            <option value="1">Admin</option>
                            <option value="2">Enforcer</option>
                            <option value="3">Front Desk</option>
                        </select>
                        <button type="submit" class="auth-button">Register</button>
                        <div class="auth-extra-links">
                            <a href="/login">Already have an account? Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="submitOverlay" class="auth-overlay-v2 hidden">
            <div class="auth-dialog-v2">
                <div id="overlaySpinner" class="auth-spinner-v2"></div>
                <div id="overlaySuccess" class="success-icon">✅</div>
                <div id="overlayMessage" class="message">Saving...</div>
                <div id="overlaySub" class="sub"></div>
            </div>
        </div>
    </div>

    <!-- small script to handle back to login on this page -->
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            // nothing needed, registration.js will handle submission and show overlays
        });
    </script>
</body>
</html>
