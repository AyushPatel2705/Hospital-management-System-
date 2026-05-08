<?php if(isset($_GET['download'])){ header('Content-Type: application/octet-stream'); header('Content-Disposition: attachment; filename="prescription.html"'); } ?>
<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
require_once 'db.php';
$id = intval($_GET['id'] ?? 0);
$rx = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT rx.*, p.name AS pname, p.age, p.gender, p.phone,
         u.name AS dname, u.specialization, a.appointment_date
  FROM prescriptions rx
  JOIN patients p ON rx.patient_id=p.id
  JOIN users u ON rx.doctor_id=u.id
  LEFT JOIN appointments a ON rx.appointment_id=a.id
  WHERE rx.id=$id"));
if(!$rx){ die("Prescription not found."); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Prescription #<?= $id ?></title>
<link rel="stylesheet" href="css/style.css">
<style>
body { font-family: 'Poppins', sans-serif; background: #fff; padding: 30px; color: #2c3e50; }
.rx-header { text-align: center; border-bottom: 3px solid #1a3c5e; padding-bottom: 16px; margin-bottom: 20px; }
.rx-header h1 { color: #1a3c5e; font-size: 22px; }
.rx-header p  { color: #7f8c8d; font-size: 13px; }
.rx-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; font-size: 13px; }
.rx-grid div { padding: 8px 0; border-bottom: 1px dashed #eee; }
.rx-grid strong { color: #1a3c5e; }
.rx-body { background: #f4f6f8; border-radius: 8px; padding: 18px; margin-bottom: 20px; }
.rx-body h3 { color: #1a3c5e; margin-bottom: 12px; font-size: 15px; }
.rx-body p { margin-bottom: 8px; font-size: 13.5px; }
.rx-sign { text-align: right; margin-top: 40px; border-top: 1px solid #ccc; padding-top: 12px; }
.btn-print { background: #1a3c5e; color: #fff; border: none; padding: 10px 24px; border-radius: 8px; cursor: pointer; font-size: 14px; font-family: inherit; }
@media print { .btn-print { display: none; } }
</style>
</head>
<body>
<div class="rx-header">
  <h1>🏥 Hospital Management System</h1>
  <p>Medical Prescription</p>
</div>
<div class="rx-grid">
  <div><strong>Patient:</strong> <?= htmlspecialchars($rx['pname']) ?></div>
  <div><strong>Age/Gender:</strong> <?= $rx['age'] ?> / <?= $rx['gender'] ?></div>
  <div><strong>Phone:</strong> <?= htmlspecialchars($rx['phone']) ?></div>
  <div><strong>Date:</strong> <?= date('d M Y', strtotime($rx['created_at'])) ?></div>
  <div><strong>Doctor:</strong> <?= htmlspecialchars($rx['dname']) ?></div>
  <div><strong>Specialization:</strong> <?= htmlspecialchars($rx['specialization'] ?: 'General') ?></div>
</div>
<div class="rx-body">
  <h3>💊 Prescription</h3>
  <p><strong>Medicine(s):</strong> <?= htmlspecialchars($rx['medicine']) ?></p>
  <p><strong>Dosage:</strong> <?= htmlspecialchars($rx['dosage']) ?></p>
  <?php if($rx['instructions']): ?>
  <p><strong>Instructions:</strong> <?= htmlspecialchars($rx['instructions']) ?></p>
  <?php endif; ?>
</div>
<div class="rx-sign">
  <p><strong>Dr. <?= htmlspecialchars($rx['dname']) ?></strong></p>
  <p style="font-size:12px;color:#999"><?= htmlspecialchars($rx['specialization'] ?: 'General Physician') ?></p>
  <p style="font-size:12px;color:#999">Signature: ____________________</p>
</div>
<div style="text-align:center;margin-top:20px">
  <button class="btn-print" onclick="window.print()">🖨️ Print Prescription</button>
</div>
</body>
</html>
