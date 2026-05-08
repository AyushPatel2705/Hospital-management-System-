<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../index.php"); exit();
}
require_once '../db.php';
$msg = '';

// Cancel appointment
if (isset($_GET['cancel'])) {
    $id = intval($_GET['cancel']);
    mysqli_query($conn,"UPDATE appointments SET status='Cancelled' WHERE id=$id");
    $msg = 'success:Appointment cancelled successfully.';
}

$filter_status = $_GET['status'] ?? '';
$filter_date   = $_GET['date']   ?? '';
$filter_doctor = intval($_GET['doctor'] ?? 0);

$where = "WHERE 1=1";
if ($filter_status) $where .= " AND a.status='".mysqli_real_escape_string($conn,$filter_status)."'";
if ($filter_date)   $where .= " AND a.appointment_date='".mysqli_real_escape_string($conn,$filter_date)."'";
if ($filter_doctor) $where .= " AND a.doctor_id=$filter_doctor";

$appts   = mysqli_query($conn,"
  SELECT a.*, p.name AS pname, p.phone, u.name AS dname, u.specialization
  FROM appointments a JOIN patients p ON a.patient_id=p.id
  JOIN users u ON a.doctor_id=u.id $where ORDER BY a.appointment_date DESC, a.appointment_time");
$doctors = mysqli_query($conn,"SELECT id,name FROM users WHERE role='doctor'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>View Appointments</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="layout">
<?php include 'sidebar.php'; ?>
<div class="main">
  <div class="topbar"><h3>All Appointments</h3><span class="date">📅 <?= date('d M Y') ?></span></div>
  <div class="content">
    <?php if($msg): list($type,$text)=explode(':',$msg,2); ?>
    <div class="alert alert-success"><?= $text ?></div>
    <?php endif; ?>

    <div class="panel">
      <div class="panel-header"><h4>🔍 Filter Appointments</h4></div>
      <div class="panel-body">
        <form method="GET" style="display:flex;gap:14px;align-items:flex-end;flex-wrap:wrap">
          <div class="form-group" style="margin:0">
            <label>Date</label>
            <input type="date" name="date" value="<?= htmlspecialchars($filter_date) ?>">
          </div>
          <div class="form-group" style="margin:0">
            <label>Status</label>
            <select name="status">
              <option value="">All</option>
              <?php foreach(['Pending','Completed','Cancelled'] as $s): ?>
              <option value="<?= $s ?>" <?= $filter_status===$s?'selected':'' ?>><?= $s ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group" style="margin:0">
            <label>Doctor</label>
            <select name="doctor">
              <option value="">All Doctors</option>
              <?php while($d=mysqli_fetch_assoc($doctors)): ?>
              <option value="<?= $d['id'] ?>" <?= $filter_doctor==$d['id']?'selected':'' ?>><?= htmlspecialchars($d['name']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Filter</button>
          <a href="view_appointments.php" class="btn btn-warning">Reset</a>
          <a href="book_appointment.php" class="btn btn-success">+ Book New</a>
        </form>
      </div>
    </div>

    <div class="panel">
      <div class="panel-header">
        <h4>📋 Appointments (<?= mysqli_num_rows($appts) ?>)</h4>
        <input type="text" id="searchInput" class="search-bar" placeholder="🔍 Search...">
      </div>
      <div class="table-wrap">
        <table id="dataTable">
          <thead><tr><th>#</th><th>Patient</th><th>Doctor</th><th>Specialization</th><th>Date</th><th>Time</th><th>Notes</th><th>Status</th><th>Action</th></tr></thead>
          <tbody>
          <?php if(mysqli_num_rows($appts)===0): ?>
          <tr><td colspan="9" style="text-align:center;padding:30px;color:#999">No appointments found</td></tr>
          <?php endif; ?>
          <?php $i=1; while($r=mysqli_fetch_assoc($appts)): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><strong><?= htmlspecialchars($r['pname']) ?></strong><br><small style="color:#999"><?= htmlspecialchars($r['phone']) ?></small></td>
            <td><?= htmlspecialchars($r['dname']) ?></td>
            <td><small><?= htmlspecialchars($r['specialization'] ?: 'General') ?></small></td>
            <td><?= date('d M Y',strtotime($r['appointment_date'])) ?></td>
            <td><?= date('h:i A',strtotime($r['appointment_time'])) ?></td>
            <td><?= htmlspecialchars($r['notes'] ?: '—') ?></td>
            <td><span class="badge badge-<?= strtolower($r['status']) ?>"><?= $r['status'] ?></span></td>
            <td>
              <?php if($r['status']==='Pending'): ?>
              <a href="?cancel=<?= $r['id'] ?>" class="btn btn-danger btn-sm"
                 onclick="return confirmDelete('Cancel this appointment?')">✕ Cancel</a>
              <?php else: ?>
              <span style="color:#ccc;font-size:12px">—</span>
              <?php endif; ?>
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
