<?php
session_start();
if (!isset($_SESSION['user']['user_id']) || $_SESSION['user']['role'] !== 'teacher') {
    exit();
}

$conn = new mysqli("localhost", "root", "root", "sign_language");
$teacher_id = $_SESSION['user']['user_id'];
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_courses,
        SUM(CASE WHEN ce.status = 'pending' THEN 1 ELSE 0 END) as total_pending,
        SUM(CASE WHEN ce.status = 'approved' THEN 1 ELSE 0 END) as total_approved,
        SUM(CASE WHEN ce.status = 'rejected' THEN 1 ELSE 0 END) as total_rejected
    FROM courses c
    LEFT JOIN course_enrollments ce ON c.id = ce.course_id
    WHERE c.created_by = ?
");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc();
$conn->close();

header('Content-Type: application/json');
echo json_encode($stats);
?>