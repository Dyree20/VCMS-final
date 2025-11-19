@extends('layouts.app')

@section('title', 'Manage Devices')

@section('content')
<div class="container-fluid" style="padding: 32px; max-width: 1200px; margin: 0 auto;">
    <!-- Header Section -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px;">
        <div>
            <h1 style="margin: 0; font-size: 28px; font-weight: 700; color: #333;">Device Manager</h1>
            <p style="margin: 8px 0 0 0; color: #666; font-size: 14px;">Manage your connected devices and sessions for security</p>
        </div>
        <a href="{{ route($userRole === 'Front Desk' ? 'front-desk.profile' : ($userRole === 'Enforcer' ? 'profile' : 'admin.profile')) }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #f0f0f0; color: #666; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s;">
            <i class="fa-solid fa-arrow-left"></i>
            Back to Profile
        </a>
    </div>

    @if(session('success'))
        <div style="background: #f0fdf4; border: 1px solid #dcfce7; color: #166534; padding: 16px; border-radius: 8px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <i class="fa-solid fa-check-circle" style="font-size: 18px;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fee; border: 1px solid #fecdd3; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <i class="fa-solid fa-circle-exclamation" style="font-size: 18px;"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Current Device Card -->
    @php
        $currentDevice = $devices->first(function($device) {
            return $device->isCurrentSession();
        });
    @endphp

    @if($currentDevice)
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 24px; margin-bottom: 32px; color: white; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                        <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            <i class="fa-solid fa-mobile"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 18px; font-weight: 700;">{{ $currentDevice->device_name }}</h3>
                            <p style="margin: 4px 0 0 0; opacity: 0.9; font-size: 13px;">This device (Current Session)</p>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 0;">
                        <div>
                            <p style="margin: 0; opacity: 0.8; font-size: 12px;">IP Address</p>
                            <p style="margin: 4px 0 0 0; font-size: 14px; font-weight: 600;">{{ $currentDevice->ip_address }}</p>
                        </div>
                        <div>
                            <p style="margin: 0; opacity: 0.8; font-size: 12px;">Last Activity</p>
                            <p style="margin: 4px 0 0 0; font-size: 14px; font-weight: 600;">{{ $currentDevice->getLastActivityFormatted() }}</p>
                        </div>
                        <div>
                            <p style="margin: 0; opacity: 0.8; font-size: 12px;">Browser</p>
                            <p style="margin: 4px 0 0 0; font-size: 14px; font-weight: 600;">{{ $currentDevice->browser ?? 'Unknown' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- All Devices Section -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #e9ecef;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #333;">
                <i class="fa-solid fa-list"></i>
                All Devices ({{ $devices->count() }})
            </h3>
            @if($devices->count() > 1)
                <form action="{{ route('devices.logout-all-others') }}" method="POST" style="display: inline;" onsubmit="return confirm('Logout all other devices? You will remain logged in on this device.');">
                    @csrf
                    <button type="submit" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; background: #fff3cd; color: #856404; border: 1px solid #ffc107; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 13px;">
                        <i class="fa-solid fa-sign-out-alt"></i>
                        Logout Other Devices
                    </button>
                </form>
            @endif
        </div>

        @if($devices->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e9ecef; background: #f9f9f9;">
                            <th style="padding: 12px; text-align: left; font-weight: 600; color: #666; font-size: 12px; text-transform: uppercase;">Device</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600; color: #666; font-size: 12px; text-transform: uppercase;">IP Address</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600; color: #666; font-size: 12px; text-transform: uppercase;">Browser & OS</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600; color: #666; font-size: 12px; text-transform: uppercase;">Last Activity</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600; color: #666; font-size: 12px; text-transform: uppercase;">Status</th>
                            <th style="padding: 12px; text-align: right; font-weight: 600; color: #666; font-size: 12px; text-transform: uppercase;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($devices as $device)
                            <tr style="border-bottom: 1px solid #e9ecef; transition: background 0.2s;">
                                <td style="padding: 16px 12px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 36px; height: 36px; background: #f0f0f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #2b58ff; font-size: 16px;">
                                            @if(str_contains(strtolower($device->device_type ?? ''), 'mobile'))
                                                <i class="fa-solid fa-mobile"></i>
                                            @elseif(str_contains(strtolower($device->device_type ?? ''), 'tablet'))
                                                <i class="fa-solid fa-tablet"></i>
                                            @else
                                                <i class="fa-solid fa-desktop"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: #333; font-size: 14px;">{{ $device->device_name }}</div>
                                            @if($device->isCurrentSession())
                                                <span style="display: inline-block; padding: 2px 8px; background: #d1e7dd; color: #0d5b2f; border-radius: 12px; font-size: 11px; font-weight: 600; margin-top: 4px;">Current</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 16px 12px; color: #666; font-size: 14px;">{{ $device->ip_address }}</td>
                                <td style="padding: 16px 12px; color: #666; font-size: 14px;">
                                    {{ $device->browser ?? 'Unknown' }}
                                    @if($device->os)
                                        <br><span style="color: #999; font-size: 12px;">{{ $device->os }}</span>
                                    @endif
                                </td>
                                <td style="padding: 16px 12px; color: #666; font-size: 14px;">
                                    {{ $device->getLastActivityFormatted() }}
                                    <br><span style="color: #999; font-size: 12px;">{{ $device->last_activity_at?->format('M d, Y h:i A') ?? 'Never' }}</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    @if($device->is_active)
                                        <span style="display: inline-block; padding: 6px 12px; background: #d1e7dd; color: #0d5b2f; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                            <i class="fa-solid fa-check-circle" style="margin-right: 4px;"></i>Active
                                        </span>
                                    @else
                                        <span style="display: inline-block; padding: 6px 12px; background: #f8d7da; color: #842029; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                            <i class="fa-solid fa-times-circle" style="margin-right: 4px;"></i>Inactive
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 16px 12px; text-align: right;">
                                    @if(!$device->isCurrentSession())
                                        <form action="{{ route('devices.logout', $device) }}" method="POST" style="display: inline;" onsubmit="return confirm('Logout this device?');">
                                            @csrf
                                            <button type="submit" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: #fee; color: #dc3545; border-radius: 6px; border: none; cursor: pointer; transition: all 0.3s; font-size: 14px;" title="Logout">
                                                <i class="fa-solid fa-sign-out-alt"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: #f0f0f0; color: #999; border-radius: 6px; font-size: 14px; cursor: not-allowed;" title="Current device">
                                            <i class="fa-solid fa-lock"></i>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding: 40px; text-align: center;">
                <i class="fa-solid fa-mobile" style="font-size: 48px; color: #ddd; display: block; margin-bottom: 16px;"></i>
                <p style="color: #999; margin: 0; font-size: 16px;">No devices registered yet</p>
            </div>
        @endif
    </div>

    <!-- Logout All Devices Section -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px; margin-top: 24px;">
        <h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 600; color: #333;">
            <i class="fa-solid fa-exclamation-triangle" style="color: #dc3545; margin-right: 8px;"></i>
            Danger Zone
        </h3>
        <p style="margin: 0 0 16px 0; color: #666; font-size: 14px;">Logout from all devices will log you out completely. You'll need to log in again on this device.</p>
        <form action="{{ route('devices.logout-all') }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure? You will be logged out from all devices and need to login again.');">
            @csrf
            <button type="submit" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: #dc3545; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 14px;">
                <i class="fa-solid fa-sign-out-alt"></i>
                Logout All Devices
            </button>
        </form>
    </div>
</div>

<style>
    table tbody tr:hover {
        background: #f9f9f9;
    }

    button:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
    }
</style>
@endsection
