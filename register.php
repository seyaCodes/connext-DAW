<?php
// connect to database
$conn = new mysqli("localhost", "root", "", "connext");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// get POST data
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = $_POST['role'];

// insert user
$stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $email, $password, $role);

if ($stmt->execute()) {
    echo "Registration successful! <a href='sign.html'>Login here</a>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
