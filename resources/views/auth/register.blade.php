<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        <div class="password-input-wrapper">
                            <input type="password" class="auth-input password-input" placeholder="Password" name="password" required>
                            <button type="button" class="password-toggle-btn" aria-label="Toggle password visibility">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-input-wrapper">
                            <input type="password" class="auth-input password-input" placeholder="Confirm Password" name="password_confirmation" required>
                            <button type="button" class="password-toggle-btn" aria-label="Toggle password visibility">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
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
                <div id="overlaySuccess" class="success-icon"><i class="fa-solid fa-circle-check"></i></div>
                <div id="overlayMessage" class="message">Saving...</div>
                <div id="overlaySub" class="sub"></div>
            </div>
        </div>
    </div>

    <!-- small script to handle back to login on this page -->
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            // nothing needed, registration.js will handle submission and show overlays

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
        });
    </script>
</body>
</html>
