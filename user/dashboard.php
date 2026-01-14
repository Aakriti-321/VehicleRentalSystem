<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost","root","","vehiclerentalsystem");
if($conn->connect_error) die("Database connection failed");

$username = $_SESSION['username'];

// Total vehicles
$totalVehicles = $conn->query("SELECT COUNT(*) AS total FROM vehicles")->fetch_assoc()['total'];

// Total bookings by this user
$totalBookings = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE user_name='$username'")->fetch_assoc()['total'];

// Total booked vehicles by this user (paid bookings)
$bookedVehicles = $conn->query("SELECT COUNT(DISTINCT vehicle_id) AS total FROM bookings WHERE user_name='$username' AND payment_status='completed'")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vehicle Rental Dashboard</title>
<link rel="stylesheet" href="user_dashboard.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
</head>
<body>

<header>
    <img src="./img/logo1.png" alt="Logo">
    <h4>EasyRide</h4>
</header>

<div class="sidebar">
    <img src="https://t4.ftcdn.net/jpg/16/09/59/37/360_F_1609593795_Ae1PPBgGSiy2tKw4GWXeXJtBTQn3dWpn.jpg" alt="Profile">
    <h4>Welcome, <?php echo $_SESSION['username']; ?></h4>
    <a href="dashboard.php" class="menu"><span class="material-icons-sharp">dashboard</span> Dashboard</a>
    <a href="rent.php" class="menu"><span class="material-icons-sharp">directions_car</span> Rent Now</a>
    <a href="booking.php" class="menu"><span class="material-icons-sharp">calendar_today</span> My Booking</a>
    <a href="logout.php" class="menu"><span class="material-icons-sharp">logout</span> Logout</a>
</div>

<div class="main">

    <div class="text">
        <h2>Dashboard</h2>
        <p>Rent your perfect vehicle easily: car, bike, or scooter. Check availability, compare prices, and book your ride in just a few clicks!</p>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="cards">
        <div class="card card-red">
            <h2><?php echo $totalVehicles; ?></h2>
            <span class="material-icons-sharp">directions_car</span>
            <p>Total Vehicles</p>
        </div>
        <div class="card card-blue">
            <h2><?php echo $totalBookings; ?></h2>
            <span class="material-icons-sharp">event</span>
            <p>My Bookings</p>
        </div>
        <div class="card card-green">
            <h2><?php echo $bookedVehicles; ?></h2>
            <span class="material-icons-sharp">check_circle</span>
            <p>Booked Vehicles</p>
        </div>
    </div>

    <!-- TRENDING VEHICLES -->
    <section class="section_categories">
        <h2>Trending Vehicles</h2>
        <div class="trending">
            <div class="vehicle">
                <img src="https://www.indiacarnews.com/wp-content/uploads/2020/12/New-Car-Launches-In-January-2021.jpg" alt="Car">
                <h3>Toyota Camry XSE</h3>
                <button onclick="toggleDetails('toyotaDetails')">View Detail</button>
                <div id="toyotaDetails" class="details">
                    <ul>
                        <li>Engine: 3.5L V6, 301 hp</li>
                        <li>Transmission: 8-speed automatic</li>
                        <li>Fuel Type: Petrol</li>
                        <li>Top Speed: Around 210 km/h</li>
                        <li>Acceleration: 0-100 km/h in ~5.8 seconds</li>
                    </ul>
                </div>
            </div>

            <div class="vehicle">
                <img src="https://motobike.in/wp-content/uploads/2021/04/TVS-Apache-RR-310-Bomber-Grey.jpg" alt="Bike">
                <h3>TVS Apache RR 310</h3>
                <button onclick="toggleDetails('bikeDetails')">View Detail</button>
                <div id="bikeDetails" class="details">
                    <ul>
                        <li>Engine: 312.2cc, single-cylinder, liquid-cooled</li>
                        <li>Power: Around 34 HP</li>
                        <li>Top Speed: 160 km/h</li>
                    </ul>
                </div>
            </div>

            <div class="vehicle">
                <img src="https://images.financialexpressdigital.com/2020/09/Vespa-Racing-Sixties-660.jpg?w=660" alt="Scooter">
                <h3>Vespa Racing Sixties</h3>
                <button onclick="toggleDetails('scooterDetails')">View Detail</button>
                <div id="scooterDetails" class="details">
                    <ul>
                        <li>Engine: 125cc, single-cylinder, 4-stroke</li>
                        <li>Transmission: Automatic CVT</li>
                        <li>Top Speed: 90 km/h</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- MOST BOOKED VEHICLES -->
    <section class="section_categories">
        <h2>Most Booked Vehicles</h2>
        <div class="cardss">
            <div class="card2">
                <img src="./img/Toyoto.jpg" alt="Toyota Land Cruiser 200">
                <h3>Toyota Land Cruiser 200</h3>
                <a href="./rent.php"><button>Rent Now</button></a>
            </div>
            <div class="card2">
                <img src="./img/FZS FI V2.avif" alt="FZS FI V2">
                <h3>FZS FI V2</h3>
                <a href="./rent.php"><button>Rent Now</button></a>
            </div>
        </div>
    </section>

</div>

<script>
function toggleDetails(id) {
    const div = document.getElementById(id);
    div.style.display = div.style.display === "block" ? "none" : "block";
}
</script>

</body>
</html>
