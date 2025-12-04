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
<link rel="stylesheet" href="dashboard.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
<style>
/* Cards */
.cards { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 30px;}
.card { flex: 1; min-width: 200px; background: #fff; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);}
.card h2 { font-size: 2em; margin: 0; color: #333;}
.card p { color: #777; margin: 5px 0 0;}
</style>
</head>
<body>
<header> 
    <img src="logo1.jpg" alt="">
    <h4>EasyRide</h4>
</header>
<div class="flex">
    <div class="sidebar">
        <img src="https://t4.ftcdn.net/jpg/16/09/59/37/360_F_1609593795_Ae1PPBgGSiy2tKw4GWXeXJtBTQn3dWpn.jpg" alt="Profile">
        <h4>Welcome, <?php echo $_SESSION['username']; ?></h4>
        <a href="dashboard.php" class="menu">
            <span class="material-icons-sharp">dashboard</span> Dashboard
        </a>
        <a href="rent.php" class="menu">
            <span class="material-icons-sharp">directions_car</span> Rent Now
        </a>
        <a href="booking.php" class="menu">
            <span class="material-icons-sharp">calendar_today</span> My Booking
        </a>
        <a href="logout.php" class="menu">
            <span class="material-icons-sharp">logout</span> Logout
        </a>
    </div>
    
    <div class="main">
        <section id="dashboard">
            <div class="text">
                <h2>Dashboard</h2>
                <p>Rent your perfect vehicle easily: car, bike, or scooter. Check availability, compare prices, and book your ride in just a few clicks!</p>
            </div>

           <!-- Summary Cards -->
<div class="cards">
    <div class="card">
        <div class="card-top">
            <h2><?php echo $totalVehicles; ?></h2>
            <span class="material-icons-sharp">directions_car</span>
        </div>
        <p>Total Vehicles</p>
    </div>

    <div class="card">
        <div class="card-top">
            <h2><?php echo $totalBookings; ?></h2>
            <span class="material-icons-sharp">event</span> <!-- Changed icon -->
        </div>
        <p>My Bookings</p>
    </div>

    <div class="card">
        <div class="card-top">
            <h2><?php echo $bookedVehicles; ?></h2>
            <span class="material-icons-sharp">check_circle</span>
        </div>
        <p>Booked Vehicles</p>
    </div>
</div>


            <h2>Trending Vehicles</h2>
            <div class="trending">
                <div class="vehicle car">
                    <div class="top">
                        <img src="https://www.indiacarnews.com/wp-content/uploads/2020/12/New-Car-Launches-In-January-2021.jpg" alt="Car">
                        <div class="info">
                            <h3>🔥 Toyota Camry XSE</h3>
                        </div>
                    </div>
                    <button onclick="toggleDetails('toyotaDetails')">View Detail</button>
                    <div id="toyotaDetails" class="details">
                        <ul>
                            <li>Engine: 3.5L V6, 301 hp</li>
                            <li>Transmission: 8-speed automatic</li>
                            <li>Fuel Type: Petrol</li>
                            <li>Top Speed: Around 210 km/h</li>
                            <li>Acceleration: 0-100 km/h in ~5.8 seconds</li>
                            <li>Features: Sporty design, premium leather seats, advanced infotainment system</li>
                            <li>Safety: Toyota Safety Sense, multiple airbags, ABS with EBD, lane departure alert</li>
                            <li>Interior: Spacious cabin, touchscreen display, Apple CarPlay & Android Auto support</li>
                            <li>Suspension: Independent front & rear for smooth ride</li>
                            <li>Use: Comfortable for city and highway drives, ideal for family and executive use</li>
                        </ul>
                    </div>
                </div>

                <div class="vehicle bike">
                    <div class="top">
                        <img src="https://motobike.in/wp-content/uploads/2021/04/TVS-Apache-RR-310-Bomber-Grey.jpg" alt="Bike">
                        <div class="info">
                            <h3>🔥 TVS Apache RR 310</h3>
                        </div>
                    </div>
                    <button onclick="toggleDetails('bikeDetails')">View Detail</button>
                    <div id="bikeDetails" class="details">
                        <ul>
                            <li>Engine: 312.2cc, single-cylinder, liquid-cooled</li>
                            <li>Power: Around 34 HP</li>
                            <li>Top Speed: 160 km/h</li>
                            <li>Launch Year: 2017</li>
                            <li>Transmission: 6-speed manual</li>
                            <li>Features: Lightweight and agile, aerodynamic design, fully digital console</li>
                            <li>Brakes: Front and rear disc brakes with ABS</li>
                            <li>Suspension: Telescopic front forks and mono-shock rear</li>
                        </ul>
                    </div>
                </div>

                <div class="vehicle scooter">
                    <div class="top">
                        <img src="https://images.financialexpressdigital.com/2020/09/Vespa-Racing-Sixties-660.jpg?w=660" alt="Scooter">
                        <div class="info">
                            <h3>🔥 Vespa Racing Sixties</h3>
                        </div>
                    </div>
                    <button onclick="toggleDetails('scooterDetails')">View Detail</button>
                    <div id="scooterDetails" class="details">
                        <ul>
                            <li>Engine: 125cc, single-cylinder, 4-stroke</li>
                            <li>Transmission: Automatic CVT</li>
                            <li>Power: Around 10.7 HP</li>
                            <li>Top Speed: 90 km/h</li>
                            <li>Weight: 150 kg (lightweight and easy to handle)</li>
                            <li>Design: Retro styling with Racing Sixties edition graphics</li>
                            <li>Features: Comfortable seating, good fuel efficiency, ideal for city rides</li>
                            <li>Brakes: Front disc and rear drum with CBS</li>
                        </ul>
                    </div>
                </div>

                <div class="vehicle jeep">
                    <div class="top">
                        <img src="https://jeep.com.np/wp-content/uploads/2021/04/Wrangler-Rubicon-White.jpg.img_.1440-1024x426.jpg" alt="Jeep">
                        <div class="info">
                            <h3>🔥 Jeep Wrangler Rubicon</h3>
                        </div>
                    </div>
                    <button onclick="toggleDetails('jeepDetails')">View Detail</button>
                    <div id="jeepDetails" class="details">
                        <ul>
                            <li>Type: 4x4 SUV</li>
                            <li>Engine: 3.6L V6, 285 bhp</li>
                            <li>Transmission: 6-speed manual or 8-speed automatic</li>
                            <li>Features: Removable doors and roof, rugged off-road design</li>
                            <li>Suspension: Heavy-duty off-road suspension with solid axles</li>
                            <li>Interior: Premium seating, touchscreen infotainment, off-road navigation</li>
                            <li>Safety: ABS, airbags, electronic stability control</li>
                            <li>Use: Ideal for adventure trips, off-road trails, and daily driving</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
function toggleDetails(id) {
    const div = document.getElementById(id);
    div.style.display = div.style.display === "block" ? "none" : "block";
}
</script>
</body>
</html>
