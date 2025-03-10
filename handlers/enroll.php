<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$success_message = '';
$error_message = '';

// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'root';
$db_name = 'sign_language';

$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $submitted_token = $_POST['form_token'] ?? '';

    // Verify token
    if ($submitted_token === $_SESSION['form_token']) {
        $insertQuery = "INSERT INTO course_enrollments (user_id, course_id, status) VALUES (?, ?, 'pending')";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ii", $user_id, $course_id);
        $insertStmt->close();

        // Invalidate token after successful submission
        unset($_SESSION['form_token']);
    }
}