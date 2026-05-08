<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../index.php"); exit();
}
require_once '../db.php';
$did = $_SESSION['user_id'];

// Patients who had appointments with this doctor
$patients = mysqli_query($conn,"
  SELECT DISTINCT p.*, COUNT(a.id) AS total_appts FROM patients p
  JOIN appointments a ON a.patient_id=p.id
  WHERE a.doctor_id=$did GROUP BY p.id ORDER BY p.name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Patient List</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="layout">
<?php include 'sidebar.php'; ?>
<div class="main">
  <div class="topbar"><h3>My Patients</h3><span class="date">📅 <?= date('d M Y') ?></span></div>
  <div class="content">
    <div class="panel">
      <div class="panel-header">
        <h4>👥 Patient List (<?= mysqli_num_rows($patients) ?>)</h4>
        <input type="text" id="searchInput" class="search-bar" placeholder="🔍 Search patient...">
      </div>
      <div class="table-wrap">
        <table id="dataTable">
          <thead><tr><th>#</th><th>Name</th><th>Age</th><th>Gender</th><th>Phone</th><th>Address</th><th>Visits</th><th>Action</th></tr></thead>
          <tbody>
          <?php if(mysqli_num_rows($patients)===0): ?>
          <tr><td colspan="8" style="text-align:center;padding:30px;color:#999">No patients yet</td></tr>
          <?php endif; ?>
          <?php $i=1; while($r=mysqli_fetch_assoc($patients)): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><strong><?= htmlspecialchars($r['name']) ?></strong></td>
            <td><?= $r['age'] ?></td>
            <td><?= $r['gender'] ?></td>
            <td><?= htmlspecialchars($r['phone']) ?></td>
            <td><?= htmlspecialchars($r['address'] ?: '—') ?></td>
            <td><span class="badge badge-completed"><?= $r['total_appts'] ?> visits</span></td>
            <td><a href="prescriptions.php?pid=<?= $r['id'] ?>" class="btn btn-primary btn-sm">💊 Prescriptions</a></td>
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
