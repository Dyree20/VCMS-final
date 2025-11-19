<?php

namespace App\Http\Controllers;

use App\Models\DeviceManager;
use Illuminate\Http\Request;

class DeviceManagerController extends Controller
{
    /**
     * Show all devices for the authenticated user
     */
    public function index()
    {
        $devices = auth()->user()->devices()
            ->orderBy('last_activity_at', 'desc')
            ->get();

        $userRole = auth()->user()->role->name ?? 'Admin';

        return view('admin.devices.index', compact('devices', 'userRole'));
    }

    /**
     * Logout a specific device
     */
    public function logoutDevice(DeviceManager $device)
    {
        // Check if user owns this device
        if ($device->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $device->deactivate();

        return back()->with('success', 'Device logged out successfully');
    }

    /**
     * Logout all other devices
     */
    public function logoutAllOtherDevices()
    {
        $currentToken = session()->get('device_token');

        auth()->user()->devices()
            ->where('session_token', '!=', $currentToken)
            ->update(['is_active' => false]);

        return back()->with('success', 'All other devices have been logged out');
    }

    /**
     * Logout all devices including current
     */
    public function logoutAllDevices()
    {
        auth()->user()->devices()->update(['is_active' => false]);
        auth()->logout();

        return redirect('/login')->with('success', 'You have been logged out from all devices');
    }

    /**
     * Parse browser information from user agent
     */
    private static function parseBrowser($userAgent)
    {
        $browsers = [
            'Chrome' => 'Chrome',
            'Firefox' => 'Firefox',
            'Safari' => 'Safari',
            'Edge' => 'Edge',
            'Opera' => 'Opera',
            'IE' => 'Internet Explorer',
        ];

        foreach ($browsers as $key => $name) {
            if (stripos($userAgent, $key) !== false) {
                return $name;
            }
        }

        return 'Unknown';
    }

    /**
     * Parse OS information from user agent
     */
    private static function parseOS($userAgent)
    {
        $os = [
            'Windows NT 10.0' => 'Windows 10',
            'Windows NT 6.3' => 'Windows 8.1',
            'Windows NT 6.2' => 'Windows 8',
            'Windows NT 6.1' => 'Windows 7',
            'Windows' => 'Windows',
            'Mac OS X' => 'macOS',
            'iPhone' => 'iOS',
            'iPad' => 'iPadOS',
            'Android' => 'Android',
            'Linux' => 'Linux',
            'Ubuntu' => 'Ubuntu',
        ];

        foreach ($os as $key => $name) {
            if (stripos($userAgent, $key) !== false) {
                return $name;
            }
        }

        return 'Unknown';
    }

    /**
     * Parse device type from user agent
     */
    private static function parseDeviceType($userAgent)
    {
        if (preg_match('/mobile|android|iphone|ipod|blackberry|iemobile|opera mini/i', $userAgent)) {
            return 'Mobile';
        } elseif (preg_match('/tablet|ipad|playbook|silk/i', $userAgent)) {
            return 'Tablet';
        }

        return 'Desktop';
    }

    /**
     * Log device information on user login
     * Should be called from LoginController or middleware
     */
    public static function logDevice(Request $request, $user)
    {
        $userAgent = $request->userAgent();
        $browser = self::parseBrowser($userAgent);
        $os = self::parseOS($userAgent);
        $deviceType = self::parseDeviceType($userAgent);

        $deviceToken = \Str::random(64);
        session()->put('device_token', $deviceToken);

        $device = DeviceManager::create([
            'user_id' => $user->id,
            'device_name' => $browser . ' on ' . $os,
            'ip_address' => $request->ip(),
            'browser' => $browser,
            'os' => $os,
            'device_type' => $deviceType,
            'user_agent' => $userAgent,
            'session_token' => $deviceToken,
            'last_activity_at' => now(),
            'is_active' => true,
        ]);

        return $device;
    }

    /**
     * Update last activity for current device
     */
    public static function updateLastActivity()
    {
        $token = session()->get('device_token');
        if ($token && auth()->check()) {
            DeviceManager::where('session_token', $token)
                ->where('user_id', auth()->id())
                ->update(['last_activity_at' => now()]);
        }
    }
}
