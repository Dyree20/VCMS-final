# Real-Time Vehicle Tracking System - Local Network Demo Setup

## Overview

This is a **Vehicle Clamping Management System** with **real-time geolocation tracking** configured for demonstration purposes on a **local network** (XAMPP/localhost).

### Key Features:

✅ **Real-Time Tracking Dashboard** - See all officers and their locations on a live map  
✅ **Multi-Device Support** - Track multiple officers from different devices on the same network  
✅ **Local Network Demo** - Works without internet, only requires devices on same WiFi  
✅ **Automatic Location Updates** - Devices send GPS/location data every 10 seconds  
✅ **Status Management** - Officers can be Online, Offline, or On Break  

---

## Setup Instructions

### 1. **Get Your Machine's Local IP Address**

Open PowerShell and run:
```powershell
ipconfig
```

Look for **IPv4 Address** (usually something like `192.168.x.x`)  
Example: `192.168.1.10`

### 2. **Start the Laravel Development Server**

```bash
php artisan serve --host=192.168.1.10 --port=8000
```

Replace `192.168.1.10` with your actual IP address.

### 3. **Access on Your Network**

All devices on the same WiFi can now access the system:
```
http://192.168.1.10:8000
```

---

## How to Use

### **For Enforcement Officers (Mobile/Tablet Devices)**

1. **Login** with an officer account
2. **Look for** the floating location tracking widget (bottom-right corner)
3. **Click "Start"** to begin sharing location
4. Location updates automatically every 10 seconds
5. **Status indicators**:
   - 🟢 **Green** = Actively tracking
   - ⚫ **Gray** = Not tracking
   - 🟡 **Yellow** = On break

### **For Administrators (Desktop Dashboard)**

1. **Login** with admin account
2. **Navigate** to: `/gps/dashboard` or `/gps/vehicle-tracking`
3. **View the map** showing all active officers
4. **See officer list** on the right with:
   - Officer name
   - Current status (Online/Offline/Break)
   - Last update time
5. **Click any officer** to center map and show details
6. **Auto-refresh** enabled by default (updates every 5 seconds)

---

## Testing with Multiple Devices

### Scenario 1: Simulate Multiple Officers on One Machine

```bash
# Terminal 1: Start the server
php artisan serve --host=192.168.1.10 --port=8000

# Terminal 2: Open in one browser window (Officer 1)
http://192.168.1.10:8000/login

# Terminal 3: Open in another browser/tab (Officer 2)
http://192.168.1.10:8000/login

# Then open admin dashboard in another window
http://192.168.1.10:8000/gps/dashboard
```

### Scenario 2: Use Real Devices on Network

1. **Officer Tablets/Phones**: Open the app on multiple devices
   ```
   http://192.168.1.10:8000
   ```

2. **Admin Desktop/Laptop**: Open dashboard
   ```
   http://192.168.1.10:8000/gps/dashboard
   ```

3. Login officers on each device → Locations appear on admin dashboard

---

## Real-Time Location Features

### **Browser Geolocation (Automatic)**

If device has GPS/location capabilities:
- Uses device's built-in GPS
- Shows accuracy radius (in meters)
- Updates position every 10 seconds
- Shows address (when available)

### **Demo Mode (No GPS)**

If device doesn't have GPS:
- System allows manual testing
- Officers can still show as "Online" with default location
- View tracking infrastructure without real GPS

### **Location Update Flow**

```
Officer Device                      Server                    Admin Dashboard
     |                               |                              |
     | Browser asks for location     |                              |
     |----permission needed--------->|                              |
     |                               |                              |
     | Sends GPS coordinates         |                              |
     |----POST /gps/update---------->|                              |
     |       (lat, lon, accuracy)    |                              |
     |                               | Stores in database           |
     |                               |                              |
     |                               | Broadcasts update            |
     |                               |----------to dashboard------->|
     |                               |                              |
     |                               |     Map refreshes, shows     |
     |                               |     officer location         |
     |                               |                              |
```

---

## Database Schema

### **enforcer_locations** Table

```
id                  - Primary key
user_id            - Officer ID
latitude           - GPS latitude (8 decimals)
longitude          - GPS longitude (8 decimals)
address            - Reverse geocoded address
accuracy_meters    - GPS accuracy radius
status             - online | offline | on_break
created_at         - Timestamp
updated_at         - Timestamp
```

### **users** Table (Extended)

```
location_tracking_enabled - Boolean (track this officer?)
```

---

## API Endpoints

### **For Officers (Location Submission)**

**POST** `/gps/update-location`
```json
{
  "latitude": 14.5995,
  "longitude": 121.0012,
  "accuracy": 25,
  "address": "Sample Location"
}
```

**POST** `/gps/set-status`
```json
{
  "status": "online" | "offline" | "on_break"
}
```

### **For Admins (View Locations)**

**GET** `/gps/online-enforcers`
- Returns only officers with updates in last 30 seconds

**GET** `/gps/recent-enforcers`
- Returns officers with updates in last 5 minutes

**GET** `/gps/all-enforcers`
- Returns all officers with their latest location

**GET** `/gps/location-history/{user}`
- Get 24 hours of location history for one officer

---

## JavaScript Location Tracker Module

### **Automatic (Loaded for all enforcers)**

The `location-tracker.js` module automatically loads for all enforcer users and provides a floating widget.

### **Manual Control (JavaScript Console)**

```javascript
// Start tracking
LocationTracker.start();

// Stop tracking
LocationTracker.stop();

// Get current status
const status = LocationTracker.getStatus();
console.log(status);

// Send location immediately
LocationTracker.sendLocation();
```

### **Floating Widget Controls**

- **Status Indicator** - Green/Gray circle shows tracking state
- **Start/Stop Button** - Toggle location sharing
- **Details Button** - Shows coordinates and accuracy

---

## Demo Scenarios

### **Scenario 1: Morning Shift Start**

1. Officers login on tablets
2. Click "Start" on location widget
3. Admin watches dashboard as officers appear on map
4. See real-time positions update every 10 seconds

### **Scenario 2: Route Tracking**

1. Officer walks/drives around parking area
2. System records trail of locations
3. Admin can see historical route on map
4. Review location history via API

### **Scenario 3: Break Time**

1. Officer clicks dropdown → Select "On Break"
2. Map marker changes to yellow
3. Officer list shows status as "On Break"
4. Returns to "Online" when resuming

### **Scenario 4: Offline Detection**

1. Officer closes browser or goes offline
2. No new location updates sent
3. Admin dashboard shows officer as "Offline"
4. Gray marker indicates offline status

---

## Troubleshooting

### **Locations Not Appearing on Dashboard**

```bash
# 1. Check if data exists in database
php artisan tinker
>>> DB::table('enforcer_locations')->limit(5)->get();

# 2. Verify officer has location tracking enabled
>>> User::find(1)->location_tracking_enabled;

# 3. Test API endpoint manually
curl -H "Accept: application/json" http://192.168.1.10:8000/gps/online-enforcers
```

### **Location Widget Not Showing**

- Verify you're logged in as an Enforcer
- Check browser console for JavaScript errors
- Ensure `public/js/location-tracker.js` exists

### **Permission Denied (Geolocation)**

- Browser needs HTTPS or localhost to access location
- XAMPP on local IP (`http://192.168.x.x`) should work
- On some browsers, may need to explicitly grant permission

### **Locations Not Updating**

- Check if officer has "location tracking" enabled in profile
- Verify CSRF token is present in page
- Check browser console Network tab for 403/500 errors
- Ensure database connection is working

---

## Demo Checklist

- [ ] Server running: `php artisan serve --host=192.168.1.10`
- [ ] Database seeded with test users
- [ ] Can access at: `http://192.168.1.10:8000`
- [ ] Can login as Admin
- [ ] Can login as Enforcer on different device/tab
- [ ] Enforcer location widget visible
- [ ] Can click "Start" on location widget
- [ ] Admin dashboard shows enforcer on map
- [ ] Location updates automatically every 10s
- [ ] Can filter officers by status
- [ ] Can search officers by name
- [ ] Auto-refresh toggle works
- [ ] Manual refresh button works

---

## Architecture

```
VCMS (Vehicle Clamping Management System)
├── Enforcers (Mobile/Tablet)
│   ├── Login
│   ├── Location Tracking Widget
│   └── Send GPS every 10s
│
├── Backend (Laravel)
│   ├── POST /gps/update-location → EnforcerLocation table
│   ├── POST /gps/set-status → Update status
│   ├── GET /gps/online-enforcers → Active officers
│   └── Database: enforcer_locations table
│
└── Admin Dashboard (Desktop)
    ├── Leaflet.js Map
    ├── GET /gps/online-enforcers (5s refresh)
    ├── Real-time markers
    └── Officer list sidebar
```

---

## Notes for Demonstration

### **Local Network Only**

- System designed for local WiFi demonstration
- No internet required
- All devices must be on same network
- Works great with XAMPP

### **GPS Not Required**

- System works without real GPS
- Uses browser Geolocation API
- Falls back to last known location if GPS unavailable
- Can be tested in demo mode with default Manila coordinates

### **Security Notice**

- Locations stored in database (keep confidential)
- Only authenticated users can view
- CSRF tokens protect against unauthorized requests
- Location data is tied to user accounts

---

## Next Steps

To enhance the demo:

1. **Add Clamping Operations Tracking**
   - Associate locations with active clamping jobs
   - Show vehicle locations alongside officer locations

2. **Add Route Playback**
   - Replay officer movements throughout the day
   - Show which routes were most active

3. **Add Geofencing**
   - Alert when officer enters/exits parking zones
   - Verify officers stayed in assigned areas

4. **Add Analytics**
   - Time spent in each zone
   - Distance traveled
   - Officer efficiency metrics

---

For questions or issues, check the system's error logs:

```bash
tail -f storage/logs/laravel.log
```
