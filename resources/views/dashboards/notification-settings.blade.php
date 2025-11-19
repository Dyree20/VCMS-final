@extends('dashboards.enforcer')

@section('content')
<div class="section-container">
    <a href="{{ route('enforcer.profile') }}" class="back-btn">← Back to Profile</a>
    
    <h2>Notification Settings</h2>
    
    <form action="{{ route('notification.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="settings-group">
            <h3>Email Notifications</h3>
            
            <div class="setting-item">
                <div class="setting-info">
                    <label><input type="checkbox" name="email_clamping" value="1" checked> New Clamping Alerts</label>
                    <p>Receive email when a new clamping is recorded</p>
                </div>
            </div>

            <div class="setting-item">
                <div class="setting-info">
                    <label><input type="checkbox" name="email_payment" value="1" checked> Payment Notifications</label>
                    <p>Receive email when a payment is processed</p>
                </div>
            </div>

            <div class="setting-item">
                <div class="setting-info">
                    <label><input type="checkbox" name="email_status" value="1" checked> Status Updates</label>
                    <p>Receive email for account status changes</p>
                </div>
            </div>
        </div>

        <div class="settings-group">
            <h3>In-App Notifications</h3>
            
            <div class="setting-item">
                <div class="setting-info">
                    <label><input type="checkbox" name="app_alerts" value="1" checked> Push Alerts</label>
                    <p>Receive push notifications in the app</p>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="auth-button">Save Settings</button>
            <a href="{{ route('enforcer.profile') }}" class="auth-button" style="background: #999;">Cancel</a>
        </div>
    </form>
</div>

<style>
    .section-container {
        max-width: 600px;
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
    .settings-group {
        margin-bottom: 30px;
    }
    .settings-group h3 {
        margin-bottom: 16px;
        color: #333;
        font-size: 16px;
    }
    .setting-item {
        padding: 12px;
        border: 1px solid #eee;
        border-radius: 8px;
        margin-bottom: 10px;
        background: #fafafa;
    }
    .setting-info label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        margin: 0;
        font-weight: 600;
    }
    .setting-info p {
        margin: 6px 0 0 24px;
        font-size: 13px;
        color: #666;
    }
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    .form-actions .auth-button {
        flex: 1;
    }
</style>
@endsection
