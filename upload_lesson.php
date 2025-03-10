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
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['course_id'], $_POST['title'], $_POST['content'], $_POST['user_id'])) {
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_POST['user_id'];

    // Insert lesson into the database without video URL
    $stmt = $conn->prepare("INSERT INTO lesson (course_id, title, description, created_by) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $course_id, $title, $content, $user_id);
    $stmt->execute();
    $lesson_id = $stmt->insert_id; // Get the inserted lesson ID
    $stmt->close();

    // Video upload logic
    $video_url = NULL;
    $upload_dir = "videos/"; // Change as needed
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Ensure the directory exists
    }

    if (isset($_FILES['video_data']) && $_FILES['video_data']['error'] == 0) {
        $video_extension = pathinfo($_FILES['video_data']['name'], PATHINFO_EXTENSION);
        $video_name = "course_{$course_id}_lesson_{$lesson_id}." . $video_extension;
        $video_path = $upload_dir . $video_name;

        if (move_uploaded_file($_FILES['video_data']['tmp_name'], $video_path)) {
            $video_url = $video_path; // Store the file path

            // Update the lesson record with the video URL
            $stmt = $conn->prepare("UPDATE lesson SET video_url = ? WHERE id = ?");
            $stmt->bind_param("si", $video_url, $lesson_id);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Error moving uploaded video.";
            exit();
        }
    } elseif ($_FILES['video_data']['error'] != UPLOAD_ERR_NO_FILE) {
        echo "Error uploading video: " . $_FILES['video_data']['error'];
        exit();
    }

    // Redirect to the dashboard
    header("Location: /dashboard?tab=courses");
    exit();
} else {
    echo "Required data not provided.";
}

$conn->close();
?>
