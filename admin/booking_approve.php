<?php
session_start();
$conn = new mysqli("localhost","root","","projcet I");
if ($conn->connect_error) die("Connection Failed");

if (!isset($_SESSION['admin'])) {
    header("Location: index.html");
    exit();
}

$id = $_GET['id'];
$conn->query("UPDATE bookings SET status='confirmed' WHERE booking_id='$id'");

header("Location: admin_manages.php");
exit();
?>
