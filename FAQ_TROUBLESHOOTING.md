# ❓ Vehicle Clamping Management System - FAQ & Troubleshooting

## 📚 Frequently Asked Questions

### Account & Login

**Q: How do I create a new account?**
A: 
1. Go to the login page
2. Click "Register" or "Sign Up"
3. Fill in your details (name, email, password, etc.)
4. Submit the registration
5. Wait for admin approval (you'll receive an email)
6. Once approved, you can login

**Q: I forgot my password. How do I reset it?**
A:
1. Go to login page
2. Click "Forgot Password"
3. Enter your email address
4. Click "Send Reset Link"
5. Check your email for reset link
6. Click link in email
7. Enter new password
8. Click "Reset Password"
9. Login with new password

**Q: How long does admin approval take?**
A: Typically within 24 hours. Check your email for approval notification.

**Q: Can I have multiple accounts?**
A: No, use only one account per person. If you need account changes, contact admin.

**Q: Why can't I login even with correct password?**
A:
- Account may not be approved yet
- Account may be disabled/deactivated
- Email may be wrong
- Password may have extra spaces
- Try "Forgot Password" to reset

---

### Dashboard & Navigation

**Q: How do I access my dashboard?**
A: After login, you're automatically redirected to your role-specific dashboard.
- Admin: `/dashboard`
- Front Desk: `/front-desk/dashboard`
- Enforcer: `/enforcer/dashboard`

**Q: Where is the menu?**
A:
- **Desktop:** Left sidebar (always visible)
- **Mobile:** Click hamburger menu (☰) icon in top-left

**Q: How do I go back to dashboard?**
A: Click "Dashboard" in sidebar or click system logo.

**Q: What are the page refresh options?**
A:
- **Soft refresh:** F5 or Ctrl+R
- **Hard refresh:** Ctrl+Shift+R (also clears cache)
- **Full refresh:** Close and reopen browser

---

### User Management (Admin)

**Q: How do I approve a new user?**
A:
1. Menu → User Management
2. Find the user in the list
3. Click "Approve" button
4. User receives email notification
5. User can now login

**Q: How do I assign an area to an enforcer?**
A:
1. Menu → User Management
2. Click on the enforcer
3. Click "Edit"
4. Select area from dropdown
5. Click "Save"

**Q: Can I change a user's role?**
A: Roles are assigned during registration and typically can't be changed. Create new account with different role if needed.

**Q: How do I deactivate a user?**
A:
1. Menu → User Management
2. Find user
3. Click "Toggle Status"
4. User is now inactive
5. They cannot login

**Q: How do I permanently delete a user?**
A: This requires direct database access. Contact system administrator.

---

### Teams Management (Admin)

**Q: How do I create a new team?**
A:
1. Menu → Teams Management
2. Click "Create New Team"
3. Enter team name
4. Enter description (optional)
5. Select enforcers to add
6. Click "Save"

**Q: Can I have teams with no members?**
A: Yes, but the team won't be useful. Add members or delete the team.

**Q: How do I add an enforcer to a team after creation?**
A:
1. Menu → Teams Management
2. Click on team
3. Click "Add Enforcer"
4. Select enforcer from list
5. Click "Save"

**Q: Can an enforcer be in multiple teams?**
A: Yes, enforcers can be assigned to multiple teams.

**Q: How do I remove an enforcer from a team?**
A:
1. Menu → Teams Management
2. Click on team
3. Find enforcer
4. Click "Remove"
5. Confirm removal

---

### Clamping Management

**Q: How do I create a new clamping record?**
A:
1. Menu → Clamping Management
2. Click "New Clamping"
3. Enter vehicle details
4. Enter violation details
5. Enter fine amount
6. Click "Submit"

**Q: What's the difference between "Accept", "Approve", and "Release"?**
A:
- **Accept** (Enforcer) - "I will do this clamping"
- **Approve** (Admin) - "This clamping is valid, proceed"
- **Release** (Enforcer/Admin) - "Vehicle unclamped"

**Q: How do I release a clamped vehicle?**
A:
1. Find clamping record
2. Click "Release"
3. Confirm release
4. Vehicle is marked as released
5. Note: Payment must be received first

**Q: Can I cancel a clamping?**
A: Yes, if it's still pending. Click "Cancel" and confirm.

**Q: How do I print a clamping receipt?**
A:
1. Find clamping record
2. Click "Receipt" or "Print"
3. Print dialog opens
4. Click "Print" to save as PDF

**Q: Where are old clamping records?**
A: Menu → Archives (view completed records)

---

### Payments & Front Desk

**Q: How do I create a payment record?**
A:
1. Menu → Payments
2. Click "Create Payment"
3. Enter vehicle plate
4. Enter amount
5. Select payment method
6. Click "Process"
7. Customer completes payment on PayMongo

**Q: Why can't the customer complete payment?**
A: Possible reasons:
- Internet connection issue
- Payment method expired
- Insufficient funds
- Browser compatibility
- Payment gateway issue

**Q: How do I mark a payment as received?**
A:
1. Find the violation/inquiry
2. Click "Mark as Paid"
3. Confirm payment received
4. Status updates to "Paid"

**Q: What payment methods are supported?**
A: Currently PayMongo gateway is integrated (credit/debit cards).

**Q: Can I refund a payment?**
A: Contact your system administrator - requires special access.

**Q: How do I search for a violation?**
A:
1. Menu → Violations
2. Enter vehicle plate number OR owner name
3. Click "Search"
4. View results

---

### Profile Management

**Q: How do I edit my profile?**
A:
1. Click Settings (gear icon) → My Profile
2. Click "Edit"
3. Update your information
4. Click "Save Changes"

**Q: How do I change my profile photo?**
A:
1. Go to Profile
2. Click on photo or "+" button
3. Select image from computer
4. Wait for upload to complete
5. Photo updates automatically

**Q: What photo formats are supported?**
A: JPEG, PNG, GIF, WebP (max 2MB)

**Q: Can I see other users' profiles?**
A: Only admins can view other users' profiles in User Management.

**Q: How do I change my email?**
A: Click Settings → Edit Profile → Change email → Save.

---

### Security & Devices

**Q: How do I check what devices are logged into my account?**
A:
1. Click Settings (gear icon)
2. Click "Security"
3. Click "Device Manager"
4. View list of active devices

**Q: How do I logout a specific device?**
A:
1. Go to Device Manager
2. Find the device
3. Click "Logout"
4. Device is signed out

**Q: Should I logout all devices?**
A: Only if you suspect unauthorized access. You'll need to login again on all devices.

**Q: How do I know if there's suspicious activity?**
A: Check Device Manager for unfamiliar devices or locations.

**Q: What should I do if I suspect my account is compromised?**
A:
1. Change your password immediately
2. Check Device Manager
3. Logout all devices
4. Contact administrator
5. Verify all activity logs

---

### Mobile & Accessibility

**Q: Why doesn't the page look right on my phone?**
A:
- Try hard refresh (Ctrl+Shift+R)
- Clear browser cache
- Update browser to latest version
- Try different browser

**Q: How do I open the menu on mobile?**
A: Click the hamburger menu icon (☰) in top-left corner.

**Q: Can I use the system on a tablet?**
A: Yes, it's fully responsive on all screen sizes.

**Q: Why is the keyboard opening on mobile?**
A: This is normal - you may have clicked an input field.

**Q: How do I use the system on iOS vs Android?**
A: Same way - use browser (Chrome, Safari, Firefox, etc.).

**Q: Is there an app?**
A: Not currently - use the web browser on your phone.

---

### Notifications

**Q: How do I turn on notifications?**
A:
1. Click bell icon (🔔) in top-right
2. Check notification settings in profile
3. Allow notifications in browser popup
4. Refresh page

**Q: Why don't I get notifications?**
A: Possible reasons:
- Notifications disabled in profile settings
- Browser notifications disabled
- Not logged in
- Notifications turned off in browser

**Q: What notifications will I receive?**
A:
- **Admin:** User approvals, new clamping tasks, payments
- **Front Desk:** New inquiries, payment received
- **Enforcer:** New task assignments, payment received

---

### Reports & Exports

**Q: How do I export clamping data?**
A:
1. Go to Clamping Management or Archives
2. Look for "Export" or download button
3. Select format (PDF, Excel, etc.)
4. Click download

**Q: How do I view activity logs?**
A: Menu → Activity Logs (Admin only)

**Q: Can I generate a report?**
A: Reports are available in the dashboard and can be exported.

**Q: How do I filter records by date?**
A: Most pages have date filters at the top - select date range and click filter.

---

## 🆘 Troubleshooting

### Login Issues

**Problem: "Invalid credentials" error**
```
Try:
1. Verify caps lock is off
2. Check email spelling
3. Check password carefully (spaces count!)
4. Use "Forgot Password" to reset
5. Make sure account is approved
```

**Problem: "Account not approved" message**
```
Try:
1. Wait for admin approval
2. Check email for approval notification
3. Contact admin to check status
4. Try again after a few minutes
```

**Problem: Page keeps redirecting to login**
```
Try:
1. Clear browser cookies
2. Hard refresh (Ctrl+Shift+R)
3. Try different browser
4. Make sure internet connection works
5. Check if cookies are enabled
```

---

### Display Issues

**Problem: Page looks broken or wrong formatting**
```
Try:
1. Hard refresh (Ctrl+Shift+R)
2. Clear browser cache completely
3. Close all tabs and reopen
4. Try different browser
5. Disable browser extensions
6. Update browser to latest version
```

**Problem: Text too small or too large**
```
Try:
1. Use Ctrl++ to zoom in
2. Use Ctrl+- to zoom out
3. Press Ctrl+0 to reset zoom
4. Check browser zoom setting
```

**Problem: Images not loading**
```
Try:
1. Check internet connection
2. Hard refresh page
3. Wait a moment and retry
4. Check if server is online
5. Try different browser
```

---

### Functionality Issues

**Problem: Search not finding records**
```
Try:
1. Check spelling of search term
2. Search by different field (plate vs name)
3. Check if record is archived
4. Check if record actually exists
5. Contact admin for help
```

**Problem: Can't create or save record**
```
Try:
1. Make sure all required fields are filled
2. Check for error messages
3. Hard refresh page
4. Try again
5. Contact admin if continues
```

**Problem: Payment won't process**
```
Try:
1. Check internet connection
2. Verify payment details
3. Try different payment method
4. Wait a moment and retry
5. Contact admin or payment support
```

**Problem: Can't delete a record**
```
This might be intentional - may need special permissions
Contact admin for assistance with deletion
```

---

### Performance Issues

**Problem: Page loading slowly**
```
Try:
1. Check internet connection speed
2. Close other browser tabs
3. Clear browser cache
4. Disable browser extensions
5. Restart browser
6. Try at different time
```

**Problem: Application keeps crashing**
```
Try:
1. Hard refresh (Ctrl+Shift+R)
2. Clear all browser data
3. Restart computer
4. Use different browser
5. Contact admin
```

---

### Account Issues

**Problem: Can't update profile**
```
Try:
1. Click "Edit" button first
2. Make changes
3. Click "Save" not just refresh
4. Hard refresh after saving
5. Clear cache if changes don't show
```

**Problem: Profile photo won't upload**
```
Try:
1. Check file is actual image (JPG, PNG, etc.)
2. Check file size (under 2MB)
3. Try smaller file size
4. Try different image format
5. Hard refresh page
```

**Problem: Can't change password**
```
Try:
1. Make sure new password is different
2. Check password requirements
3. Try "Forgot Password" instead
4. Verify email works first
5. Contact admin if still failing
```

---

### Permission Issues

**Problem: "Access Denied" or "Unauthorized"**
```
This means your role doesn't have access to this page
Solution:
1. Contact admin to request access
2. Check if you have correct role
3. Wait for admin to grant permissions
```

**Problem: Missing menu items**
```
Some items appear based on role:
- User Management - Admin only
- Teams - Admin only
- Transactions - Enforcer only

Your role may not have access to all features
```

---

## 🔧 Advanced Troubleshooting

### Clear Browser Cache
**Chrome/Edge:**
1. Press Ctrl+Shift+Delete
2. Select "All time"
3. Check "Cookies and cached images"
4. Click "Clear data"

**Firefox:**
1. Press Ctrl+Shift+Delete
2. Select "Everything"
3. Click "Clear Now"

**Safari:**
1. Press Cmd+Option+E
2. Select timeframe
3. Click "Remove"

### Check Browser Console
1. Press F12 to open developer tools
2. Go to "Console" tab
3. Look for red error messages
4. Screenshot and send to admin if needed

### Try Incognito/Private Mode
1. Press Ctrl+Shift+N (Chrome) or Ctrl+Shift+P (Firefox)
2. Try using system in private window
3. If works, clear cache in normal mode
4. Return to normal browsing

---

## 📞 When to Contact Support

Contact your administrator if:
- You can't login after trying all solutions
- You need account role changed
- You found a security issue
- Database appears corrupted
- System is completely down
- You found a bug/error
- You need new feature

**Provide:**
- Your username/email
- What you were trying to do
- Error message received (screenshot)
- When it started happening
- Steps to reproduce

---

## 📋 Common Error Messages & Meanings

| Error | Meaning | Solution |
|-------|---------|----------|
| "Invalid Credentials" | Wrong email/password | Reset password |
| "Unauthenticated" | Not logged in | Login first |
| "Unauthorized" | No permission | Ask admin for access |
| "Page Not Found" | URL incorrect or page deleted | Check URL |
| "Server Error" | Server problem | Wait or contact admin |
| "Validation Error" | Missing/invalid form data | Fill all required fields |
| "Duplicate Entry" | Record already exists | Use different data |
| "Field Required" | Forgot to fill required field | Fill all required fields |

---

## ✅ Verification Checklist

Before contacting support, verify:
- [ ] You're using correct email/password
- [ ] You're using correct URL
- [ ] You have internet connection
- [ ] Browser is up to date
- [ ] Cookies are enabled
- [ ] You tried hard refresh
- [ ] You tried clearing cache
- [ ] You tried different browser
- [ ] Account is approved/active
- [ ] You have required role/permissions

---

**Still having issues? Contact your system administrator!**

**Version:** 1.0
**Last Updated:** November 18, 2025
**System:** Vehicle Clamping Management System (VCMS)
