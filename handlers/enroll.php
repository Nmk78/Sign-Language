<?php
session_start();

// Initialize messages
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
    $user_id = $_POST['user_id'] ?? 0;
    $course_id = $_POST['course_id'] ?? 0;

    // Check if the user is logged in (ensure user_id is not empty)
    if (empty($user_id)) {
        $error_message = "You must be logged in to enroll.";
    } else {
        // Check if the enrollment already exists
        $checkQuery = "SELECT * FROM course_enrollments WHERE user_id = ? AND course_id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("ii", $user_id, $course_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $error_message = "You are already enrolled in this course.";
        } else {
            // Insert the enrollment into the database
            $insertQuery = "INSERT INTO course_enrollments (user_id, course_id, status) VALUES (?, ?, 'pending')";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ii", $user_id, $course_id);

            if ($insertStmt->execute()) {
                $success_message = "You have successfully enrolled in the course!";
            } else {
                $error_message = "Error: " . $insertStmt->error;
            }

            // Close the prepared statement
            $insertStmt->close();
        }

        // Close the check statement
        $checkStmt->close();
    }
}

// Close the database connection
$conn->close();

// Redirect back to the previous page with messages (if necessary)
if ($success_message) {
    header("Location: /courseDetails?course=" . $course_id . "&success=" . urlencode($success_message));
    exit();
}

if ($error_message) {
    header("Location: /courseDetails?course=" . $course_id . "&error=" . urlencode($error_message));
    exit();
}
?>
