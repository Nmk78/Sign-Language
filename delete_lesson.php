<?php
$host = "localhost";
$user = "root";  // Change if needed
$pass = "root";  // Change if needed
$dbname = "sign_language"; // Replace with your actual database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$lesson_id = $_GET['lesson_id'];
$conn->query("DELETE FROM lesson WHERE id = $lesson_id");

$conn->close();
?>
