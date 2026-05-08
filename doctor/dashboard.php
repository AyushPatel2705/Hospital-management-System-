<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../index.php"); exit();
}
require_once '../db.php';
$did = $_SESSION['user_id'];
$today = date('Y-m-d');

$total_appt  = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM appointments WHERE doctor_id=$did"))[0];
$today_appt  = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM appointments WHERE doctor_id=$did AND appointment_date='$today'"))[0];
$pending     = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM appointments WHERE doctor_id=$did AND status='Pending'"))[0];
$completed   = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM appointments WHERE doctor_id=$did AND status='Completed'"))[0];

$today_list = mysqli_query($conn,"
  SELECT a.*, p.name AS pname, p.age, p.gender FROM appointments a
  JOIN patients p ON a.patient_id=p.id
  WHERE a.doctor_id=$did AND a.appointment_date='$today'
  ORDER BY a.appointment_time");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Doctor Dashboard</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="layout">
<?php include 'sidebar.php'; ?>
<div class="main">
  <div class="topbar">
    <h3>Doctor Dashboard</h3>
    <span class="date">📅 <?= date('l, d M Y') ?></span>
  </div>
  <div class="content">
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue">📅</div>
        <div><div class="stat-num"><?= $today_appt ?></div><div class="stat-label">Today's Appointments</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon yellow">⏳</div>
        <div><div class="stat-num"><?= $pending ?></div><div class="stat-label">Pending</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div><div class="stat-num"><?= $completed ?></div><div class="stat-label">Completed</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue">📋</div>
        <div><div class="stat-num"><?= $total_appt ?></div><div class="stat-label">Total Appointments</div></div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-header">
        <h4>📋 Today's Appointments</h4>
        <a href="appointments.php" class="btn btn-primary btn-sm">View All</a>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>#</th><th>Patient</th><th>Age/Gender</th><th>Time</th><th>Notes</th><th>Status</th><th>Action</th></tr></thead>
          <tbody>
          <?php if (mysqli_num_rows($today_list) === 0): ?>
          <tr><td colspan="7" style="text-align:center;color:#999;padding:30px">No appointments today</td></tr>
          <?php endif; ?>
          <?php $i=1; while($r = mysqli_fetch_assoc($today_list)): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><strong><?= htmlspecialchars($r['pname']) ?></strong></td>
            <td><?= $r['age'] ?> / <?= $r['gender'] ?></td>
            <td><?= date('h:i A', strtotime($r['appointment_time'])) ?></td>
            <td><?= htmlspecialchars($r['notes']) ?></td>
            <td><span class="badge badge-<?= strtolower($r['status']) ?>"><?= $r['status'] ?></span></td>
            <td>
              <?php if($r['status']==='Pending'): ?>
              <a href="appointments.php?complete=<?= $r['id'] ?>" class="btn btn-success btn-sm">✓ Done</a>
              <?php endif; ?>
              <a href="prescriptions.php?appt=<?= $r['id'] ?>&pid=<?= $r['patient_id'] ?>" class="btn btn-primary btn-sm">💊 Rx</a>
            </td>
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
