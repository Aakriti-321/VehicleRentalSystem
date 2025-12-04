<?php
session_start();
$conn = new mysqli("localhost", "root", "", "vehiclerentalsystem");
if ($conn->connect_error) die("Database connection failed: " . $conn->connect_error);

if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

$username = $_SESSION['username'];
$booking_id = intval($_GET['booking_id'] ?? 0);

if ($booking_id > 0) {
   
    $check = $conn->query("SELECT * FROM payments WHERE booking_id=$booking_id LIMIT 1");
    if ($check->num_rows == 0) {
        
        $res = $conn->query("SELECT total_amount FROM bookings WHERE booking_id=$booking_id");
        $amount = ($res->num_rows > 0) ? $res->fetch_assoc()['total_amount'] : 0;

       
        $status = 'completed';
        $stmt = $conn->prepare("INSERT INTO payments (booking_id, username, amount, payment_status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isds", $booking_id, $username, $amount, $status);
        $stmt->execute();
        $stmt->close();

        $update = $conn->prepare("UPDATE bookings SET payment_status='completed' WHERE booking_id=?");
        $update->bind_param("i", $booking_id);
        $update->execute();
        $update->close();
    }
} else {
    echo "Booking ID not found!";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Success</title>
</head>
<body>
<h2>Payment Successful!</h2>
<p>Booking ID: <?= htmlspecialchars($booking_id) ?></p>
<p>Status: Completed</p>
<a href="booking.php">Back to Bookings</a>
</body>
</html>
