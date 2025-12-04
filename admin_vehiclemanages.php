<?php
session_start();
if(!isset($_SESSION['admin'])) {
    header("Location: index.html");
    exit();
}

$conn = new mysqli("localhost","root","","vehiclerentalsystem");
if($conn->connect_error) die("Connection Failed");

$admin_email = $_SESSION['admin'];

if(isset($_POST['add_vehicle'])) {
    $name = $_POST['vehicle_name'];
    $category = $_POST['category_type'];
    $model = $_POST['model'];
    $price = $_POST['price_per_day'];
    $image = $_POST['image_url'];

    $conn->query("INSERT INTO vehicles (vehicle_name, category_type, model, price_per_day, image) 
                  VALUES ('$name','$category','$model','$price','$image')");
}

if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $check = $conn->query("SELECT * FROM bookings WHERE vehicle_id=$id");
    
    if($check->num_rows > 0) {
        echo "<script>alert('Cannot delete this vehicle. It has existing bookings.'); window.location='admin_vehiclemanages.php';</script>";
    } else {
        $conn->query("DELETE FROM vehicles WHERE vehicle_id=$id");
        echo "<script>alert('Vehicle deleted successfully.'); window.location='admin_vehiclemanages.php';</script>";
    }
}

if(isset($_POST['update_vehicle'])) {
    $id = $_POST['vehicle_id'];
    $name = $_POST['vehicle_name'];
    $category = $_POST['category_type'];
    $model = $_POST['model'];
    $price = $_POST['price_per_day'];
    $image = $_POST['image_url'];

    $conn->query("UPDATE vehicles SET vehicle_name='$name', category_type='$category', model='$model', price_per_day='$price', image='$image' WHERE vehicle_id=$id");
}

$vehicles = $conn->query("SELECT * FROM vehicles ORDER BY vehicle_id DESC");

$edit_vehicle = null;
if(isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM vehicles WHERE vehicle_id=$id");
    $edit_vehicle = $res->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Vehicles</title>
<link rel="stylesheet" href="admin_vehiclemanages.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
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
        <a href="admin_dashboard.php" class="menu"><span class="material-icons-sharp">dashboard</span> Dashboard</a>
        <a href="admin_manages.php" class="menu"><span class="material-icons-sharp">people</span>Bookings</a>
        <a href="admin_vehiclemanages.php" class="menu"><span class="material-icons-sharp">directions_car</span> Vehicles</a>
        <a href="logout.php" class="menu"><span class="material-icons-sharp">logout</span> Logout</a>
    </div>

    <div class="main">
        <h2>Add Vehicle</h2>
        <form method="POST">
            <input type="text" name="vehicle_name" placeholder="Vehicle Name" required>
            <input type="text" name="category_type" placeholder="Category Type" required>
            <input type="text" name="model" placeholder="Model" required>
            <input type="number" name="price_per_day" placeholder="Price Per Day" required>
            <input type="text" name="image_url" placeholder="Enter Image URL" required><br>
            <button type="submit" name="add_vehicle">Add Vehicle</button><br><br>
        </form>

        <?php if($edit_vehicle): ?>
        <h2>Update Vehicle</h2>
        <form method="POST">
            <input type="hidden" name="vehicle_id" value="<?= $edit_vehicle['vehicle_id'] ?>">
            <input type="text" name="vehicle_name" value="<?= $edit_vehicle['vehicle_name'] ?>" required>
            <input type="text" name="category_type" value="<?= $edit_vehicle['category_type'] ?>" required>
            <input type="text" name="model" value="<?= $edit_vehicle['model'] ?>" required>
            <input type="number" name="price_per_day" value="<?= $edit_vehicle['price_per_day'] ?>" required>
            <input type="text" name="image_url" value="<?= $edit_vehicle['image'] ?>" required><br>
            <button type="submit" name="update_vehicle">Update Vehicle</button><br><br>
        </form>
        <?php endif; ?>

        <h2>Vehicles List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Model</th>
                <th>Price</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
            <?php while($v = $vehicles->fetch_assoc()): ?>
            <tr>
                <td><?= $v['vehicle_id'] ?></td>
                <td><?= $v['vehicle_name'] ?></td>
                <td><?= $v['category_type'] ?></td>
                <td><?= $v['model'] ?></td>
                <td><?= $v['price_per_day'] ?></td>
                <td><img src="<?= htmlspecialchars($v['image']) ?>" width="70" alt="Vehicle Image"></td>
                <td>
                    <a href="?edit=<?= $v['vehicle_id'] ?>" class="button">Edit</a>
                    <a href="?delete=<?= $v['vehicle_id'] ?>" class="button button-red" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>
