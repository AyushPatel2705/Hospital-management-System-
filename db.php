<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "hospital_db";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("<div style='font-family:Poppins,sans-serif;padding:20px;color:red;'>
        <h3>Database Connection Failed</h3>
        <p>" . mysqli_connect_error() . "</p>
        <p>Please import <strong>hospital_db.sql</strong> in phpMyAdmin first.</p>
    </div>");
}
?>
