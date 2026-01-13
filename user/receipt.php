<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "vehiclerentalsystem");
if ($conn->connect_error) die("Database connection failed");

$username = $_SESSION['username'];

if (!isset($_GET['booking_id'])) {
    die("Invalid request");
}

$booking_id = intval($_GET['booking_id']);

/* Fetch booking + payment info */
$query = "
SELECT b.*, p.payment_id, p.payment_date,p.payment_status
FROM bookings b
JOIN payments p ON b.booking_id = p.booking_id
WHERE b.booking_id = ? AND b.user_name = ? AND p.payment_status='completed'
";

$stmt = $conn->prepare($query);
$stmt->bind_param("is", $booking_id, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Receipt not found");
}

$data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
<title>Payment Receipt</title>
<style>
body {
    font-family: Arial;
    background: lightblue;
}
.receipt {
    width: 700px;
    margin: 30px auto;
    background: #fff;
    padding: 25px;
    border: 1px solid #ddd;
}
h2 {
    text-align: center;
    color:darkblue;
}
h4{
    text-align : center;
}
table {
    width: 100%;
    border-collapse: collapse;
}
td {
    padding: 10px;
}
.footer {
    text-align: center;
    margin-top: 20px;
}
.print-btn {
    background: green;
    color: white;
    padding: 10px 20px;
    border: none;
    cursor: pointer;
}
</style>
</head>

<body>

<div class="receipt">
    <img src="img/logo1.png" alt="logo" style="height: 0px; width:100">

    <h2>Easy Ride</h2>
    <h4>Payment Receipt</h4>
    <hr>
    

    <table>
        <tr>
            <td><strong>Receipt No:</strong></td>
            <td><?= $data['payment_id'] ?></td>
        </tr>
        <tr>
            <td><strong>User:</strong></td>
            <td><?= htmlspecialchars($data['user_name']) ?></td>
        </tr>
        <tr>
            <td><strong>Vehicle:</strong></td>
            <td><?= htmlspecialchars($data['vehicle_name']) ?> (<?= $data['vehicle_model'] ?>)</td>
        </tr>
        <tr>
            <td><strong>Rental Period:</strong></td>
            <td><?= $data['start_date'] ?> to <?= $data['end_date'] ?></td>
        </tr>
        <tr>
            <td><strong>Total Amount:</strong></td>
            <td>NPR <?= number_format($data['total_amount'], 2) ?></td>
        </tr>
        <tr>
            <td><strong>Payment Date:</strong></td>
            <td><?= $data['payment_date'] ?></td>
        </tr>
        <tr>
            <td><strong>Payment Status:</strong></td>
            <td style="color:green;">Completed</td>
        </tr>
    </table>

    <div class="footer">
        <button onclick="window.print()" class="print-btn">Print / Save PDF</button>
        <p>Thank you for choosing Easy Ride 🚗</p>
    </div>
</div>

</body>
</html>

