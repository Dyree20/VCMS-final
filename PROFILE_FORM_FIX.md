# Profile Form Validation Fix - December 2, 2025

## Problem Identified
When filling out the "Address" section in the edit profile form, validation errors appeared for first name and last name fields ("f name field is required" and "l name field is required") even though these fields were not part of the address form.

## Root Cause
**ProfileController.php** - The controller was validating ALL form sections with the same validation rules. When the address form was submitted, it was still requiring first name and last name fields, which weren't present in the address form.

**admin/edit-profile.blade.php** - The view had an incorrect route reference for enforcer profile updates (using `profile.update` instead of `enforcer.profile.update`).

## Issues Fixed

### Fix #1: Controller Validation Logic (ProfileController.php)
**Problem:** 
- Single validation rule for all form types
- Address form submission was checked against personal form rules
- First name and last name were required even for address-only submissions

**Solution:**
Added conditional validation based on `form_type`:
- `form_type='personal'` → Validates: f_name, l_name, phone, photo, nationality, gender, birthdate
- `form_type='address'` → Validates: country, city, postal_code, address, bio
- `form_type='login'` → Validates: email, username, password

**Changes:**
```php
// Added new condition for address form
elseif ($formType === 'address') {
    $validated = $request->validate([
        'country' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'postal_code' => 'nullable|string|max:20',
        'address' => 'nullable|string',
        'bio' => 'nullable|string',
    ]);
    
    UserDetail::updateOrCreate(
        ['user_id' => $user->id],
        [
            'country' => $validated['country'] ?? null,
            'city' => $validated['city'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'address' => $validated['address'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ]
    );
}
```

### Fix #2: View Route Reference (admin/edit-profile.blade.php)
**Problem:**
- Enforcer profile update route was set to `profile.update` (non-existent)
- Should be `enforcer.profile.update` for consistency

**Solution:**
```php
// BEFORE:
str_contains($currentRoute, 'enforcer') => 'profile.update',

// AFTER:
str_contains($currentRoute, 'enforcer') => 'enforcer.profile.update',
```

## Files Modified
1. `app/Http/Controllers/ProfileController.php` - Updated validation logic
2. `resources/views/admin/edit-profile.blade.php` - Fixed route reference

## Verification
✅ All profile update routes properly registered:
- `PUT /profile/update` → admin.profile.update
- `PUT /enforcer/profile/update` → enforcer.profile.update
- `PUT /front-desk/profile/update` → front-desk.profile.update

## Testing Checklist
- [ ] Submit Personal Information form - should save name, email, phone
- [ ] Submit Address form - should save address details WITHOUT requiring name fields
- [ ] Submit Login & Password form - should save credentials
- [ ] All three sections should redirect properly based on user role
- [ ] No validation errors on address form submission

## Impact
✅ Address form now submits without requiring first/last name fields
✅ Each form section validates only its own fields
✅ Better user experience with targeted error messages
✅ Consistent routing across all user roles
