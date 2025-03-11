<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user']['user_id']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'teacher') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Check if user_id is provided
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'User ID is required']);
    exit();
}

$user_id = intval($_GET['user_id']);

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "sign_language";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Get user information
$user_stmt = $conn->prepare("
    SELECT 
        id, 
        username, 
        email, 
        role, 
        created_at, 
        profile,
        (SELECT COUNT(*) FROM course_enrollments WHERE user_id = users.id) as enrolled_courses
    FROM users 
    WHERE id = ?
");

$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$result = $user_stmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'User not found']);
    exit();
}

$user_data = $result->fetch_assoc();
$user_stmt->close();
$conn->close();

// Return user data as JSON
header('Content-Type: application/json');
echo json_encode($user_data);
?>