<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'D:\Programming\Sign-Language\utils\db.php'; // Ensure this file connects to your MySQL database

/// Get JSON input
$inputJSON = file_get_contents("php://input");
$data = json_decode($inputJSON, true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid JSON"]);
    exit();
}

$username = $data['username'] ?? '';
$avatar = $data['avatar'] ?? '';

if (empty($username) || empty($avatar)) {
    echo json_encode(["success" => false, "message" => "Missing username or avatar"]);
    exit();
}

// Update the avatar path in the database
$sql = "UPDATE users SET profile = ? WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $avatar, $username);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update avatar"]);
}

$stmt->close();
$conn->close();
?>
