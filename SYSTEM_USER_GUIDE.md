# 🚗 Vehicle Clamping Management System (VCMS) - User Guide

## 📋 Table of Contents
1. [Getting Started](#getting-started)
2. [User Roles & Access](#user-roles--access)
3. [Admin Dashboard](#admin-dashboard)
4. [Front Desk Dashboard](#front-desk-dashboard)
5. [Enforcer Dashboard](#enforcer-dashboard)
6. [Common Features](#common-features)
7. [Troubleshooting](#troubleshooting)

---

## 🚀 Getting Started

### Logging In
1. Go to the login page: `http://192.168.1.6:8000/login`
2. Enter your **email** and **password**
3. Click **Login**
4. You'll be redirected to your role-specific dashboard

### User Roles
The system has three main user roles:
- **Admin** - System administrator with full access
- **Front Desk** - Handles customer inquiries and payments
- **Enforcer** - Field officers who perform clamping operations

---

## 👥 User Roles & Access

### 🔑 Admin Access
**Permissions:**
- View complete dashboard with all statistics
- Manage users (approve/reject, assign areas, toggle status)
- Manage teams and assign enforcers
- View all clamping records
- View all payments and transactions
- Access activity logs
- View archives
- Device security management

**Main Menu:**
- Dashboard - Overview and statistics
- Clamping Management - View all clamping records
- Payments - Payment tracking
- User Management - Manage system users
- Teams Management - Create and manage teams
- Activity Logs - System activity tracking
- Settings - Profile and account settings

---

### 🏢 Front Desk Access
**Permissions:**
- Search vehicle violations
- Record customer inquiries
- Create and manage payments
- View payment status
- Profile management
- View their own dashboard statistics

**Main Menu:**
- Dashboard - Front desk overview
- Violations - Search and view violations
- Payments - Create and track payments
- Archives - View past records
- Settings - Profile and account settings

**Key Responsibilities:**
1. **Search Violations** - Look up vehicle violation records
2. **Record Inquiries** - Log customer inquiries about violations
3. **Create Payments** - Process customer payments
4. **Mark as Paid** - Update payment status when received
5. **View Statistics** - Monitor daily activity and payments

---

### 👮 Enforcer Access
**Permissions:**
- View assigned clamping tasks
- Create new clamping records
- Accept/reject/approve clamping tasks
- View transaction history
- Profile management
- View notifications

**Main Menu:**
- Dashboard - Clamping tasks and stats
- My Clampings - View all clamping records
- Payments - View payment records
- Transactions History - View payment history
- Notifications - System notifications
- Contact Us - Support contact
- Help & FAQs - Documentation
- Settings - Profile and account settings

**Key Responsibilities:**
1. **Clamp Vehicles** - Perform clamping on violating vehicles
2. **Record Details** - Document clamping information
3. **Update Status** - Mark clamping as complete/released
4. **View Payments** - Monitor collected payments

---

## 📊 Admin Dashboard

### Dashboard Overview
The admin dashboard displays:
- **Total Clampings** - Number of active clamping records
- **Total Payments** - Revenue generated
- **Total Users** - System users count
- **Active Teams** - Number of teams

### User Management
**Access:** Main Menu → User Management

**Features:**
- View all registered users
- **Approve** new user registrations
- **Reject** suspicious accounts
- **Assign Area** - Assign geographic areas to enforcers
- **Toggle Status** - Activate/deactivate users
- **View Details** - Click user to see full profile

**Steps to Approve a User:**
1. Go to User Management
2. Find the user in the list
3. Click **Approve** button
4. User will be activated and can log in

---

### Teams Management
**Access:** Main Menu → Teams Management

**Features:**
- **Create New Team** - Click "Create Team" button
- **Add Enforcers** - Assign enforcers to teams
- **Remove Enforcers** - Remove from team
- **View Teams** - See all teams and members
- **Edit Team** - Update team information

**Steps to Create a Team:**
1. Click **Create New Team**
2. Enter team name and description
3. Select enforcers to add
4. Click **Save**

---

### Clamping Management
**Access:** Main Menu → Clamping Management

**Features:**
- View all clamping records
- Edit clamping details
- Release clamped vehicle
- Cancel clamping record
- Delete record
- View clamping receipt/details

**Clamping Status:**
- **Pending** - Waiting for enforcer action
- **Accepted** - Enforcer accepted the task
- **Approved** - Admin approved
- **Released** - Vehicle unclamped
- **Cancelled** - Clamping cancelled

---

### Payments Management
**Access:** Main Menu → Payments

**Features:**
- View all payments received
- Track payment status
- Monitor revenue

**Payment Status:**
- **Pending** - Payment not yet received
- **Completed** - Payment received and processed
- **Cancelled** - Payment cancelled

---

### Activity Logs
**Access:** Main Menu → Activity Logs

**Features:**
- Track all system activities
- Filter by date, user, or action
- Monitor user actions
- Audit trail

**Useful For:**
- Investigating issues
- Monitoring user activity
- System auditing
- Security tracking

---

### Archives
**Access:** Main Menu → Archives

**Features:**
- View completed/closed clamping records
- Filter and search archived records
- View archive details
- Export if needed

---

## 🏪 Front Desk Dashboard

### Dashboard Overview
Displays:
- **Today's Statistics** - Inquiries, payments, violations
- **Recent Activities** - Latest transactions
- **Quick Actions** - Fast access to common tasks

### Violations Search
**Access:** Main Menu → Violations

**How to Search:**
1. Enter **vehicle plate number** or **owner name**
2. Click **Search**
3. View violation details
4. See associated clamping record if exists
5. Link to payment if available

**Information Displayed:**
- Vehicle details (plate, owner)
- Violation type
- Violation date and location
- Fine amount
- Clamping status
- Payment status

---

### Payments Management
**Access:** Main Menu → Payments

**Features:**

**View Payments:**
1. Go to Payments
2. View all payment transactions
3. Filter by date or status
4. Click payment to see details

**Create Payment:**
1. Click **Create Payment**
2. Select or enter vehicle plate number
3. Enter payment amount
4. Choose payment method (if multiple available)
5. Click **Process Payment**
6. Customer completes payment on PayMongo gateway

**Mark as Paid:**
1. Find the inquiry with pending payment
2. Click **Mark as Paid**
3. Confirm payment received
4. Status updates to "Paid"

---

### Inquiry Management
**Access:** Main Menu → Violations (Inquiries shown here)

**Recording an Inquiry:**
1. Search for violation record
2. Click on the record
3. Add customer details if not present
4. Note the inquiry details
5. Link to payment if payment received

---

### Archives
**Access:** Main Menu → Archives

**Features:**
- View closed/completed inquiries
- Search archived records
- Filter by date range
- View historical data

---

## 👮 Enforcer Dashboard

### Dashboard Overview
Displays:
- **Pending Tasks** - New clamping assignments
- **My Clampings** - All your clamping records
- **Total Collected** - Payment statistics
- **Quick Stats** - Today's activity

### Clamping Tasks
**Access:** Main Menu → My Clampings

**Viewing Tasks:**
1. Go to My Clampings
2. See list of assigned clamping tasks
3. Filter by status (Pending, Accepted, Approved, Released)
4. Click task for details

**Task Actions:**

**Accept Task:**
1. Click on pending task
2. Click **Accept**
3. Task status changes to "Accepted"
4. You can now proceed with clamping

**Reject Task:**
1. Click on pending task
2. Click **Reject**
3. Provide reason (optional)
4. Task reassigned to another enforcer

**Report Completion:**
1. After clamping vehicle
2. Click **Approve** to mark complete
3. Enter completion details
4. Submit

**Release Vehicle:**
1. Click on clamped vehicle record
2. Click **Release**
3. Confirm release
4. Vehicle is unclamped in system
5. Payment must be received before release

---

### Create New Clamping
**Access:** Main Menu → Create Clamping

**Steps:**
1. Enter vehicle plate number
2. Enter vehicle owner name
3. Select violation type
4. Enter violation location
5. Enter fine amount
6. Take/upload vehicle photo (optional)
7. Add any notes
8. Click **Submit**

**After Submission:**
- Record enters "Pending" status
- Awaits admin approval
- You get notification when approved
- Then you can proceed with actual clamping

---

### Transactions History
**Access:** Main Menu → Transactions History

**Information Shown:**
- All payments received
- Transaction date and amount
- Payment method
- Transaction status
- Associated clamping record

---

### View Profile
**Access:** Settings icon → Profile

**Profile Information:**
- Full name
- Email
- Role
- Assigned area
- Join date
- Contact information
- Profile photo

**Edit Profile:**
1. Click **Edit** button
2. Update information
3. Click **Save**

**Change Photo:**
1. Go to profile
2. Click photo or **+** button
3. Select image file
4. Upload
5. Photo updates automatically

---

## 🔧 Common Features

### Profile Management
**Access:** Settings (Gear icon in top-right)

**Available Options:**
1. **My Profile** - View/edit personal information
2. **Edit Profile** - Update details
3. **Change Photo** - Upload new profile picture
4. **Security Settings** - Device management
5. **Notification Settings** - Notification preferences

### Profile Editing Steps:
1. Click Settings → My Profile
2. Click **Edit** button
3. Update fields:
   - First Name
   - Last Name
   - Email
   - Phone (if available)
   - Address
   - Gender
   - Birth Date
4. Click **Save Changes**

### Device Manager
**Access:** Settings → Security → Device Manager

**Features:**
- View all devices logged into your account
- See device location and last login
- **Logout Device** - Sign out specific device
- **Logout All Others** - Sign out all devices except current
- **Logout All Devices** - Sign out all devices

**Steps to Logout a Device:**
1. Go to Device Manager
2. Find the device you want to logout
3. Click **Logout** button
4. Device will be signed out

### Notifications
**Access:** Bell icon in top-right corner

**Notification Types:**
- Clamping task assignments
- Payment received
- User approvals
- System messages
- Team updates

**Managing Notifications:**
- Click bell icon to view latest notifications
- Click notification to go to related page
- Notification settings in profile

---

## 🔐 Security & Best Practices

### Password Security
- Never share your password
- Use strong passwords (mix of letters, numbers, symbols)
- Change password regularly
- Log out when done, especially on shared computers

### Device Security
- Check Device Manager regularly
- Logout devices you don't recognize
- Review login locations
- Use Device Manager on suspicious activity

### Data Protection
- Don't share sensitive customer information
- Keep vehicle details confidential
- Report security concerns to admin
- Lock your computer when away

---

## ❓ Troubleshooting

### Can't Log In
**Problem:** Username/password not working

**Solutions:**
1. Verify CAPS LOCK is off
2. Check email address spelling
3. Use "Forgot Password" to reset
4. Verify account is approved (admins may need to approve)

### Page Not Loading
**Problem:** Page shows error or blank

**Solutions:**
1. Hard refresh page (Ctrl+Shift+R or Cmd+Shift+R)
2. Clear browser cache
3. Try different browser
4. Check internet connection

### Mobile Display Issues
**Problem:** Page looks wrong on mobile

**Solutions:**
1. Hard refresh (Ctrl+Shift+R)
2. Close and reopen browser
3. Clear cache and cookies
4. Update browser to latest version

### Payment Issues
**Problem:** Payment not processing

**Solutions:**
1. Check internet connection
2. Verify payment details are correct
3. Try different payment method
4. Contact front desk for assistance

### Can't Find Violation/Clamping Record
**Problem:** Record not appearing in search

**Solutions:**
1. Verify plate number spelling
2. Search by vehicle owner name instead
3. Check Archives if record is old
4. Contact admin for assistance

### Notifications Not Working
**Problem:** Not receiving notifications

**Solutions:**
1. Check notification settings in profile
2. Verify browser allows notifications
3. Clear browser cache
4. Check that you're logged in

---

## 📞 Support

### Getting Help
- **Contact Admin** - For account/permission issues
- **Contact Support** - Use "Contact Us" form in system
- **FAQ** - Check Help & FAQs section
- **Check Activity Logs** - For system issues

### Information to Provide When Reporting Issues
- Your role/username
- What you were trying to do
- What error you received (if any)
- When the issue occurred
- Steps to reproduce the problem

---

## 🎓 Quick Reference

### Keyboard Shortcuts
- **Ctrl+R** - Refresh page
- **Ctrl+L** - Go to address bar
- **Ctrl+D** - Bookmark page
- **F11** - Fullscreen
- **F12** - Open developer tools

### Important URLs
- **Login:** `http://192.168.1.6:8000/login`
- **Dashboard:** `http://192.168.1.6:8000/dashboard`
- **Payments:** `http://192.168.1.6:8000/payments`
- **Users:** `http://192.168.1.6:8000/users`

---

## 📝 Feature Checklist

### For Admins ✅
- [ ] Know how to manage users
- [ ] Know how to manage teams
- [ ] Can view all clamping records
- [ ] Can view all payments
- [ ] Know how to check activity logs
- [ ] Know how to manage device security

### For Front Desk ✅
- [ ] Know how to search violations
- [ ] Know how to record inquiries
- [ ] Can create and process payments
- [ ] Know how to mark payments as paid
- [ ] Can view your profile
- [ ] Know how to update profile photo

### For Enforcers ✅
- [ ] Know how to view assigned tasks
- [ ] Can accept/reject clamping tasks
- [ ] Can create new clamping records
- [ ] Know how to report completion
- [ ] Can view transaction history
- [ ] Know how to update profile

---

## 🎉 You're Ready!

You now have a comprehensive understanding of the Vehicle Clamping Management System. Start exploring and don't hesitate to contact support if you need help!

**Happy clamping! 🚗**

---

**Version:** 1.0
**Last Updated:** November 18, 2025
**System:** Vehicle Clamping Management System (VCMS)
