<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../index.php"); exit();
}
require_once '../db.php';
$uid = $_SESSION['user_id'];
$msg = '';

// Add patient
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_patient'])) {
    $name   = mysqli_real_escape_string($conn,$_POST['name']);
    $age    = intval($_POST['age']);
    $gender = mysqli_real_escape_string($conn,$_POST['gender']);
    $phone  = mysqli_real_escape_string($conn,$_POST['phone']);
    $addr   = mysqli_real_escape_string($conn,$_POST['address']);
    mysqli_query($conn,"INSERT INTO patients(name,age,gender,phone,address,registered_by) VALUES('$name',$age,'$gender','$phone','$addr',$uid)");
    $msg = 'success:Patient added successfully!';
}

// Edit patient
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['edit_patient'])) {
    $id     = intval($_POST['id']);
    $name   = mysqli_real_escape_string($conn,$_POST['name']);
    $age    = intval($_POST['age']);
    $gender = mysqli_real_escape_string($conn,$_POST['gender']);
    $phone  = mysqli_real_escape_string($conn,$_POST['phone']);
    $addr   = mysqli_real_escape_string($conn,$_POST['address']);
    mysqli_query($conn,"UPDATE patients SET name='$name',age=$age,gender='$gender',phone='$phone',address='$addr' WHERE id=$id");
    $msg = 'success:Patient updated successfully!';
}

// Delete patient
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn,"DELETE FROM patients WHERE id=$id");
    $msg = 'success:Patient deleted.';
}

// Fetch for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM patients WHERE id=".intval($_GET['edit'])));
}

$patients = mysqli_query($conn,"SELECT * FROM patients ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manage Patients</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="layout">
<?php include 'sidebar.php'; ?>
<div class="main">
  <div class="topbar"><h3>Manage Patients</h3><span class="date">📅 <?= date('d M Y') ?></span></div>
  <div class="content">
    <?php if($msg): list($type,$text)=explode(':',$msg,2); ?>
    <div class="alert alert-<?= $type ?>"><?= $text ?></div>
    <?php endif; ?>

    <div class="panel">
      <div class="panel-header">
        <h4><?= $edit_data ? '✏️ Edit Patient' : '➕ Add New Patient' ?></h4>
        <?php if($edit_data): ?><a href="manage_patients.php" class="btn btn-warning btn-sm">Cancel Edit</a><?php endif; ?>
      </div>
      <div class="panel-body">
        <form method="POST">
          <?php if($edit_data): ?>
          <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
          <?php endif; ?>
          <div class="form-grid">
            <div class="form-group">
              <label>Full Name *</label>
              <input type="text" name="name" placeholder="Patient full name" required
                     value="<?= htmlspecialchars($edit_data['name'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label>Age *</label>
              <input type="number" name="age" min="1" max="150" required
                     value="<?= $edit_data['age'] ?? '' ?>">
            </div>
            <div class="form-group">
              <label>Gender *</label>
              <select name="gender" required>
                <option value="">Select Gender</option>
                <?php foreach(['Male','Female','Other'] as $g): ?>
                <option value="<?= $g ?>" <?= ($edit_data['gender']??'')===$g?'selected':'' ?>><?= $g ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Phone *</label>
              <input type="text" name="phone" placeholder="10-digit phone" required
                     value="<?= htmlspecialchars($edit_data['phone'] ?? '') ?>">
            </div>
            <div class="form-group form-full">
              <label>Address</label>
              <textarea name="address" placeholder="Patient address"><?= htmlspecialchars($edit_data['address'] ?? '') ?></textarea>
            </div>
          </div>
          <button type="submit" name="<?= $edit_data ? 'edit_patient' : 'add_patient' ?>" class="btn btn-primary">
            <?= $edit_data ? '💾 Update Patient' : '➕ Add Patient' ?>
          </button>
        </form>
      </div>
    </div>

    <div class="panel">
      <div class="panel-header">
        <h4>👥 All Patients (<?= mysqli_num_rows($patients) ?>)</h4>
        <input type="text" id="searchInput" class="search-bar" placeholder="🔍 Search patient...">
      </div>
      <div class="table-wrap">
        <table id="dataTable">
          <thead><tr><th>#</th><th>Name</th><th>Age</th><th>Gender</th><th>Phone</th><th>Address</th><th>Registered</th><th>Actions</th></tr></thead>
          <tbody>
          <?php if(mysqli_num_rows($patients)===0): ?>
          <tr><td colspan="8" style="text-align:center;padding:30px;color:#999">No patients registered yet</td></tr>
          <?php endif; ?>
          <?php $i=1; mysqli_data_seek($patients,0); while($r=mysqli_fetch_assoc($patients)): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><strong><?= htmlspecialchars($r['name']) ?></strong></td>
            <td><?= $r['age'] ?></td>
            <td><?= $r['gender'] ?></td>
            <td><?= htmlspecialchars($r['phone']) ?></td>
            <td><?= htmlspecialchars($r['address'] ?: '—') ?></td>
            <td><?= date('d M Y',strtotime($r['created_at'])) ?></td>
            <td style="white-space:nowrap">
              <a href="?edit=<?= $r['id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
              <a href="?delete=<?= $r['id'] ?>" class="btn btn-danger btn-sm"
                 onclick="return confirmDelete('Delete patient <?= htmlspecialchars(addslashes($r['name'])) ?>?')">🗑️ Delete</a>
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
