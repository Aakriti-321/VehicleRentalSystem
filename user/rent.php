<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "vehiclerentalsystem");
if ($conn->connect_error) die("Connection Failed: " . $conn->connect_error);

$email = $_SESSION['email'];
$res = $conn->query("SELECT username FROM users WHERE email='$email'");
$username = ($res->num_rows > 0) ? $res->fetch_assoc()['username'] : "User";

/* ---------------------------------------------------
   FIXED AVAILABILITY SYSTEM
   A vehicle is unavailable ONLY if today's date 
   is inside an approved booking range.
---------------------------------------------------- */

$today = date("Y-m-d");

$allVehicles = $conn->query("SELECT vehicle_id FROM vehicles");

if ($allVehicles && $allVehicles->num_rows > 0) {
    while ($v = $allVehicles->fetch_assoc()) {
        $vehicle_id = $v['vehicle_id'];

        // Check if vehicle is booked TODAY
        $booking = $conn->query("
            SELECT * FROM bookings 
            WHERE vehicle_id='$vehicle_id'
            AND pickup_status='Approved'
            AND start_date <= '$today'
            AND end_date >= '$today'
        ");

        if ($booking && $booking->num_rows > 0) {
            // Booked today → unavailable
            $conn->query("UPDATE vehicles SET available='unavailable' WHERE vehicle_id='$vehicle_id'");
        } else {
            // Not booked today → available
            $conn->query("UPDATE vehicles SET available='available' WHERE vehicle_id='$vehicle_id'");
        }
    }
}

/* ---------------------------------------------------
   FETCH VEHICLES
---------------------------------------------------- */

$vehicles = $conn->query("SELECT * FROM vehicles");

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rent Vehicles</title>
<link rel="stylesheet" href="rent.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
</head>
<body>

<header style="width: 200%;
background: linear-gradient(90deg, #1e90ff, #3692b0);
padding: 15px 0;
text-align: left;
color: white;
font-weight: bold;
font-size: 24px;
letter-spacing: 1px;
box-shadow: 0 4px 10px rgba(0,0,0,0.2);
position: fixed;
top: 0;
left: 0;
z-index: 1000;
padding-left: 20px;
display: flex;
align-items: center;
gap: 15px;">
    <img src="./img/logo1.png" alt="" style="width: 80px; height: 40px; border-radius: 50%; object-fit: cover;">
    <h4>Easy Ride</h4>
</header>

<div class="flex">
  <div class="sidebar"style=" width: 220px;
  margin-top: 10px;
  background-color: #3f4e94;
  color: white;
  min-height: 100vh;
  padding: 20px 10px;
  position: fixed;
  top: 70px;
  display: flex;
  flex-direction: column;
  align-items: center;">
        <div class="users-profile">
            <img src="https://t4.ftcdn.net/jpg/16/09/59/37/360_F_1609593795_Ae1PPBgGSiy2tKw4GWXeXJtBTQn3dWpn.jpg" alt="Profile">
        </div>
        <h4>Welcome, <span><?php echo $username; ?></span></h4>
        <a href="dashboard.php" class="menu"><span class="material-icons-sharp">dashboard</span> Dashboard</a>
        <a href="rent.php" class="menu"><span class="material-icons-sharp">directions_car</span> Rent Now</a>
        <a href="booking.php" class="menu"><span class="material-icons-sharp">calendar_today</span> My Booking</a>
        <a href="logout.php" class="menu"><span class="material-icons-sharp">logout</span> Logout</a>
    </div>

    <div class="main">
        <div class="header-bar">
            <h2>Available Vehicles</h2>
            <div class="search-container">
                <input type="text" placeholder="Search vehicles..." id="searchInput">
                <i class="material-icons-sharp">search</i>
            </div>
        </div>

        <div class="vehicle-list" id="vehicleList">
            <?php
            if ($vehicles && $vehicles->num_rows > 0) {
                while ($v = $vehicles->fetch_assoc()) {
                    $id    = $v['vehicle_id'];
                    $name  = $v['vehicle_name'];
                    $type  = $v['category_type'];
                    $model = $v['model'];
                    $price = $v['price_per_day'];
                    $img   = $v['image'] ?: 'placeholder.png';
                    
                    echo "<div class='vehicle-card'>
                            <img src='$img' alt='$name'>
                            <h3>$name</h3>
                            <p>Type: $type</p>
                            <p>Model: $model</p>
                            <p>Price/day: Rs. <span id='price-$id'>$price</span></p>";

                    // Show Book Now or Unavailable
                    if($v['available'] == 'unavailable'){
                        echo "<span style='color:red; font-weight:bold;'>Unavailable</span>";
                    } else {
                        echo "<button onclick='openForm($id, $price)'>Book Now</button>";
                    }

                    echo "</div>";
                }
            } else {
                echo "<p>No vehicles available at the moment.</p>";
            }
            ?>
        </div>
    </div>
</div>

<div id="bookingForm" style="display:none;">
    <div class="form-container">
        <h3>Book Vehicle</h3>
        <form action="booking.php" method="post">
            <input type="hidden" name="vehicle_id" id="vehicle_id">
            <label>Start Date:</label>
            <input type="date" name="start_date" id="start_date" required onchange="calculateTotal()">
            <label>End Date:</label>
            <input type="date" name="end_date" id="end_date" required onchange="calculateTotal()">
            <p>Days: <span id="num_days_display">0</span></p>
            <p>Total Price: Rs. <span id="total_price">0</span></p>
            <input type="hidden" name="num_days" id="num_days_input">
            <input type="hidden" name="total_price" id="total_price_input">
            <button type="submit">Confirm Booking</button>
            <button type="button" class="close-btn" onclick="closeForm()">Cancel</button>
        </form>
    </div>
</div>

<script src="rent.js"></script>

</body>
</html>
