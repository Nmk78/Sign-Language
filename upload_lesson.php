<?php
$host = "localhost";
$user = "root";  // Change if needed
$pass = "root";  // Change if needed
$dbname = "sign_language"; // Replace with your actual database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if required POST data is set
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['course_id'], $_POST['title'], $_POST['content'])) {
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Video upload logic
    $video_data = NULL;
    if (isset($_FILES['video_data']) && $_FILES['video_data']['error'] == 0) {
        $video_data = file_get_contents($_FILES['video_data']['tmp_name']);
    } elseif ($_FILES['video_data']['error'] != UPLOAD_ERR_NO_FILE) {
        echo "Error uploading video: " . $_FILES['video_data']['error'];
        exit();
    }

    // Insert lesson into the database with video
    $stmt = $conn->prepare("INSERT INTO lesson (course_id, title, description, video_data) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $course_id, $title, $content, $video_data);
    $stmt->execute();
    $stmt->close();

    // Redirect to the dashboard
    header("Location: /dashboard?tab=statistics");
    exit();
} else {
    echo "Required data not provided.";
}

$conn->close();
?>
