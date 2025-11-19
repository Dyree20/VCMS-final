# 🚗 VCMS Quick Reference Guide

## 🔐 Login
```
URL: http://192.168.1.6:8000/login
Email: your@email.com
Password: yourpassword
```

---

## 👥 Role Quick Reference

| Feature | Admin | Front Desk | Enforcer |
|---------|-------|-----------|----------|
| **Dashboard** | Full | Limited | Limited |
| **User Management** | ✅ | ❌ | ❌ |
| **Teams Management** | ✅ | ❌ | ❌ |
| **Clamping Records** | View All | Limited | Own |
| **Payments** | All | Manage | View |
| **Activity Logs** | ✅ | ❌ | ❌ |
| **Profile Edit** | ✅ | ✅ | ✅ |

---

## 📊 Admin - Top Tasks

### 1. Approve New Users
```
Menu → User Management → Select User → Click "Approve"
```

### 2. Create Team
```
Menu → Teams Management → Click "Create Team"
Enter Team Name → Select Enforcers → Save
```

### 3. View All Clampings
```
Menu → Clamping Management
Filter by Status (Pending, Accepted, Approved, Released)
```

### 4. Check Activity
```
Menu → Activity Logs
Filter by Date, User, or Action
```

---

## 🏢 Front Desk - Top Tasks

### 1. Search Violation
```
Menu → Violations → Enter Plate Number/Name → Search
```

### 2. Create Payment
```
Menu → Payments → Click "Create Payment"
Select Vehicle → Enter Amount → Click "Process"
```

### 3. Mark Payment Received
```
Menu → Violations → Find Inquiry → Click "Mark as Paid"
```

### 4. View Statistics
```
Dashboard shows Today's Stats
Payments, Inquiries, Violations
```

---

## 👮 Enforcer - Top Tasks

### 1. View Assigned Tasks
```
Menu → My Clampings → Filter by "Pending"
```

### 2. Accept Clamping Task
```
Click Task → Click "Accept" → Proceed with Clamping
```

### 3. Report Completion
```
Click Task → Click "Approve" → Add Completion Details
```

### 4. Create New Clamping
```
Menu → Create Clamping
Enter Details → Click "Submit" → Await Admin Approval
```

### 5. View Payments Received
```
Menu → Transactions History
Shows all payments linked to your clampings
```

---

## ⚙️ Common Actions

### Update Profile
```
Settings (Gear Icon) → My Profile → Click "Edit"
Update Fields → Click "Save Changes"
```

### Change Profile Photo
```
Profile → Click "+" Button or Photo
Select Image → Upload
```

### Logout Device
```
Settings → Security → Device Manager
Select Device → Click "Logout"
```

### Reset Password
```
Login Page → Click "Forgot Password"
Enter Email → Click "Send Link"
Check Email → Click Link → Enter New Password
```

---

## 🔍 Search Tips

| What You Want | Where to Look | How to Find |
|---------------|---------------|------------|
| **Violation Record** | Front Desk → Violations | Search by plate or name |
| **Clamping Record** | Admin → Clamping or Enforcer → My Clampings | Search by plate |
| **Payment Status** | Admin/Front Desk → Payments | Filter by date |
| **User Activity** | Admin → Activity Logs | Filter by user/date |
| **Old Records** | Archives | Search by plate/date |

---

## 📞 Status Meanings

### Clamping Status
- **Pending** = Waiting for enforcer
- **Accepted** = Enforcer accepted task
- **Approved** = Admin approved, ready to clamp
- **Released** = Vehicle unclamped
- **Cancelled** = Record cancelled

### Payment Status
- **Pending** = Waiting for customer payment
- **Completed** = Payment received
- **Cancelled** = Payment cancelled

### User Status
- **Pending** = Waiting for admin approval
- **Approved** = User active
- **Rejected** = Application rejected
- **Inactive** = User disabled

---

## 🆘 Quick Troubleshoot

| Problem | Quick Fix |
|---------|-----------|
| Page not loading | Ctrl+Shift+R (hard refresh) |
| Can't login | Check email/password, try "Forgot Password" |
| Mobile display wrong | Clear cache, hard refresh |
| Not seeing record | Check spelling, search by different field |
| Notification missing | Check notification settings in profile |
| Payment not processing | Verify internet, check details, retry |

---

## 🎯 Daily Workflows

### Admin Daily
```
1. Check Dashboard for stats
2. Review pending user approvals
3. Monitor clamping records
4. Review activity logs
5. Check payment received
```

### Front Desk Daily
```
1. Check Dashboard for daily stats
2. Search and respond to violations
3. Process customer payments
4. Record new inquiries
5. Update payment status
```

### Enforcer Daily
```
1. Check Dashboard for new tasks
2. Accept pending clamping tasks
3. Perform field clamping
4. Report completion
5. Check payment records
```

---

## 🔐 Security Reminders

✅ **DO:**
- Change password regularly
- Logout when done
- Check Device Manager
- Report suspicious activity
- Keep login credentials private

❌ **DON'T:**
- Share your password
- Leave computer unattended
- Use public WiFi for sensitive actions
- Share customer information
- Click suspicious links

---

## 📱 Mobile Tips

- **Hamburger Menu (☰)** - Click to open/close sidebar
- **Responsive Layout** - Page adapts to screen size
- **Touch Targets** - All buttons sized for tapping
- **Hard Refresh** - Ctrl+Shift+R for updates

---

## 🌐 Important URLs

```
Login:          http://192.168.1.6:8000/login
Dashboard:      http://192.168.1.6:8000/dashboard
Admin:          http://192.168.1.6:8000/dashboard
Front Desk:     http://192.168.1.6:8000/front-desk/dashboard
Enforcer:       http://192.168.1.6:8000/enforcer/dashboard
Users:          http://192.168.1.6:8000/users
Clampings:      http://192.168.1.6:8000/clampings
Payments:       http://192.168.1.6:8000/payments
Teams:          http://192.168.1.6:8000/teams
Activity Logs:  http://192.168.1.6:8000/activity-logs
```

---

## ⚡ Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| `Ctrl+R` | Refresh |
| `Ctrl+Shift+R` | Hard refresh |
| `Ctrl+L` | Address bar |
| `Ctrl+D` | Bookmark |
| `F11` | Fullscreen |
| `F12` | Developer tools |

---

## 📋 Forms Checklist

### Clamping Record
- [ ] Vehicle plate number
- [ ] Owner name
- [ ] Violation type
- [ ] Location
- [ ] Fine amount
- [ ] Photos (if available)

### Payment Record
- [ ] Vehicle plate
- [ ] Amount
- [ ] Payment method
- [ ] Payment reference
- [ ] Status

### User Approval
- [ ] Review name
- [ ] Check email
- [ ] Verify role
- [ ] Assign area (if enforcer)
- [ ] Click Approve

---

## 🎓 Getting Help

### In-App Help
- **Menu** → Check for help links
- **Contact Us** → Send support request
- **FAQ** → Find answers
- **Activity Logs** → See what changed

### External Help
- Contact your admin
- Check system guide (SYSTEM_USER_GUIDE.md)
- Review mobile responsive guide
- Check route documentation

---

**Version:** 1.0
**Last Updated:** November 18, 2025
**Bookmark this page for quick reference!** 📌

---

## 💡 Pro Tips

1. **Use search** instead of scrolling through long lists
2. **Filter records** by date/status for faster access
3. **Check Device Manager** regularly for security
4. **Download data** before deleting records
5. **Use browser bookmarks** for frequent pages
6. **Hard refresh** if something looks wrong
7. **Keep browser updated** for best performance
8. **Use strong passwords** with special characters

---

**Ready to use VCMS? Start with your first task above!** 🚀
