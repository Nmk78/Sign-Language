<?php
header('Content-Type: application/json');
session_start();

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "sign_language";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Ensure the request method is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

$lesson_id = $_POST["lesson_id"] ?? null;
$user_id = $_POST["user_id"] ?? null;
$comment = trim($_POST["comment"] ?? "");

// Validate required fields
if (!$lesson_id || !$user_id || empty($comment)) {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

// Prepare and insert the comment into the database
$stmt = $conn->prepare("INSERT INTO lesson_comments (lesson_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iis", $lesson_id, $user_id, $comment);

if ($stmt->execute()) {
    $username = $_SESSION["username"] ?? "User" . $user_id;
    $profile_image = $_SESSION["profile_image"] ?? "";
    $initials = strtoupper(substr($username, 0, 1));

    echo json_encode([
        "success" => true,
        "comment" => htmlspecialchars($comment),
        "username" => htmlspecialchars($username),
        "profile_image" => $profile_image ? htmlspecialchars($profile_image) : null,
        "initials" => $initials
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
