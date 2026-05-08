<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../index.php"); exit();
}
require_once '../db.php';
$did = $_SESSION['user_id'];
$msg = '';

// Mark complete
if (isset($_GET['complete'])) {
    $id = intval($_GET['complete']);
    mysqli_query($conn,"UPDATE appointments SET status='Completed' WHERE id=$id AND doctor_id=$did");
    $msg = 'success:Appointment marked as completed!';
}
// Cancel
if (isset($_GET['cancel'])) {
    $id = intval($_GET['cancel']);
    mysqli_query($conn,"UPDATE appointments SET status='Cancelled' WHERE id=$id AND doctor_id=$did");
    $msg = 'success:Appointment cancelled.';
}

$filter_status = $_GET['status'] ?? '';
$filter_date   = $_GET['date'] ?? '';
$where = "WHERE a.doctor_id=$did";
if ($filter_status) $where .= " AND a.status='".mysqli_real_escape_string($conn,$filter_status)."'";
if ($filter_date)   $where .= " AND a.appointment_date='".mysqli_real_escape_string($conn,$filter_date)."'";

$appts = mysqli_query($conn,"
  SELECT a.*, p.name AS pname, p.age, p.phone FROM appointments a
  JOIN patients p ON a.patient_id=p.id $where ORDER BY a.appointment_date DESC, a.appointment_time");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Appointments</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="layout">
<?php include 'sidebar.php'; ?>
<div class="main">
  <div class="topbar"><h3>My Appointments</h3><span class="date">📅 <?= date('d M Y') ?></span></div>
  <div class="content">
    <?php if($msg): list($type,$text)=explode(':',$msg,2); ?>
    <div class="alert alert-<?= $type ?>"><?= $text ?></div>
    <?php endif; ?>

    <div class="panel">
      <div class="panel-header">
        <h4>Filter Appointments</h4>
      </div>
      <div class="panel-body">
        <form method="GET" style="display:flex;gap:14px;align-items:flex-end;flex-wrap:wrap">
          <div class="form-group" style="margin:0">
            <label>By Date</label>
            <input type="date" name="date" value="<?= htmlspecialchars($filter_date) ?>">
          </div>
          <div class="form-group" style="margin:0">
            <label>By Status</label>
            <select name="status">
              <option value="">All Status</option>
              <option value="Pending"   <?= $filter_status==='Pending'  ?'selected':'' ?>>Pending</option>
              <option value="Completed" <?= $filter_status==='Completed'?'selected':'' ?>>Completed</option>
              <option value="Cancelled" <?= $filter_status==='Cancelled'?'selected':'' ?>>Cancelled</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Filter</button>
          <a href="appointments.php" class="btn btn-warning">Reset</a>
        </form>
      </div>
    </div>

    <div class="panel">
      <div class="panel-header"><h4>📅 Appointments (<?= mysqli_num_rows($appts) ?>)</h4></div>
      <div class="table-wrap">
        <table id="dataTable">
          <thead><tr><th>#</th><th>Patient</th><th>Phone</th><th>Date</th><th>Time</th><th>Notes</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
          <?php if(mysqli_num_rows($appts)===0): ?>
          <tr><td colspan="8" style="text-align:center;padding:30px;color:#999">No appointments found</td></tr>
          <?php endif; ?>
          <?php $i=1; while($r=mysqli_fetch_assoc($appts)): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><strong><?= htmlspecialchars($r['pname']) ?></strong><br><small style="color:#999">Age: <?= $r['age'] ?></small></td>
            <td><?= htmlspecialchars($r['phone']) ?></td>
            <td><?= date('d M Y', strtotime($r['appointment_date'])) ?></td>
            <td><?= date('h:i A', strtotime($r['appointment_time'])) ?></td>
            <td><?= htmlspecialchars($r['notes'] ?: '—') ?></td>
            <td><span class="badge badge-<?= strtolower($r['status']) ?>"><?= $r['status'] ?></span></td>
            <td style="white-space:nowrap">
              <?php if($r['status']==='Pending'): ?>
              <a href="?complete=<?= $r['id'] ?>" class="btn btn-success btn-sm">✓ Complete</a>
              <a href="?cancel=<?= $r['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Cancel this appointment?')">✕</a>
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
