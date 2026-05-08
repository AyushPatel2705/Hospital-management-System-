<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon">🏥</div>
    <h2>Hospital<br>Management</h2>
    <p>System v1.0</p>
  </div>
  <div class="sidebar-user">
    <div class="user-avatar">👤</div>
    <div class="user-info">
      <div class="name"><?= htmlspecialchars($_SESSION['name']) ?></div>
      <div class="role">Staff</div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-label">Main Menu</div>
    <a href="dashboard.php" class="<?= $current==='dashboard.php'?'active':'' ?>">
      <span class="nav-icon">📊</span> Dashboard
    </a>
    <a href="manage_patients.php" class="<?= $current==='manage_patients.php'?'active':'' ?>">
      <span class="nav-icon">👥</span> Manage Patients
    </a>
    <a href="book_appointment.php" class="<?= $current==='book_appointment.php'?'active':'' ?>">
      <span class="nav-icon">📅</span> Book Appointment
    </a>
    <a href="view_appointments.php" class="<?= $current==='view_appointments.php'?'active':'' ?>">
      <span class="nav-icon">📋</span> View Appointments
    </a>
  </nav>
  <div class="sidebar-logout">
    <a href="../logout.php">🚪 Logout</a>
  </div>
</div>
