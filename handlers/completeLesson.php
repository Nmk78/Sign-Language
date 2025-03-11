<?php
session_start();
include '../utils/db.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lesson_id = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : 0;
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

    $user_id = isset($_SESSION['user']['user_id']) ? intval($_SESSION['user']['user_id']) : 0;

    if ($lesson_id > 0 && $user_id > 0) {
        // Check if lesson is already completed
        $checkQuery = "SELECT id FROM completed_lessons WHERE user_id = ? AND lesson_id = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ii", $user_id, $lesson_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            // Insert into completed_lessons
            $insertQuery = "INSERT INTO completed_lessons (user_id, lesson_id) VALUES (?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ii", $user_id, $lesson_id);
            if ($stmt->execute()) {
                header("Location: /courseDetails?course=$course_id&&lesson=$lesson_id");
            } else {
                header("Location: /courseDetails?course=$course_id&&lesson=$lesson_id");
            }
        } else {
            header("Location: /courseDetails?course=$course_id&&lesson=$lesson_id");
            // echo "already_completed"; // Lesson already completed
        }

        $stmt->close();
    } else {
        echo "invalid"; // Invalid input
    }

    $conn->close();
}
?>