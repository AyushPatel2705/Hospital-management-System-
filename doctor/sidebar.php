<?php
// Doctor sidebar include
$current = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon">🏥</div>
    <h2>Hospital<br>Management</h2>
    <p>System v1.0</p>
  </div>
  <div class="sidebar-user">
    <div class="user-avatar">🩺</div>
    <div class="user-info">
      <div class="name"><?= htmlspecialchars($_SESSION['name']) ?></div>
      <div class="role">Doctor</div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-label">Main Menu</div>
    <a href="dashboard.php" class="<?= $current==='dashboard.php'?'active':'' ?>">
      <span class="nav-icon">📊</span> Dashboard
    </a>
    <a href="appointments.php" class="<?= $current==='appointments.php'?'active':'' ?>">
      <span class="nav-icon">📅</span> My Appointments
    </a>
    <a href="patients.php" class="<?= $current==='patients.php'?'active':'' ?>">
      <span class="nav-icon">👥</span> Patient List
    </a>
    <a href="prescriptions.php" class="<?= $current==='prescriptions.php'?'active':'' ?>">
      <span class="nav-icon">💊</span> Prescriptions
    </a>
  </nav>
  <div class="sidebar-logout">
    <a href="../logout.php">🚪 Logout</a>
  </div>
</div>
