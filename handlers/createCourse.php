<?php
header('Content-Type: application/json');
session_start();

$conn = new mysqli("localhost", "root", "root", "sign_language");

// Check connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

// Retrieve form data
$title = $conn->real_escape_string($_POST["title"]);
$description = $conn->real_escape_string($_POST["description"]);
$category = $conn->real_escape_string($_POST["category"]);
$price = floatval($_POST["price"]);
$status = $conn->real_escape_string($_POST["status"]);
$created_by = $_SESSION['user']['user_id'] ?? 1; // Replace 1 with actual user ID

$thumbnail_url = ""; // Default empty

// Handle file upload
if (!empty($_FILES["thumbnail"]["name"])) {
    $target_dir = "../images/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Create folder if not exists
    }

    $file_name = time() . "_" . basename($_FILES["thumbnail"]["name"]);
    $target_file = $target_dir . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Allow certain file formats
    $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowed_extensions)) {
        echo json_encode(["success" => false, "message" => "Only JPG, JPEG, PNG & GIF files are allowed."]);
        exit;
    }

    // Move uploaded file
    if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $target_file)) {
        $thumbnail_url = $target_file;
    } else {
        echo json_encode(["success" => false, "message" => "Failed to upload image."]);
        exit;
    }
}

// Insert into database
$query = "INSERT INTO courses (title, description, category, price, thumbnail_url, status, created_by, created_at) 
          VALUES ('$title', '$description', '$category', '$price', '$thumbnail_url', '$status', '$created_by', NOW())";

if ($conn->query($query)) {
    echo json_encode(["success" => true, "message" => "Course created successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to create course"]);
}

$conn->close();
?>
