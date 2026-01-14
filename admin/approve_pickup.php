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

    // Approve booking
    $conn->query("UPDATE bookings SET pickup_status='Approved' WHERE booking_id='$id'");

    // Make vehicle unavailable
    $booking = $conn->query("SELECT vehicle_id FROM bookings WHERE booking_id='$id'")->fetch_assoc();
    $vehicle_id = $booking['vehicle_id'];
    $conn->query("UPDATE vehicles SET available='unavailable' WHERE vehicle_id='$vehicle_id'");
}

header("Location: admin_manages.php");
exit();
?>
