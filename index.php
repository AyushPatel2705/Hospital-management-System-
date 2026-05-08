<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'doctor' ? 'doctor/dashboard.php' : 'staff/dashboard.php'));
    exit();
}
require_once 'db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass  = trim($_POST['password']);
    $stmt  = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $res  = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);
    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];
        header("Location: " . ($user['role'] === 'doctor' ? 'doctor/dashboard.php' : 'staff/dashboard.php'));
        exit();
    } else {
        $error = 'Invalid email or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Hospital Management System — Login</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body class="login-body">
<div class="login-card">
  <div class="login-logo">
    <div class="icon">🏥</div>
    <h1>Hospital Management</h1>
    <p>System v1.0</p>
  </div>
  <?php if ($error): ?>
  <div class="error-msg"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST" autocomplete="off">
    <div class="form-group">
      <label>Email Address</label>
      <input type="email" name="email" placeholder="Enter your email" required
             value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" placeholder="Enter your password" required>
    </div>
    <button type="submit" class="btn-login">Login →</button>
  </form><p><a href="register_doctor.php">Register Doctor</a> | <a href="register_staff.php">Register Staff</a></p>
  
</div>
</body>
</html>
