@extends('dashboards.enforcer')

@section('content')
<div class="section-container">
    <a href="{{ route('enforcer.profile') }}" class="back-btn">← Back to Profile</a>
    
    <h2>Help & FAQs</h2>
    
    <div class="faq-section">
        <div class="faq-item">
            <h3>What is the Clamping Management System?</h3>
            <p>The Clamping Management System is a platform designed to efficiently manage vehicle clamping operations, track cases, process payments, and maintain records of enforcement activities.</p>
        </div>

        <div class="faq-item">
            <h3>How do I report a clamping issue?</h3>
            <p>You can report any issues by visiting the "Contact Us" section in your profile menu. Our support team will assist you within 24 hours.</p>
        </div>

        <div class="faq-item">
            <h3>How are payment records maintained?</h3>
            <p>All payment records are automatically logged in your Transactions History. You can view payment status, dates, and amounts at any time.</p>
        </div>

        <div class="faq-item">
            <h3>How do I update my profile information?</h3>
            <p>Click on "Edit Profile" from your profile menu to update your personal information, contact details, and profile photo.</p>
        </div>

        <div class="faq-item">
            <h3>What should I do if I forget my password?</h3>
            <p>On the login page, click "Forgot password?" and follow the instructions to reset your password via email.</p>
        </div>

        <div class="faq-item">
            <h3>How do I manage my notification preferences?</h3>
            <p>Go to "Notification Settings" to customize which types of notifications you receive via email or in-app alerts.</p>
        </div>

        <div class="faq-item">
            <h3>Who do I contact for technical support?</h3>
            <p>For technical issues, please use the "Contact Us" form or email support@clampingsystem.com. Our team responds within 24 hours.</p>
        </div>

        <div class="faq-item">
            <h3>Is my personal information secure?</h3>
            <p>Yes, we use industry-standard encryption and security protocols to protect your personal and financial information.</p>
        </div>
    </div>
</div>

<style>
    .section-container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
    }
    .back-btn {
        display: inline-block;
        margin-bottom: 20px;
        color: #4e5de3;
        text-decoration: none;
        font-weight: 600;
    }
    .faq-section {
        margin-top: 20px;
    }
    .faq-item {
        padding: 16px;
        margin-bottom: 12px;
        background: #f9f9f9;
        border-left: 4px solid #4e5de3;
        border-radius: 4px;
    }
    .faq-item h3 {
        margin: 0 0 10px 0;
        color: #333;
        font-size: 15px;
        font-weight: 600;
    }
    .faq-item p {
        margin: 0;
        color: #666;
        font-size: 14px;
        line-height: 1.5;
    }
</style>
@endsection
