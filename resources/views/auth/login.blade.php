<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Auth - Login & Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/../../styles/authentication.css">
    <link rel="stylesheet" href="/../../styles/auth-overlay.css">
    <script src="/../../js/registration.js" defer></script>
    <script src="/../../js/login.js" defer></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any())
            var overlay = document.getElementById('loginStatusOverlay');
            var icon = document.getElementById('loginStatusIcon');
            var msg = document.getElementById('loginStatusMessage');
            if (overlay && icon && msg) {
                icon.textContent = '❌';
                icon.style.color = 'red';
                msg.textContent = @json($errors->first('login'));
                overlay.classList.remove('hidden');
                setTimeout(function() {
                    overlay.classList.add('hidden');
                }, 4000);
            }
        @endif
    });
    </script>
    <link rel="stylesheet" href="/../../styles/overlay.css">

</head>
<body>
    <div class="auth-mobile-logo-bar">
        <img src="/images/VCMSlogo.png" alt="Auth Logo">
    </div>
    <div class="auth-container" id="auth-container">
        <div class="auth-background"></div>
        <div class="auth-bg-logo">
            <img src="/images/VCMSlogo.png" alt="logo">
        </div>

        <!-- Registration Form -->
        <div class="auth-form-container auth-register-container">
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
                </div>
            </form>
        </div>

        <!-- Login Form -->
        <div class="auth-form-container auth-login-container">
            <form action="{{ route('login') }}" method="POST" id="loginForm" class="auth-form">
                @csrf
                <div class="auth-logo-bar">
                    <img src="/images/VCMSlogo.png" alt="Auth Logo">
                </div>
                <h2>Sign In</h2>
                <input type="text" class="auth-input" placeholder="Username or Email" name="login" required>
                <input type="password" class="auth-input" placeholder="Password" name="password" required>
                <button type="submit" class="auth-button">Login</button>
                <div id="loginStatusOverlay" class="auth-overlay-v2 hidden">
                    <div class="auth-dialog-v2">
                        <div id="loginStatusIcon" class="success-icon">❌</div>
                        <div id="loginStatusMessage" class="message"></div>
                    </div>
                </div>
                <div class="auth-extra-links">
                    <a href="{{ route('forgot-password.form') }}">Forgot password?</a>
                </div>
                <button type="button" class="mobile-register-button" onclick="window.location.href='/register'">
                     Don't have an account? Register
                </button>
            </form>
        </div>

        <!-- Register CTA Panel (visible on mobile) -->
        <div class="auth-register-cta-panel">
            <h2>Hello, Staff!</h2>
            <p>New to Auth System? Enter your details and start your journey.</p>
            <a href="/register" class="auth-button auth-ghost">Register</a>
        </div>

        <!-- <div id="submitOverlay" class="overlay hidden">
            <div class="overlay-card">
                <div id="overlaySpinner" class="spinner"></div>
                <div id="overlaySuccess" class="success-icon">✅</div>
                <div id="overlayMessage" class="message">Saving...</div>
                <div id="overlaySub" class="sub"></div>
            </div>
            </div> -->

        <div id="submitOverlay" class="auth-overlay-v2 hidden">
            <div class="auth-dialog-v2">
                <div id="overlaySpinner" class="auth-spinner-v2"></div>
                <div id="overlaySuccess" class="success-icon">✅</div>
                <div id="overlayMessage" class="message">Saving...</div>
                <div id="overlaySub" class="sub"></div>
            </div>
        </div>

        <div id="loginOverlay" class="auth-overlay-v2 hidden">
            <div class="auth-dialog-v2">
                <div id="loginSpinner" class="auth-spinner-v2"></div>
                <div id="loginSuccess" class="auth-success-v2" style="display:none;">✅</div>
                <div id="loginMessage" class="auth-message-v2">Logging in...</div>
                <div id="loginSub" class="auth-sub-v2"></div>
            </div>
        </div>

        <!-- Overlay for Slide Effect -->
        <div class="auth-overlay-container">
        <div class="auth-overlay">
            <div class="auth-overlay-panel auth-overlay-left">
            <h2>Welcome!</h2>
            <p>Already have an account? Sign in to keep managing operations.</p>
            <button class="auth-button auth-ghost" id="auth-signIn">Login</button>
            </div>
            <div class="auth-overlay-panel auth-overlay-right">
            <h2>Hello, Staff!</h2>
            <p>New to Auth System? Enter your details and start your journey.</p>
            <button class="auth-button auth-ghost" id="auth-signUp">Register</button>
            </div>
        </div>
        </div>
    </div>

    <script>
        const authContainer = document.getElementById('auth-container');
        const authSignUpBtn = document.getElementById('auth-signUp');
        const authSignInBtn = document.getElementById('auth-signIn');

        authSignUpBtn.addEventListener('click', () => authContainer.classList.add('auth-active'));
        authSignInBtn.addEventListener('click', () => authContainer.classList.remove('auth-active'));
    </script>

</body>
</html>

