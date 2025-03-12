<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user']['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit;
}

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
$course_id = intval($_POST["course_id"]);
$title = $conn->real_escape_string($_POST["title"]);
$description = $conn->real_escape_string($_POST["description"]);
$category = $conn->real_escape_string($_POST["category"]);
$price = floatval($_POST["price"]);
$created_by = $_SESSION['user']['user_id'];

// Handle file upload (optional)
$thumbnail_url = "";
if (!empty($_FILES["thumbnail"]["name"])) {
    $target_dir = "../images/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = time() . "_" . basename($_FILES["thumbnail"]["name"]);
    $target_file = $target_dir . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowed_extensions)) {
        echo json_encode(["success" => false, "message" => "Only JPG, JPEG, PNG & GIF files are allowed."]);
        exit;
    }

    if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $target_file)) {
        $thumbnail_url = $target_file;
    } else {
        echo json_encode(["success" => false, "message" => "Failed to upload image."]);
        exit;
    }
}

// Build update query
$query = "UPDATE courses SET 
    title = '$title', 
    description = '$description', 
    category = '$category', 
    price = '$price'";
    
if (!empty($thumbnail_url)) {
    $query .= ", thumbnail_url = '$thumbnail_url'";
}

$query .= " WHERE id = $course_id AND created_by = $created_by";

if ($conn->query($query)) {
    // Update session data (simple version)
    foreach ($_SESSION['courses'] as &$course) {
        if ($course['id'] == $course_id) {
            $course['title'] = $title;
            $course['description'] = $description;
            $course['category'] = $category;
            $course['price'] = $price;
            if (!empty($thumbnail_url)) {
                $course['thumbnail_url'] = $thumbnail_url;
            }
            break;
        }
    }
    unset($course);

    echo json_encode(["success" => true, "message" => "Course updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update course"]);
}

$conn->close();
?>