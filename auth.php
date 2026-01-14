<?php
session_start();
$conn = new mysqli("localhost", "root","", "projcet I");
if ($conn->connect_error) die("Connection Failed");

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];


    if($email === "admin@gmail.com" && $password === "admin123") {
        $_SESSION['admin'] = "Admin";
        header("Location: /admin/admin_dashboard.php");
        exit();
    }

   
    $res = $conn->query("SELECT * FROM users WHERE email='$email'");

    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            header("Location: /user/dashboard.php");
            exit;
        } else {
            echo "<script>alert('Invalid Password'); window.location='index.html';</script>";
        }
    } else {
        echo "<script>alert('User Not Found'); window.location='index.html';</script>";
    }
}

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];

    $check = $conn->query("SELECT * FROM users WHERE email='$email'");

    if ($check->num_rows > 0) {
        echo "<script>alert('Email already exists'); window.location='index.html';</script>";
    } else {
        $conn->query("INSERT INTO users (username, email, password, phone, address, dob, gender)
                      VALUES ('$username', '$email', '$password', '$phone', '$address', '$dob', '$gender')");
        echo "<script>alert('Registered Successfully'); window.location='index.html';</script>";
    }
}
?>
