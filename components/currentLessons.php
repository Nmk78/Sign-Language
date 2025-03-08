<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "sign_language";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the course ID from the URL
$course_id = isset($_GET['course']) ? intval($_GET['course']) : 0;

// Fetch lessons for this course
$sql = "SELECT id, title, description FROM lesson WHERE course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

$lessons = [];
while ($row = $result->fetch_assoc()) {
    $lessons[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Lessons</title>
    <script>
        function updateLesson(lessonId) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('lesson', lessonId);
            window.history.pushState({}, '', '?' + urlParams.toString());
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-3xl font-bold mb-2">Course Lessons</h1>

        <div class="space-y-4">
            <?php foreach ($lessons as $lesson): ?>
                <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100"
                     onclick="updateLesson(<?php echo $lesson['id']; ?>)">
                    <div class="w-32 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-medium"><?php echo htmlspecialchars($lesson['title']); ?></h3>
                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($lesson['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
