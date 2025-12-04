<?php 
session_start(); 
$conn = new mysqli("localhost","root","","vehiclerentalsystem"); 
if ($conn->connect_error) die("Connection Failed");

if(!isset($_SESSION['admin'])){
    header("Location: index.html");
    exit();
}

$admin_email = $_SESSION['admin'];


$bookings = $conn->query("
    SELECT b.*, v.model 
    FROM bookings b
    JOIN payments p ON b.booking_id = p.booking_id
    JOIN vehicles v ON b.vehicle_id = v.vehicle_id
    WHERE p.payment_status = 'completed'
    ORDER BY b.booking_id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Bookings</title>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
<link rel="stylesheet" href="admin_uder.css">
</head>
<body>

<header>
    <img src="logo1.jpg" alt="Logo">
    <h4>Easy Ride</h4>
</header>

<div class="flex">

    <div class="sidebar">
        <img src="https://t4.ftcdn.net/jpg/16/09/59/37/360_F_1609593795_Ae1PPBgGSiy2tKw4GWXeXJtBTQn3dWpn.jpg" alt="Profile">
        <h4>Welcome, <?= $admin_email ?></h4>

        <a href="admin_dashboard.php" class="menu">
            <span class="material-icons-sharp">dashboard</span> Dashboard
        </a>

        <a href="admin_manages.php" class="menu">
            <span class="material-icons-sharp">people</span> Bookings
        </a>

        <a href="admin_vehiclemanages.php" class="menu">
            <span class="material-icons-sharp">directions_car</span> Vehicles
        </a>

        <a href="logout.php" class="menu">
            <span class="material-icons-sharp">logout</span> Logout
        </a>
    </div>

    <div class="main">
        <h2> Booking Requests</h2>
        <table>
            <tr>
                <th>Booking ID</th>
                <th>User</th>
                <th>Vehicle</th>
                <th>Model</th>
                <th>Start</th>
                <th>End</th>
                <th>Total Amount</th>
                <th>Pickup Status</th>
            </tr>

            <?php while ($b = $bookings->fetch_assoc()): ?>
            <tr>
                <td><?= $b['booking_id'] ?></td>
                <td><?= $b['user_name'] ?></td>
                <td><?= $b['vehicle_name'] ?></td>
                <td><?= $b['model'] ?></td>
                <td><?= $b['start_date'] ?></td>
                <td><?= $b['end_date'] ?></td>
                <td>NPR <?= $b['total_amount'] ?></td>
<td>
    <?php 
    // Normalize status to avoid case issues
    $status = ucfirst(strtolower($b['pickup_status']));

    if ($status === 'Pending'): ?>
        <a href="approve_pickup.php?id=<?= $b['booking_id'] ?>" 
           class="button" style="background:green;color:white;">Approve</a>
        <a href="reject_pickup.php?id=<?= $b['booking_id'] ?>" 
           class="button" style="background:red;color:white;">Reject</a>
    <?php elseif ($status === 'Approved'): ?>
        <span style="color:green;font-weight:bold;">Approved</span>
    <?php elseif ($status === 'Rejected'): ?>
        <span style="color:red;font-weight:bold;">Rejected</span>
    <?php else: ?>
        <span style="color:orange;font-weight:bold;">Unknown</span>
    <?php endif; ?>
</td>

            </tr>
            <?php endwhile; ?>

        </table>
    </div>
</div>
</body>
</html>
