<?php
$host = "localhost";
$user = "root";  // Change if needed
$pass = "root";  // Change if needed
$dbname = "sign_language"; // Replace with your actual database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$course_id = $_GET['course_id'];
$result = $conn->query("SELECT id, title FROM lesson WHERE course_id = $course_id");

$lessons = [];
while ($row = $result->fetch_assoc()) {
    $lessons[] = $row;
}

echo json_encode($lessons);

$conn->close();
?>
