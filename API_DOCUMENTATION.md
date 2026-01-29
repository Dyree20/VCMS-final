# Vehicle Tracking API Documentation

## Base URL
```
http://192.168.1.10:8000
```

All API endpoints require authentication (logged-in user).

---

## Location Tracking Endpoints

### 1. Update Enforcer Location
**Endpoint:** `POST /gps/update-location`

**Required:** Enforcer role with location tracking enabled

**Request Body:**
```json
{
  "latitude": 14.5995,
  "longitude": 121.0012,
  "accuracy": 25,
  "address": "Sample Street, Manila"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Location updated successfully",
  "location": {
    "id": 1,
    "user_id": 5,
    "latitude": 14.5995,
    "longitude": 121.0012,
    "address": "Sample Street, Manila",
    "accuracy_meters": 25,
    "status": "online",
    "created_at": "2026-01-29T10:30:00.000000Z",
    "updated_at": "2026-01-29T10:30:00.000000Z"
  }
}
```

---

### 2. Get Current Location
**Endpoint:** `GET /gps/current-location`

**Required:** Enforcer role

**Response:**
```json
{
  "id": 1,
  "user_id": 5,
  "latitude": 14.5995,
  "longitude": 121.0012,
  "address": "Sample Street, Manila",
  "accuracy_meters": 25,
  "status": "online",
  "created_at": "2026-01-29T10:30:00.000000Z",
  "updated_at": "2026-01-29T10:30:00.000000Z"
}
```

---

### 3. Set Enforcer Status
**Endpoint:** `POST /gps/set-status`

**Required:** Enforcer role

**Request Body:**
```json
{
  "status": "online"
}
```

**Status Values:**
- `online` - Officer is actively working
- `offline` - Officer is not on duty
- `on_break` - Officer is on break

**Response:**
```json
{
  "success": true,
  "message": "Status updated to: online"
}
```

---

## Admin Viewing Endpoints

### 4. Get Online Enforcers (Last 30 seconds)
**Endpoint:** `GET /gps/online-enforcers`

**Required:** Admin access

**Response:**
```json
{
  "success": true,
  "count": 3,
  "enforcers": [
    {
      "id": 10,
      "user_id": 5,
      "latitude": 14.5995,
      "longitude": 121.0012,
      "address": "Makati Avenue",
      "accuracy_meters": 25,
      "status": "online",
      "created_at": "2026-01-29T10:35:00.000000Z",
      "user": {
        "id": 5,
        "f_name": "John",
        "l_name": "Doe",
        "email": "john@example.com"
      }
    }
  ]
}
```

---

### 5. Get Recent Enforcers (Last 5 minutes)
**Endpoint:** `GET /gps/recent-enforcers`

**Required:** Admin access

**Query Parameters:**
- None (uses 5-minute window)

**Response:**
```json
{
  "success": true,
  "count": 5,
  "enforcers": [...],
  "timestamp": "2026-01-29T10:35:45.000000Z"
}
```

---

### 6. Get All Enforcers Latest Locations
**Endpoint:** `GET /gps/all-enforcers`

**Required:** Admin access

**Response:**
```json
{
  "success": true,
  "count": 10,
  "enforcers": [
    {
      "id": 10,
      "user_id": 5,
      "latitude": 14.5995,
      "longitude": 121.0012,
      "address": "Makati",
      "accuracy_meters": 25,
      "status": "online",
      "created_at": "2026-01-29T10:35:00.000000Z",
      "user": {
        "id": 5,
        "f_name": "John",
        "l_name": "Doe"
      }
    }
  ],
  "timestamp": "2026-01-29T10:35:45.000000Z"
}
```

---

## Location History & Analytics

### 7. Get Location History
**Endpoint:** `GET /gps/location-history/{user_id}`

**Required:** Admin access OR own user ID

**Query Parameters:**
- `hours` (optional, default: 24)
  - `1` - Last 1 hour
  - `4` - Last 4 hours
  - `8` - Last 8 hours
  - `24` - Last 24 hours

**Example:**
```
GET /gps/location-history/5?hours=24
```

**Response:**
```json
{
  "success": true,
  "count": 48,
  "locations": [
    {
      "id": 1,
      "user_id": 5,
      "latitude": 14.5995,
      "longitude": 121.0012,
      "address": "Makati Avenue",
      "accuracy_meters": 25,
      "status": "online",
      "created_at": "2026-01-29T10:30:00.000000Z"
    },
    {
      "id": 2,
      "user_id": 5,
      "latitude": 14.6000,
      "longitude": 121.0020,
      "address": "Paseo de Roxas",
      "accuracy_meters": 30,
      "status": "online",
      "created_at": "2026-01-29T10:31:00.000000Z"
    }
  ]
}
```

---

### 8. Get Location Analytics
**Endpoint:** `GET /gps/analytics/{user_id}`

**Required:** Admin access OR own user ID

**Query Parameters:**
- `hours` (optional, default: 24)

**Example:**
```
GET /gps/analytics/5?hours=24
```

**Response:**
```json
{
  "success": true,
  "enforcer": {
    "id": 5,
    "f_name": "John",
    "l_name": "Doe",
    "email": "john@example.com",
    "role": {
      "id": 3,
      "name": "Enforcer"
    }
  },
  "period_hours": 24,
  "location_count": 48,
  "total_distance_km": 25.45,
  "total_time_minutes": 720,
  "average_accuracy_m": 28.5,
  "max_distance_from_start_km": 8.2,
  "first_location": {
    "id": 1,
    "latitude": 14.5995,
    "longitude": 121.0012,
    "created_at": "2026-01-29T10:30:00.000000Z"
  },
  "last_location": {
    "id": 48,
    "latitude": 14.6100,
    "longitude": 121.0100,
    "created_at": "2026-01-30T10:30:00.000000Z"
  },
  "locations": [...]
}
```

---

## Error Responses

### 403 Forbidden
```json
{
  "error": "Only enforcers can update location"
}
```

### 404 Not Found
```json
{
  "error": "No location data found"
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "latitude": ["The latitude must be between -90 and 90."],
    "longitude": ["The longitude must be between -180 and 180."]
  }
}
```

---

## cURL Examples

### Send Location
```bash
curl -X POST http://192.168.1.10:8000/gps/update-location \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your_csrf_token" \
  -d '{
    "latitude": 14.5995,
    "longitude": 121.0012,
    "accuracy": 25,
    "address": "Sample Location"
  }'
```

### Get Online Enforcers
```bash
curl http://192.168.1.10:8000/gps/online-enforcers \
  -H "Accept: application/json"
```

### Get Location History
```bash
curl http://192.168.1.10:8000/gps/location-history/5?hours=24 \
  -H "Accept: application/json"
```

### Get Analytics
```bash
curl http://192.168.1.10:8000/gps/analytics/5?hours=24 \
  -H "Accept: application/json"
```

---

## JavaScript Examples

### Using Fetch API
```javascript
// Update location
fetch('/gps/update-location', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  },
  body: JSON.stringify({
    latitude: 14.5995,
    longitude: 121.0012,
    accuracy: 25,
    address: 'Sample Location'
  })
})
.then(r => r.json())
.then(data => console.log(data));
```

```javascript
// Get online enforcers
fetch('/gps/online-enforcers')
  .then(r => r.json())
  .then(data => {
    console.log(`${data.count} officers online`);
    data.enforcers.forEach(enforcer => {
      console.log(`${enforcer.user.f_name}: (${enforcer.latitude}, ${enforcer.longitude})`);
    });
  });
```

```javascript
// Get location analytics
fetch('/gps/analytics/5?hours=24')
  .then(r => r.json())
  .then(data => {
    console.log(`Distance: ${data.total_distance_km} km`);
    console.log(`Time: ${data.total_time_minutes} minutes`);
    console.log(`Locations: ${data.location_count}`);
  });
```

---

## Rate Limiting

- No specific rate limits currently implemented
- Location updates recommended every 10-30 seconds
- Dashboard refresh recommended every 5 seconds

---

## Authentication

All endpoints use Laravel's built-in session-based authentication. Make sure:
1. User is logged in
2. User has appropriate role
3. CSRF token is included in POST requests

---

## Data Storage

Location data is stored in the `enforcer_locations` table:
- Maximum 100 locations per enforcer kept in active storage
- Older records are automatically deleted
- All timestamps are in UTC

---

## Best Practices

1. **Frequency:** Send location every 10-30 seconds
2. **Accuracy:** Request high accuracy on mobile devices
3. **Caching:** Don't cache location responses (maximumAge: 0)
4. **Error Handling:** Implement exponential backoff for failures
5. **Storage:** Archive location data regularly for historical analysis
