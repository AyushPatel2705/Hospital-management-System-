# 🏥 Hospital Management System

## Setup Instructions (XAMPP)

### Step 1 — Copy Files
Copy the `hospital` folder to:
```
C:\xampp\htdocs\hospital
```

### Step 2 — Import Database
1. Start **Apache** and **MySQL** from XAMPP Control Panel
2. Open your browser → go to: http://localhost/phpmyadmin
3. Click **"New"** → create database named: `hospital_db`
4. Click **Import** → Choose file → select `hospital_db.sql`
5. Click **Go**

### Step 3 — Run the App
Open browser → http://localhost/hospital

---

## Login Credentials

| Role   | Email                   | Password    |
|--------|-------------------------|-------------|
| Doctor | doctor@hospital.com     | password123 |
| Doctor | doctor2@hospital.com    | password123 |
| Staff  | staff@hospital.com      | password123 |

---

## Features

### Staff Role
- ✅ Dashboard with stats
- ✅ Add / Edit / Delete patients
- ✅ Book appointments (with time-conflict check)
- ✅ View & filter all appointments
- ✅ Cancel appointments

### Doctor Role
- ✅ Dashboard with today's appointments
- ✅ View & filter own appointments
- ✅ Mark appointments as completed / cancel
- ✅ Write prescriptions
- ✅ Print prescriptions
- ✅ View patient list

---

## File Structure
```
hospital/
├── index.php              ← Login page
├── logout.php
├── db.php                 ← Database connection
├── print_rx.php           ← Prescription print
├── hospital_db.sql        ← Database import file
├── css/style.css
├── js/main.js
├── doctor/
│   ├── dashboard.php
│   ├── appointments.php
│   ├── patients.php
│   └── prescriptions.php
└── staff/
    ├── dashboard.php
    ├── manage_patients.php
    ├── book_appointment.php
    └── view_appointments.php
```

---

## Requirements
- XAMPP (Apache + MySQL + PHP 7.4+)
- Browser (Chrome / Firefox / Edge)
