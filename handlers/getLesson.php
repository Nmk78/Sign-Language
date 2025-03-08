<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "sign_language";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Get lesson ID from URL
$lessonId = isset($_GET['lesson']) ? intval($_GET['lesson']) : 0;

if ($lessonId > 0) {
    // Prepare SQL statement
    $stmt = $conn->prepare("SELECT id, title, description, category, created_by, created_at, course_id, rating, video_data FROM lesson WHERE id = ?");
    
    if (!$stmt) {
        die(json_encode(["error" => "Failed to prepare statement: " . $conn->error]));
    }

    $stmt->bind_param("i", $lessonId);
    $stmt->execute();
    $stmt->store_result();

    // Check if the lesson exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $title, $description, $category, $created_by, $created_at, $course_id, $rating, $video_data);
        $stmt->fetch();
        $stmt->close();
        $conn->close();

        $lessonData = [
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'created_by' => $created_by,
            'created_at' => $created_at,
            'course_id' => $course_id,
            'rating' => $rating,
            'video_data' => 'data:video/mp4;base64,' . base64_encode($video_data)
        ];

        header("Content-Type: application/json");
        echo json_encode($lessonData);
        exit();
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Lesson not found."]);
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid lesson ID."]);
}

$conn->close();
?>
