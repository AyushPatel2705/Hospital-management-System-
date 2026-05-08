<?php
require_once 'db.php';
$msg='';
if($_SERVER['REQUEST_METHOD']=='POST'){
$name=trim($_POST['name']);
$email=trim($_POST['email']);
$password=password_hash($_POST['password'],PASSWORD_DEFAULT);
$specialization=trim($_POST['specialization']);

$stmt=mysqli_prepare($conn,"INSERT INTO users(name,email,password,role,specialization) VALUES(?,?,?,?,?)");
$role='doctor';
mysqli_stmt_bind_param($stmt,'sssss',$name,$email,$password,$role,$specialization);
if(mysqli_stmt_execute($stmt)){
$msg="Doctor registered successfully. You can login now.";
}else{
$msg="Error: email may already exist.";
}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Doctor Registration</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body class="login-body">
<div class="login-card">
<h2>Doctor Registration</h2>
<p style="color:green;"><?php echo $msg; ?></p>
<form method="POST" autocomplete="off">
<label>Name</label>
<input type="text" name="name" required>

<label>Email</label>
<input type="email" name="email" required autocomplete="off">

<label>Password</label>
<input type="password" name="password" required autocomplete="new-password">

<label>Specialization</label>
<select name="specialization" required>
<option value="">Select specialization</option>
<option>Cardiologist</option>
<option>Dermatologist</option>
<option>Neurologist</option>
<option>Pediatrician</option>
<option>Orthopedic</option>
<option>General Physician</option>
</select>

<button type="submit" class="btn-login">Register</button>
</form>
<a href="index.php">Back to login</a>
</div>
</body>
</html>
