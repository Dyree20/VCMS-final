<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VCMS - Parking Violation Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/VCMSlogo.png') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
        }

        /* Header/Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 22px;
            font-weight: 700;
            color: #4361ee;
        }

        .navbar-logo img {
            height: 40px;
            width: 40px;
        }

        .navbar-actions {
            display: flex;
            gap: 15px;
        }

        .btn-nav {
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-login-nav {
            background: transparent;
            color: #4361ee;
            border: 2px solid #4361ee;
        }

        .btn-login-nav:hover {
            background: #4361ee;
            color: white;
        }

        .btn-register-nav {
            background: linear-gradient(135deg, #4e5de3, #4361ee);
            color: white;
        }

        .btn-register-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
        }

        /* Hero Section */
        .hero {
            background-image: url('/images/bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 55, 175, 0.7);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 700px;
            padding: 40px;
        }

        .hero-logo {
            height: 80px;
            width: auto;
            max-width: 80px;
            margin: 0 auto 30px;
            object-fit: contain;
        }

        .hero h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 40px;
            opacity: 0.95;
            line-height: 1.6;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-hero {
            padding: 14px 40px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-hero-primary {
            background: white;
            color: #4361ee;
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .btn-hero-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-hero-secondary:hover {
            background: white;
            color: #4361ee;
            transform: translateY(-3px);
        }

        /* Features Section */
        .section {
            padding: 80px 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 36px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 50px;
            color: #333;
        }

        .section-subtitle {
            font-size: 16px;
            color: #666;
            text-align: center;
            margin-bottom: 30px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            font-size: 48px;
            color: #4361ee;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 12px;
            color: #333;
        }

        .feature-card p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }

        /* About Section */
        .about {
            background: linear-gradient(135deg, #f8f9ff, #f0f3ff);
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }

        .about-text h2 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #333;
        }

        .about-text p {
            color: #666;
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 15px;
        }

        .about-list {
            list-style: none;
            margin-top: 30px;
        }

        .about-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
            font-size: 15px;
            color: #555;
        }

        .about-list i {
            color: #4361ee;
            font-size: 20px;
        }

        .about-image {
            text-align: center;
        }

        .about-image img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
        }

        /* FAQ Section */
        .faq {
            background: white;
        }

        .faq-item {
            background: #f8f9ff;
            border-radius: 8px;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .faq-question {
            padding: 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: #333;
            transition: all 0.3s ease;
            background: #f8f9ff;
        }

        .faq-question:hover {
            background: #eef1ff;
        }

        .faq-question i {
            color: #4361ee;
            transition: transform 0.3s ease;
        }

        .faq-question.active i {
            transform: rotate(180deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            color: #666;
            font-size: 14px;
        }

        .faq-answer.active {
            max-height: 300px;
            padding: 0 20px 20px 20px;
        }

        /* Footer */
        .footer {
            background: #2a2d3a;
            color: white;
            padding: 60px 40px 30px;
            text-align: center;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto 40px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            text-align: left;
        }

        .footer-section h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .footer-section p,
        .footer-section a {
            font-size: 14px;
            color: #ccc;
            line-height: 1.8;
            display: block;
            margin-bottom: 8px;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: #4361ee;
        }

        .footer-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 30px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }

            .hero h1 {
                font-size: 32px;
            }

            .hero p {
                font-size: 16px;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .btn-hero {
                width: 100%;
                justify-content: center;
            }

            .section {
                padding: 50px 20px;
            }

            .section-title {
                font-size: 28px;
            }

            .about-content {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .feature-card {
                padding: 20px;
            }

            .footer-content {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-logo">
            <img src="{{ asset('images/VCMSlogo.png') }}" alt="VCMS">
            <span>VCMS</span>
        </div>
        <div class="navbar-actions">
            <a href="{{ route('account.form') }}" class="btn-nav btn-login-nav">Login</a>
            <a href="{{ route('account.form') }}" class="btn-nav btn-register-nav">Create Account</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <img src="{{ asset('images/VCMSlogo.png') }}" alt="VCMS Logo" class="hero-logo">
            <h1>Vehicle Clamping Management System</h1>
            <p>Streamline parking enforcement and violation management with our comprehensive platform. Efficient tracking, real-time updates, and seamless payment processing.</p>
            <div class="hero-buttons">
                <a href="{{ route('account.form') }}" class="btn-hero btn-hero-primary">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="{{ route('account.form') }}" class="btn-hero btn-hero-secondary">
                    <i class="fas fa-user-plus"></i> Create Account
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section">
        <h2 class="section-title">Why Choose VCMS?</h2>
        <p class="section-subtitle">Our platform provides comprehensive tools for efficient parking violation management</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-map-location-dot"></i>
                </div>
                <h3>Real-Time Tracking</h3>
                <p>GPS-enabled location tracking for enforcers with live updates and comprehensive route optimization.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-screen"></i>
                </div>
                <h3>Mobile Friendly</h3>
                <p>Fully responsive design accessible on any device - smartphones, tablets, and desktops.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <h3>Secure Payments</h3>
                <p>Integrated payment processing with multiple payment options and secure transaction handling.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Analytics & Reports</h3>
                <p>Comprehensive reporting tools with detailed analytics for better decision-making.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Team Management</h3>
                <p>Efficient team coordination and management with role-based access control.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h3>Enterprise Security</h3>
                <p>Bank-level security with data encryption, secure authentication, and compliance standards.</p>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="section about">
        <div class="about-content">
            <div class="about-text">
                <h2>About VCMS</h2>
                <p>The Parking Violation Management System (VCMS) is a comprehensive platform designed to revolutionize parking enforcement and violation management.</p>
                <p>Our solution brings together modern technology and practical efficiency to help administrators, enforcers, and front-desk staff work seamlessly together.</p>
                <ul class="about-list">
                    <li><i class="fas fa-check-circle"></i> Streamlined violation tracking and management</li>
                    <li><i class="fas fa-check-circle"></i> Real-time GPS location tracking</li>
                    <li><i class="fas fa-check-circle"></i> Automated payment processing</li>
                    <li><i class="fas fa-check-circle"></i> Comprehensive audit logs</li>
                    <li><i class="fas fa-check-circle"></i> Advanced search and filtering</li>
                    <li><i class="fas fa-check-circle"></i> Team and zone management</li>
                </ul>
            </div>
            <div class="about-image">
                <img src="{{ asset('images/VCMSlogo.png') }}" alt="About VCMS" style="max-width: 300px; opacity: 0.8;">
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section">
        <h2 class="section-title">Frequently Asked Questions</h2>
        <div style="max-width: 800px; margin: 0 auto;">
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span>How long does it take to get my account approved?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    New accounts are typically reviewed within 24-48 hours. You'll receive an email notification once your account has been approved. In the meantime, you can update your profile information.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span>What can I do before my account is approved?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    While your account is pending approval, you can log in and access your profile section. Here you can complete your profile information, update your details, and manage your security settings. Full system access will be granted once approved.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span>What payment methods are accepted?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    We accept multiple payment methods including credit cards, debit cards, and online payment gateways. Our secure payment system ensures your transactions are protected with industry-standard encryption.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span>Is my data secure?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Yes, security is our top priority. We use bank-level encryption, secure authentication protocols, and regular security audits. All personal and financial data is protected and compliant with international standards.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span>How do I reset my password?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Click "Forgot Password" on the login page, enter your email address, and follow the instructions sent to your email. You'll receive a password reset link valid for 24 hours.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span>What devices can I use to access VCMS?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    VCMS is fully responsive and works on all modern devices including desktops, tablets, and smartphones. You can work from anywhere with an internet connection.
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3><i class="fas fa-info-circle"></i> About</h3>
                <p>VCMS is a comprehensive parking violation management system designed for efficiency and reliability.</p>
            </div>
            <div class="footer-section">
                <h3><i class="fas fa-link"></i> Quick Links</h3>
                <a href="{{ route('account.form') }}">Login</a>
                <a href="{{ route('account.form') }}">Register</a>
                <a href="#faq">FAQ</a>
                <a href="#about">About Us</a>
            </div>
            <div class="footer-section">
                <h3><i class="fas fa-headset"></i> Support</h3>
                <p>Email: support@vcms.com</p>
                <p>Phone: +1 (555) 123-4567</p>
                <p>Available 24/7 for assistance</p>
            </div>
        </div>
        <div class="footer-divider">
            <p>&copy; 2025 VCMS - Parking Violation Management System. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function toggleFAQ(element) {
            const faqItem = element.parentElement;
            const answer = faqItem.querySelector('.faq-answer');
            
            // Close other open FAQs
            document.querySelectorAll('.faq-question').forEach(q => {
                if (q !== element) {
                    q.classList.remove('active');
                    q.parentElement.querySelector('.faq-answer').classList.remove('active');
                }
            });
            
            // Toggle current FAQ
            element.classList.toggle('active');
            answer.classList.toggle('active');
        }
    </script>
</body>
</html>