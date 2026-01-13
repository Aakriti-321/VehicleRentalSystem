<?php
session_start();
$conn = new mysqli("localhost", "root", "", "vehiclerentalsystem");
if ($conn->connect_error) die("Database connection failed: " . $conn->connect_error);

$booking_id = intval($_GET['booking_id'] ?? 0);


if ($booking_id > 0) {
    $update = $conn->prepare("UPDATE bookings SET payment_status='failed' WHERE booking_id=?");
    $update->bind_param("i", $booking_id);
    $update->execute();
    $update->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Failed</title>
</head>
<body>
    <h2>Payment Failed!</h2>
    <?php if ($booking_id > 0): ?>
        <p>Booking ID: <?= htmlspecialchars($booking_id) ?> could not be processed.</p>
    <?php endif; ?>
    <p>Your payment was not successful. Please try again.</p>
    <a href="booking.php">Back to Bookings</a>
</body>
</html>
