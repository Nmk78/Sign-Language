<?php
session_start();
if (!isset($_SESSION['user']['user_id']) || $_SESSION['user']['role'] !== 'teacher') {
    exit();
}

$conn = new mysqli("localhost", "root", "root", "sign_language");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$teacher_id = $_SESSION['user']['user_id'];
$stmt = $conn->prepare("
    SELECT 
        ce.id as enrollment_id,
        ce.user_id,
        ce.course_id,
        ce.enrolled_at,
        ce.status,
        u.username,
        u.email,
        u.profile,
        c.title as course_title
    FROM course_enrollments ce
    JOIN users u ON ce.user_id = u.id
    JOIN courses c ON ce.course_id = c.id
    WHERE c.created_by = ? AND ce.status = 'pending'
    ORDER BY ce.enrolled_at DESC
");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$pending_enrollments = $result->fetch_all(MYSQLI_ASSOC);

echo "success";
// Return the HTML for the pending enrollments section
// Include your existing table HTML here with the updated data
$conn->close();
?>