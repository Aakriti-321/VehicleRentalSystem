<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "vehiclerentalsystem");
if ($conn->connect_error) die("Database connection failed");

$username = $_SESSION['username'];

// eSewa configuration
$secretKey = "8gBm/:&EnhH.1/q"; 
$product_code = "EPAYTEST";
$signed_field_names = "total_amount,transaction_uuid,product_code";

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vehicle_id'])) {
    $vehicle_id = intval($_POST['vehicle_id']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $today = date('Y-m-d');

    // Check if vehicle is available
    $stmt = $conn->prepare("
        SELECT * FROM bookings
        WHERE vehicle_id = ?
          AND pickup_status = 'approved'
          AND NOT (end_date < ? OR start_date > ?)
    ");
    $stmt->bind_param("iss", $vehicle_id, $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Vehicle not available for the selected dates'); window.location='rent.php';</script>";
        exit();
    }
    $stmt->close();

    // Fetch vehicle info
    $result = $conn->query("SELECT vehicle_name, model, price_per_day FROM vehicles WHERE vehicle_id = $vehicle_id");
    if ($result->num_rows == 0) {
        echo "<script>alert('Vehicle not found'); window.location='rent.php';</script>";
        exit();
    }
    $vehicle = $result->fetch_assoc();
    $vehicle_name = $vehicle['vehicle_name'];
    $vehicle_model = $vehicle['model'];
    $price_per_day = floatval($vehicle['price_per_day']);

    $days = (strtotime($end_date) - strtotime($start_date)) / 86400 + 1;
    $total_amount = $price_per_day * $days;
    $pickup_status = "pending";

    // Insert booking
    $stmt = $conn->prepare("
        INSERT INTO bookings 
        (user_name, vehicle_id, vehicle_name, vehicle_model, start_date, end_date, total_amount, pickup_status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sissssds", 
        $username, 
        $vehicle_id, 
        $vehicle_name, 
        $vehicle_model, 
        $start_date, 
        $end_date, 
        $total_amount,
        $pickup_status
    );
    $stmt->execute();
    $stmt->close();
}

// Fetch user bookings
$query = "SELECT * FROM bookings WHERE user_name='$username' ORDER BY start_date DESC";
$bookings = $conn->query($query);

// Fetch completed payments
$payments = [];
$result = $conn->query("SELECT booking_id FROM payments WHERE payment_status='completed'");
while ($row_p = $result->fetch_assoc()) {
    $payments[$row_p['booking_id']] = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Bookings</title>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="mybooking.css">
</head>
<body>

<header>
    <img src="./img/logo1.png" alt="Logo">
    <h4>Easy Ride</h4>
</header>

<div class="flex">
    <div class="sidebar">
        <img src="https://t4.ftcdn.net/jpg/16/09/59/37/360_F_1609593795_Ae1PPBgGSiy2tKw4GWXeXJtBTQn3dWpn.jpg" alt="Profile">
        <h4>Welcome, <?= htmlspecialchars($username) ?></h4>
        <a href="dashboard.php" class="menu"><span class="material-icons">dashboard</span> Dashboard</a>
        <a href="rent.php" class="menu"><span class="material-icons">directions_car</span> Rent Now</a>
        <a href="booking.php" class="menu"><span class="material-icons">calendar_today</span> My Booking</a>
        <a href="logout.php" class="menu"><span class="material-icons">logout</span> Logout</a>
    </div>

    <div class="main">
        <h2>My Bookings</h2>
        <table>
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>Vehicle Name</th>
                    <th>Model</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Total Amount</th>
                    <th>Action</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($bookings->num_rows > 0): 
                    $sn = 1;
                    while ($row = $bookings->fetch_assoc()):
                        $total_amount_str = number_format($row['total_amount'], 2, '.', '');
                ?>
                <tr>
                    <td><?= $sn ?></td>
                    <td><?= htmlspecialchars($row['vehicle_name']) ?></td>
                    <td><?= htmlspecialchars($row['vehicle_model']) ?></td>
                    <td><?= $row['start_date'] ?></td>
                    <td><?= $row['end_date'] ?></td>
                    <td>NPR <?= $total_amount_str ?></td>
                    <td>
                        <?php 
                        if (isset($row['pickup_status'])) {
                            if ($row['pickup_status'] === 'approved') {
                              echo '<span class="paid-status">Booked</span><br>
      <a href="receipt.php?booking_id='.$row['booking_id'].'"br
      >View Receipt</a>';

                                
                            } elseif ($row['pickup_status'] === 'rejected') {
                                echo '<span class="rejected-status">Rejected</span>';
                            } else {
                                echo 'Pending';
                            }
                        } else {
                            echo 'Pending';
                        }
                        ?>
                    </td>
                    <td>
                        <?php if (isset($payments[$row['booking_id']])): ?>
                            <span class="paid-status">Paid</span><br>
                        <?php else: 
                            $transaction_uuid = uniqid('TXN_');
                            $signed_fields = [
                                'total_amount' => $total_amount_str,
                                'transaction_uuid' => $transaction_uuid,
                                'product_code' => $product_code
                            ];
                            $fields_order = explode(',', $signed_field_names);
                            $data_to_sign = [];
                            foreach ($fields_order as $field) $data_to_sign[] = $field . '=' . $signed_fields[$field];
                            $data_string = implode(',', $data_to_sign);
                            $hash = hash_hmac('sha256', $data_string, $secretKey, true);
                            $signature = base64_encode($hash);
                        ?>
                            <form method="POST" action="https://rc-epay.esewa.com.np/api/epay/main/v2/form">
                                <input type="hidden" name="amount" value="<?= $total_amount_str ?>">
                                <input type="hidden" name="tax_amount" value="0">
                                <input type="hidden" name="total_amount" value="<?= $total_amount_str ?>">
                                <input type="hidden" name="transaction_uuid" value="<?= $transaction_uuid ?>">
                                <input type="hidden" name="product_code" value="<?= $product_code ?>">
                                <input type="hidden" name="product_service_charge" value="0">
                                <input type="hidden" name="product_delivery_charge" value="0">
                                <input type="hidden" name="success_url" value="http://localhost/vehiclerentalsystem/project/success.php?booking_id=<?= $row['booking_id'] ?>">
                                <input type="hidden" name="failure_url" value="http://localhost/vehiclerentalsystem/project/failure.php">
                                <input type="hidden" name="signed_field_names" value="<?= $signed_field_names ?>">
                                <input type="hidden" name="signature" value="<?= $signature ?>">
                                <input type="submit" class="pay-button" value="Pay with eSewa">
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php $sn++; endwhile; else: ?>
                <tr><td colspan="8">No bookings found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
