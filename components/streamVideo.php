<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "sign_language";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get lesson ID
$lessonId = isset($_GET['lesson']) ? intval($_GET['lesson']) : 0;

if ($lessonId > 0) {
    // Fetch video data
    $stmt = $conn->prepare("SELECT video_data FROM lesson WHERE id = ?");
    $stmt->bind_param("i", $lessonId);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($videoData);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    if (!$videoData) {
        http_response_code(404);
        die("Video not found.");
    }

    // Set headers for video streaming
    $fileSize = strlen($videoData);
    header("Content-Type: video/mp4");  // Adjust if needed
    header("Accept-Ranges: bytes");

    // Handle byte-range requests (for seeking)
    if (isset($_SERVER['HTTP_RANGE'])) {
        $range = $_SERVER['HTTP_RANGE'];
        list(, $range) = explode("=", $range, 2);
        list($start, $end) = explode("-", $range);

        $start = intval($start);
        $end = ($end === "") ? $fileSize - 1 : intval($end);
        $length = $end - $start + 1;

        header("HTTP/1.1 206 Partial Content");
        header("Content-Length: $length");
        header("Content-Range: bytes $start-$end/$fileSize");

        echo substr($videoData, $start, $length);
    } else {
        // Send the entire file
        header("Content-Length: $fileSize");
        echo $videoData;
    }
    exit();
} else {
    http_response_code(400);
    die("Invalid lesson ID.");
}
?>
