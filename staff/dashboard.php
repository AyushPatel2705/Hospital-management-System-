<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../index.php"); exit();
}
require_once '../db.php';
$today = date('Y-m-d');

$total_patients = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM patients"))[0];
$total_doctors  = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM users WHERE role='doctor'"))[0];
$today_appts    = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM appointments WHERE appointment_date='$today'"))[0];
$pending        = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM appointments WHERE status='Pending'"))[0];

$recent = mysqli_query($conn,"
  SELECT a.*, p.name AS pname, u.name AS dname FROM appointments a
  JOIN patients p ON a.patient_id=p.id
  JOIN users u ON a.doctor_id=u.id
  ORDER BY a.created_at DESC LIMIT 8");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Staff Dashboard</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="layout">
<?php include 'sidebar.php'; ?>
<div class="main">
  <div class="topbar">
    <h3>Staff Dashboard</h3>
    <span class="date">📅 <?= date('l, d M Y') ?></span>
  </div>
  <div class="content">
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue">👥</div>
        <div><div class="stat-num"><?= $total_patients ?></div><div class="stat-label">Total Patients</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">🩺</div>
        <div><div class="stat-num"><?= $total_doctors ?></div><div class="stat-label">Doctors Available</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon yellow">📅</div>
        <div><div class="stat-num"><?= $today_appts ?></div><div class="stat-label">Today's Appointments</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon red">⏳</div>
        <div><div class="stat-num"><?= $pending ?></div><div class="stat-label">Pending Appointments</div></div>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px">
      <a href="manage_patients.php" class="panel" style="text-decoration:none;display:flex;align-items:center;gap:16px;padding:22px">
        <div class="stat-icon blue" style="width:56px;height:56px;font-size:26px">👥</div>
        <div><div style="font-size:15px;font-weight:600;color:#1a3c5e">Manage Patients</div><div style="font-size:12px;color:#999">Add, edit, or remove patients</div></div>
      </a>
      <a href="book_appointment.php" class="panel" style="text-decoration:none;display:flex;align-items:center;gap:16px;padding:22px">
        <div class="stat-icon green" style="width:56px;height:56px;font-size:26px">📅</div>
        <div><div style="font-size:15px;font-weight:600;color:#1a3c5e">Book Appointment</div><div style="font-size:12px;color:#999">Schedule a new appointment</div></div>
      </a>
    </div>

    <div class="panel">
      <div class="panel-header">
        <h4>🕒 Recent Appointments</h4>
        <a href="view_appointments.php" class="btn btn-primary btn-sm">View All</a>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>#</th><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th></tr></thead>
          <tbody>
          <?php if(mysqli_num_rows($recent)===0): ?>
          <tr><td colspan="6" style="text-align:center;padding:30px;color:#999">No appointments yet</td></tr>
          <?php endif; ?>
          <?php $i=1; while($r=mysqli_fetch_assoc($recent)): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($r['pname']) ?></td>
            <td><?= htmlspecialchars($r['dname']) ?></td>
            <td><?= date('d M Y',strtotime($r['appointment_date'])) ?></td>
            <td><?= date('h:i A',strtotime($r['appointment_time'])) ?></td>
            <td><span class="badge badge-<?= strtolower($r['status']) ?>"><?= $r['status'] ?></span></td>
          </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</div>
<script src="../js/main.js"></script>
</body>
</html>
