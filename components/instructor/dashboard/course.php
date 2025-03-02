<?php
$host = "localhost";
$user = "root";  // Change if needed
$pass = "root";  // Change if needed
$dbname = "sign_language"; // Replace with your actual database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch all courses
$courses = $conn->query("SELECT * FROM courses");

if (isset($_POST['add_lesson'])) {
  $course_id = $_POST['course_id'];
  $title = $_POST['title'];
  $content = $_POST['content'];

  // Video upload logic
  $video_data = NULL;
  if (isset($_FILES['video_data']) && $_FILES['video_data']['error'] == 0) {
    $video_data = file_get_contents($_FILES['video_data']['tmp_name']);
  }

  // Insert lesson into the database with video
  $stmt = $conn->prepare("INSERT INTO lesson (course_id, title, description, video_data) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("isss", $course_id, $title, $content, $video_data);
  $stmt->execute();
  header("Location: components/instructor/dashboard/course.php");
}

if (isset($_GET['delete_lesson'])) {
  $lesson_id = $_GET['delete_lesson'];
  $conn->query("DELETE FROM lesson WHERE id = $lesson_id");
  header("Location: components/instructor/dashboard/course.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Course Manager</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 20px;
    }

    .container {
      max-width: 800px;
      margin: auto;
      background: white;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
    }

    h2 {
      text-align: center;
      color: #333;
    }

    h3 {
      background: #007BFF;
      color: white;
      padding: 10px;
      border-radius: 5px;
    }

    ul {
      list-style: none;
      padding: 0;
    }

    li {
      background: #e9ecef;
      margin: 5px 0;
      padding: 10px;
      border-radius: 5px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    a {
      color: red;
      text-decoration: none;
      font-weight: bold;
    }

    form {
      margin-top: 10px;
    }

    input,
    textarea,
    input[type="file"] {
      width: calc(100% - 20px);
      padding: 10px;
      margin: 5px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      background: #28a745;
      color: white;
      border: none;
      padding: 10px;
      width: 100%;
      cursor: pointer;
      border-radius: 5px;
      margin-top: 10px;
    }

    button:hover {
      background: #218838;
    }
  </style>
</head>

<body>
<div class="container overflow-scroll">
  <h2>Courses and Lessons</h2>

  <?php while ($course = $courses->fetch_assoc()): ?>
    <div class="p-2 @container assignment-container" id="assignment-<?php echo $course['id']; ?>">
      <div class="flex flex-col items-stretch rounded-xl justify-start gap-2 bg-surface hover:shadow-sm focus:shadow-md transition-all duration-300 shadow- p-2">
        <div class="flex flex-col bg-surface items-stretch justify-start rounded-xl @xl:flex-row @xl:items-start">
          <div class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-xl"
            style='background-image: url("<?php echo $course['thumbnail_url']; ?>");'></div>
          <div class="flex w-full min-w-72 grow flex-col items-stretch justify-center gap-1 py-4 @xl:px-4">
            <p class="text-[#0e161b] text-lg font-bold leading-tight tracking-[-0.015em]">
              <?php echo $course['title']; ?></p>
            <div class="flex items-end gap-3 justify-between">
              <button class="view-details-btn flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-8 px-4 bg-[#1d8cd7] text-[#f8fafb] text-sm font-medium leading-normal"
                data-course-id="<?php echo $course['id']; ?>">
                <span class="truncate">View details</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Lesson details and form (hidden by default) -->
        <div id="lesson-details-<?php echo $course['id']; ?>" class="hidden mt-4 p-4 bg-[#e8eef3] rounded-xl">
          <h3 class="text-[#0e161b] text-base font-bold mb-2">Lessons:</h3>
          <ul>
            <?php
            $course_id = $course['id'];
            $lessons = $conn->query("SELECT * FROM lesson WHERE course_id = $course_id");
            while ($lesson = $lessons->fetch_assoc()): ?>
              <li>
                <?php echo htmlspecialchars($lesson['title']); ?>
                <a href="?delete_lesson=<?php echo $lesson['id']; ?>" onclick="return confirm('Are you sure?')">❌ Delete</a>
              </li>
            <?php endwhile; ?>
          </ul>

          <!-- Add Lesson Form -->
          <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
            <input type="text" name="title" placeholder="Lesson Title" required>
            <textarea name="content" placeholder="Lesson Content" required></textarea>
            <input type="file" name="video_data" accept="video/*" />
            <button type="submit" name="add_lesson">Add Lesson</button>
          </form>
        </div>

      </div>
    </div>
  <?php endwhile; ?>
</div>

<!-- JavaScript to toggle lesson details -->
<script>
  document.querySelectorAll('.view-details-btn').forEach(button => {
    button.addEventListener('click', function () {
      let courseId = this.getAttribute('data-course-id');
      let lessonDetails = document.getElementById('lesson-details-' + courseId);
      lessonDetails.classList.toggle('hidden');
    });
  });
</script>

</body>

</html>
