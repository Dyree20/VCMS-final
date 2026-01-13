<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Auth - Login & Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                icon.innerHTML = '<i class="fa-solid fa-circle-xmark"></i>';
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
    <style>
        .back-button-container {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }
        
        .back-button {
            background: rgba(255, 255, 255, 0.98);
            border: none;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 22px;
            color: #4361ee;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.12);
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .back-button:hover {
            background: white;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.18);
            transform: scale(1.1);
        }
        
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
    </style>

</head>
<body>
    <div class="back-button-container">
        <a href="{{ route('home') }}" class="back-button" title="Back to Home">
            <i class="fas fa-home"></i>
        </a>
    </div>
    
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
            <form action="{{ route('account.register') }}" id="registerForm" method="POST" class="auth-form">
                @csrf
                <h2>Create Account</h2>
                <input type="text" class="auth-input" placeholder="First Name" name="f_name" required>
                <input type="text" class="auth-input" placeholder="Last Name" name="l_name" required>
                <input type="email" class="auth-input" placeholder="Email" name="email" required>
                <input type="text" class="auth-input" placeholder="Username" name="username" required>
                <input type="password" class="auth-input" placeholder="Password" name="password" required>
                <input type="password" class="auth-input" placeholder="Confirm Password" name="password_confirmation" required>

                <button type="submit" class="auth-button">Register</button>
                <div class="auth-extra-links">
                    <a href="#" class="back-to-login-link">Already have an account? Login</a>
                </div>
            </form>
        </div>

        <!-- Login Form -->
        <div class="auth-form-container auth-login-container">
            <form action="{{ route('account.login') }}" method="POST" id="loginForm" class="auth-form">
                @csrf
                <div class="auth-logo-bar">
                    <img src="/images/VCMSlogo.png" alt="Auth Logo">
                </div>
                <h2>Sign In</h2>
                <input type="text" class="auth-input" placeholder="Username or Email" name="login" required>
                <div class="password-input-wrapper">
                    <input type="password" class="auth-input password-input" placeholder="Password" name="password" required>
                    <button type="button" class="password-toggle-btn" aria-label="Toggle password visibility">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <button type="submit" class="auth-button">Login</button>
                <div id="loginStatusOverlay" class="auth-overlay-v2 hidden">
                    <div class="auth-dialog-v2">
                        <div id="loginStatusIcon" class="success-icon"><i class="fa-solid fa-circle-xmark"></i></div>
                        <div id="loginStatusMessage" class="message"></div>
                    </div>
                </div>
                <div class="auth-extra-links">
                    <a href="{{ route('forgot-password.form') }}">Forgot password?</a>
                </div>
                <button type="button" class="mobile-register-button" onclick="window.location.href='{{ route('account.form') }}'">
                     Don't have an account? Register
                </button>
            </form>
        </div>

        <!-- Register CTA Panel (visible on mobile) -->
        <div class="auth-register-cta-panel">
            <h2 class="cta-title">Hello, Staff!</h2>
            <p class="cta-subtitle">New to Auth System? Enter your details and start your journey.</p>
            <button type="button" class="auth-button auth-ghost cta-toggle-btn" id="ctaToggleBtn">Register</button>
        </div>

        <!-- <div id="submitOverlay" class="overlay hidden">
            <div class="overlay-card">
                <div id="overlaySpinner" class="spinner"></div>
                <div id="overlaySuccess" class="success-icon"><i class="fa-solid fa-check-circle"></i></div>
                <div id="overlayMessage" class="message">Saving...</div>
                <div id="overlaySub" class="sub"></div>
            </div>
            </div> -->

        <div id="submitOverlay" class="auth-overlay-v2 hidden">
            <div class="auth-dialog-v2">
                <div id="overlaySpinner" class="auth-spinner-v2"></div>
                <div id="overlaySuccess" class="success-icon"><i class="fa-solid fa-circle-check"></i></div>
                <div id="overlayMessage" class="message">Saving...</div>
                <div id="overlaySub" class="sub"></div>
            </div>
        </div>

        <div id="loginOverlay" class="auth-overlay-v2 hidden">
            <div class="auth-dialog-v2">
                <div id="loginSpinner" class="auth-spinner-v2"></div>
                <div id="loginSuccess" class="auth-success-v2" style="display:none;"><i class="fa-solid fa-circle-check"></i></div>
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
        const ctaPanelRegisterBtn = document.querySelector('.auth-register-cta-panel .auth-button');
        const ctaToggleBtn = document.getElementById('ctaToggleBtn');
        const ctaTitle = document.querySelector('.cta-title');
        const ctaSubtitle = document.querySelector('.cta-subtitle');
        const mobileRegisterBtn = document.querySelector('.mobile-register-button');
        const backToLoginLink = document.querySelector('.back-to-login-link');

        // Function to show register form
        function showRegisterForm() {
            authContainer.classList.add('auth-active');
            updateCtaPanel(true);
        }

        // Function to show login form
        function showLoginForm() {
            authContainer.classList.remove('auth-active');
            updateCtaPanel(false);
        }

        // Function to update CTA panel based on active state
        function updateCtaPanel(isRegisterActive) {
            if (ctaToggleBtn) {
                if (isRegisterActive) {
                    ctaToggleBtn.textContent = 'Back to Login';
                    ctaTitle.textContent = 'Already have an account?';
                    ctaSubtitle.textContent = 'Return to the login page to access your account.';
                } else {
                    ctaToggleBtn.textContent = 'Register';
                    ctaTitle.textContent = 'Hello, Staff!';
                    ctaSubtitle.textContent = 'New to Auth System? Enter your details and start your journey.';
                }
            }
        }

        // Desktop overlay buttons
        authSignUpBtn.addEventListener('click', showRegisterForm);
        authSignInBtn.addEventListener('click', showLoginForm);

        // CTA toggle button - use ID selector only for reliability
        const ctaBtn = document.getElementById('ctaToggleBtn');
        
        if (ctaBtn) {
            // Clear any previous handlers
            ctaBtn.onclick = null;
            
            // Single, simple event listener
            ctaBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('CTA button clicked - current state:', authContainer.classList.contains('auth-active'));
                
                if (authContainer.classList.contains('auth-active')) {
                    showLoginForm();
                    console.log('Showing login form');
                } else {
                    showRegisterForm();
                    console.log('Showing register form');
                }
                
                return false;
            });
            
            console.log('CTA button event listener attached successfully');
        } else {
            console.error('CTA button with ID ctaToggleBtn not found!');
        }

        // Mobile CTA panel register button - trigger swipe instead of redirect
        if (ctaPanelRegisterBtn) {
            ctaPanelRegisterBtn.addEventListener('click', (e) => {
                e.preventDefault();
                showRegisterForm();
            });
        }

        // Mobile register button text - trigger swipe
        if (mobileRegisterBtn) {
            mobileRegisterBtn.addEventListener('click', (e) => {
                e.preventDefault();
                showRegisterForm();
            });
        }

        // Back to login link in register form - with better event handling
        if (backToLoginLink) {
            backToLoginLink.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                showLoginForm();
                return false;
            });
        } else {
            console.warn('Back to login link not found');
        }

        // Password Toggle Functionality
        document.querySelectorAll('.password-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const input = this.parentElement.querySelector('.password-input');
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                    this.classList.add('visible');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    this.classList.remove('visible');
                }
            });
        });
    </script>

</body>
</html>

