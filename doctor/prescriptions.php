<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../index.php"); exit();
}
require_once '../db.php';
$did = $_SESSION['user_id'];
$msg = '';

// Pre-fill from appointment link
$appt_id = intval($_GET['appt'] ?? 0);
$pre_pid  = intval($_GET['pid']  ?? 0);

// Save prescription
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['save_rx'])) {
    $apid = intval($_POST['appointment_id']);
    $pid  = intval($_POST['patient_id']);
    $med  = mysqli_real_escape_string($conn, $_POST['medicine']);
    $dos  = mysqli_real_escape_string($conn, $_POST['dosage']);
    $ins  = mysqli_real_escape_string($conn, $_POST['instructions']);
    mysqli_query($conn,"INSERT INTO prescriptions(appointment_id,doctor_id,patient_id,medicine,dosage,instructions) VALUES($apid,$did,$pid,'$med','$dos','$ins')");
    // Mark appointment completed
    if ($apid) mysqli_query($conn,"UPDATE appointments SET status='Completed' WHERE id=$apid AND doctor_id=$did");
    $msg = 'success:Prescription saved successfully!';
    $appt_id = 0; $pre_pid = 0;
}

// Get appointments for dropdown
$appts_list = mysqli_query($conn,"
  SELECT a.id, a.appointment_date, p.name AS pname, p.id AS pid
  FROM appointments a JOIN patients p ON a.patient_id=p.id
  WHERE a.doctor_id=$did ORDER BY a.appointment_date DESC");

// Get all prescriptions by this doctor
$filter_pid = intval($_GET['pid'] ?? 0);
$where = "WHERE rx.doctor_id=$did";
if ($filter_pid) $where .= " AND rx.patient_id=$filter_pid";
$rx_list = mysqli_query($conn,"
  SELECT rx.*, p.name AS pname, a.appointment_date FROM prescriptions rx
  JOIN patients p ON rx.patient_id=p.id
  LEFT JOIN appointments a ON rx.appointment_id=a.id
  $where ORDER BY rx.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Prescriptions</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="layout">
<?php include 'sidebar.php'; ?>
<div class="main">
  <div class="topbar"><h3>Prescriptions</h3><span class="date">📅 <?= date('d M Y') ?></span></div>
  <div class="content">
    <?php if($msg): list($type,$text)=explode(':',$msg,2); ?>
    <div class="alert alert-<?= $type ?>"><?= $text ?></div>
    <?php endif; ?>

    <div class="panel">
      <div class="panel-header"><h4>💊 Write New Prescription</h4></div>
      <div class="panel-body">
        <form method="POST">
          <div class="form-grid">
            <div class="form-group">
              <label>Select Appointment</label>
              <select name="appointment_id" id="apptSelect" required onchange="fillPatient(this)">
                <option value="">-- Select Appointment --</option>
                <?php
                mysqli_data_seek($appts_list,0);
                while($a=mysqli_fetch_assoc($appts_list)):
                ?>
                <option value="<?= $a['id'] ?>" data-pid="<?= $a['pid'] ?>"
                  <?= $a['id']===$appt_id?'selected':'' ?>>
                  <?= htmlspecialchars($a['pname']) ?> — <?= date('d M Y',strtotime($a['appointment_date'])) ?>
                </option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Patient ID (auto-filled)</label>
              <input type="number" name="patient_id" id="patientId" value="<?= $pre_pid ?>" required readonly style="background:#f4f6f8">
            </div>
            <div class="form-group">
              <label>Medicine(s)</label>
              <input type="text" name="medicine" placeholder="e.g. Paracetamol 500mg, Amoxicillin 250mg" required>
            </div>
            <div class="form-group">
              <label>Dosage</label>
              <input type="text" name="dosage" placeholder="e.g. 1 tablet three times daily" required>
            </div>
            <div class="form-group form-full">
              <label>Instructions / Notes</label>
              <textarea name="instructions" placeholder="e.g. Take after meals, drink plenty of water..."></textarea>
            </div>
          </div>
          <button type="submit" name="save_rx" class="btn btn-primary">💾 Save Prescription</button>
        </form>
      </div>
    </div>

    <div class="panel">
      <div class="panel-header">
        <h4>📋 Prescription History (<?= mysqli_num_rows($rx_list) ?>)</h4>
        <input type="text" id="searchInput" class="search-bar" placeholder="🔍 Search...">
      </div>
      <div class="table-wrap">
        <table id="dataTable">
          <thead><tr><th>#</th><th>Patient</th><th>Medicine</th><th>Dosage</th><th>Instructions</th><th>Date</th><th>Print</th></tr></thead>
          <tbody>
          <?php if(mysqli_num_rows($rx_list)===0): ?>
          <tr><td colspan="7" style="text-align:center;padding:30px;color:#999">No prescriptions yet</td></tr>
          <?php endif; ?>
          <?php $i=1; while($r=mysqli_fetch_assoc($rx_list)): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><strong><?= htmlspecialchars($r['pname']) ?></strong></td>
            <td><?= htmlspecialchars($r['medicine']) ?></td>
            <td><?= htmlspecialchars($r['dosage']) ?></td>
            <td><?= htmlspecialchars($r['instructions'] ?: '—') ?></td>
            <td><?= date('d M Y', strtotime($r['created_at'])) ?></td>
            <td><button onclick="downloadRx(<?= $r['id'] ?>)" class="btn btn-warning btn-sm">⬇️ Download</button></td>
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
<script>
function fillPatient(sel) {
  const opt = sel.options[sel.selectedIndex];
  document.getElementById('patientId').value = opt.dataset.pid || '';
}
// Auto fill if coming from appointment link
window.addEventListener('DOMContentLoaded', function() {
  const sel = document.getElementById('apptSelect');
  if (sel.value) fillPatient(sel);
});
function downloadRx(id) {
  window.location.href = '../print_rx.php?id='+id+'&download=1';
}
</script>
</body>
</html>
