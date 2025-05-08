<?php
session_start();
$conn = new mysqli("localhost", "root", "", "connext");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $hashed_password, $role);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['role'] = $role;

        // redirect user to profile page based on role
        header("Location: startup.php"); // or use "investor.html" if you split them later
        exit();
    } else {
        echo "Wrong password.";
    }
} else {
    echo "User not found.";
}

$stmt->close();
$conn->close();
?>
