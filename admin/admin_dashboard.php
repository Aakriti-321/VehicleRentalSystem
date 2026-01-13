<?php
session_start();
if(!isset($_SESSION['admin'])) {
    header("Location: index.html");
    exit();
}

$conn = new mysqli("localhost","root","","vehiclerentalsystem");
if ($conn->connect_error) die("Database connection failed");

$admin_email = $_SESSION['admin'];

$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_vehicles = $conn->query("SELECT COUNT(*) as count FROM vehicles")->fetch_assoc()['count'];
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];

$query = "SELECT id, username, email, created_at FROM users ORDER BY created_at DESC LIMIT 5";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="admin_dasboard.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
</head>
<body>

<header>
    <img src="./img/logo.png" alt="Logo">
    <h4>EasyRide </h4>
</header>

<div class="flex">
    <div class="sidebar">
        <img src="https://t4.ftcdn.net/jpg/16/09/59/37/360_F_1609593795_Ae1PPBgGSiy2tKw4GWXeXJtBTQn3dWpn.jpg" alt="Profile">
        <h4>Welcome, <?= $admin_email ?></h4>
        <a href="admin_dashboard.php" class="menu"><span class="material-icons-sharp">dashboard</span> Dashboard</a>
        <a href="admin_manages.php" class="menu"><span class="material-icons-sharp">people</span> Bookings</a>
        <a href="admin_vehiclemanages.php" class="menu"><span class="material-icons-sharp">directions_car</span> Vehicles</a>
        <a href="logout.php" class="menu"><span class="material-icons-sharp">logout</span> Logout</a>
    </div>

    <div class="main">
        
        <!-- Cards Section -->
        <div class="cards">
            <div class="card">
                <h3>Total Users</h3><br><br>
                <p><?= $total_users ?></p>
            </div>
            <div class="card">
                <h3>Total Vehicles</h3>
                <p><?= $total_vehicles ?></p>
            </div>
            <div class="card">
                <h3>Total Bookings</h3>
                <p><?= $total_bookings ?></p>
            </div>
        </div> <!-- close cards -->

        <!-- Recent Users Section -->
        <div class="text">
            <h2>Recent Users</h2>
            <table width="100%" cellpadding="10">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Registered Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['username']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['created_at']}</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No users found</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
</body>
</html>
