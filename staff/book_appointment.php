<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../index.php"); exit();
}
require_once '../db.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $pid   = intval($_POST['patient_id']);
    $did   = intval($_POST['doctor_id']);
    $date  = mysqli_real_escape_string($conn,$_POST['appointment_date']);
    $time  = mysqli_real_escape_string($conn,$_POST['appointment_time']);
    $notes = mysqli_real_escape_string($conn,$_POST['notes']);
    // Check conflict
    $check = mysqli_fetch_row(mysqli_query($conn,"SELECT id FROM appointments WHERE doctor_id=$did AND appointment_date='$date' AND appointment_time='$time' AND status!='Cancelled'"));
    if ($check) {
        $msg = 'danger:Doctor already has an appointment at this time. Please choose another slot.';
    } else {
        mysqli_query($conn,"INSERT INTO appointments(patient_id,doctor_id,appointment_date,appointment_time,notes) VALUES($pid,$did,'$date','$time','$notes')");
        $msg = 'success:Appointment booked successfully!';
    }
}

$patients = mysqli_query($conn,"SELECT id,name FROM patients ORDER BY name");
$doctors  = mysqli_query($conn,"SELECT id,name,specialization FROM users WHERE role='doctor' ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Book Appointment</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="layout">
<?php include 'sidebar.php'; ?>
<div class="main">
  <div class="topbar"><h3>Book Appointment</h3><span class="date">📅 <?= date('d M Y') ?></span></div>
  <div class="content">
    <?php if($msg): list($type,$text)=explode(':',$msg,2); ?>
    <div class="alert alert-<?= $type ?>"><?= $text ?></div>
    <?php endif; ?>

    <div class="panel" style="max-width:700px">
      <div class="panel-header"><h4>📅 Schedule New Appointment</h4></div>
      <div class="panel-body">
        <form method="POST">
          <div class="form-grid">
            <div class="form-group">
              <label>Select Patient *</label>
              <select name="patient_id" required>
                <option value="">-- Choose Patient --</option>
                <?php while($p=mysqli_fetch_assoc($patients)): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Select Doctor *</label>
              <select name="doctor_id" required>
                <option value="">-- Choose Doctor --</option>
                <?php while($d=mysqli_fetch_assoc($doctors)): ?>
                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?> — <?= htmlspecialchars($d['specialization'] ?: 'General') ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Appointment Date *</label>
              <input type="date" name="appointment_date" required>
            </div>
            <div class="form-group">
              <label>Appointment Time *</label>
              <input type="time" name="appointment_time" required>
            </div>
            <div class="form-group form-full">
              <label>Notes / Reason for Visit</label>
              <textarea name="notes" placeholder="e.g. Fever and headache for 3 days..."></textarea>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">📅 Book Appointment</button>
        </form>
      </div>
    </div>

    <div class="panel" style="max-width:700px">
      <div class="panel-header"><h4>ℹ️ Appointment Guidelines</h4></div>
      <div class="panel-body" style="font-size:13.5px;line-height:1.8;color:#555">
        <p>• Please ensure the patient is registered before booking an appointment.</p>
        <p>• Verify doctor availability before selecting a time slot.</p>
        <p>• The system will alert you if a time slot is already taken.</p>
        <p>• Appointments can be cancelled from the <a href="view_appointments.php">View Appointments</a> page.</p>
      </div>
    </div>
  </div>
</div>
</div>
<script src="../js/main.js"></script>
</body>
</html>
