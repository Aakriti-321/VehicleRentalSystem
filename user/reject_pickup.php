<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: index.html");
    exit();
}

$conn = new mysqli("localhost","root","","vehiclerentalsystem");
if($conn->connect_error) die("DB Error");


if(isset($_GET['id'])){
    $id = $_GET['id'];

    $conn->query("UPDATE bookings SET pickup_status='Rejected' WHERE booking_id='$id'");

}

header("Location: admin_manages.php");
exit();
?>
