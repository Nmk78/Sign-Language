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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
    $stmt->close();
}

if (isset($_GET['delete_lesson'])) {
    $lesson_id = $_GET['delete_lesson'];
    $conn->query("DELETE FROM lesson WHERE id = $lesson_id");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Course Manager</h1>

        <!-- Course Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($course = $courses->fetch_assoc()): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <img class="w-full h-48 object-cover object-center" src="<?php echo $course['thumbnail_url']; ?>" alt="<?php echo $course['title']; ?>">
                    <div class="p-4">
                        <h2 class="text-xl font-semibold text-gray-800 mb-2"><?php echo $course['title']; ?></h2>
                        <p class="text-gray-600 text-sm mb-4"><?php echo substr($course['description'], 0, 100); ?>...</p>
                        <button class="manage-course-btn w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition duration-300" data-course-id="<?php echo $course['id']; ?>">
                            Manage Course
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Course Manager Modal -->
        <div id="courseManagerModal" class="fixed z-40 inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-gray-900" id="modalCourseTitle"></h3>
                        <div class="items-center px-4 py-3">
                            <button id="closeModal" class="px-4 py-2 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            <i class="fas fa-close text-indigo-600"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-2 px-7 py-3">
                        <!-- Lesson List -->
                        <div id="lessonList" class="text-left mb-4"></div>

                        <!-- Add Lesson Form -->
                        <form id="addLessonForm" action="upload_lesson.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" id="modalCourseId" name="course_id">
                            <div class="mb-4">
                                <input type="text" id="lessonTitle" name="title" placeholder="Lesson Title" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="mb-4">
                                <textarea id="lessonContent" name="content" placeholder="Lesson Content" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3"></textarea>
                            </div>
                            <div class="mb-4">
                                <input type="file" id="lessonVideo" name="video_data" accept="video/*" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition duration-300">
                                Add Lesson
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('courseManagerModal');
        const closeModal = document.getElementById('closeModal');
        const modalCourseTitle = document.getElementById('modalCourseTitle');
        const modalCourseId = document.getElementById('modalCourseId');
        const lessonList = document.getElementById('lessonList');
        const addLessonForm = document.getElementById('addLessonForm');

        document.querySelectorAll('.manage-course-btn').forEach(button => {
            button.addEventListener('click', function() {
                const courseId = this.getAttribute('data-course-id');
                const courseTitle = this.parentElement.querySelector('h2').textContent;
                openCourseManager(courseId, courseTitle);
            });
        });

        closeModal.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        function openCourseManager(courseId, courseTitle) {
            modalCourseTitle.textContent = courseTitle;
            modalCourseId.value = courseId;
            fetchLessons(courseId);
            modal.classList.remove('hidden');
        }

        function fetchLessons(courseId) {
            // Fetch lessons from the server
            fetch(`fetch_lessons.php?course_id=${courseId}`)
                .then(response => response.json())
                .then(lessons => {
                    lessonList.innerHTML = lessons.map(lesson => `
                        <div class="flex justify-between items-center bg-gray-100 p-2 mb-2 rounded">
                            <div class="aspect-video rounded-lg border-black"><i class="fas fa-play-circle text-indigo-600"></i></div>
                            <span>${lesson.title}</span>
                            <div>
                                <button class="text-blue-500 hover:text-blue-700 mr-2" onclick="editLesson(${lesson.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-500 hover:text-red-700" onclick="deleteLesson(${lesson.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `).join('');
                });
        }

        addLessonForm.addEventListener('submit', function(e) {
            // Allow form submission
        });

        function editLesson(lessonId) {
            // Implement edit functionality
            console.log('Editing lesson:', lessonId);
        }

        function deleteLesson(lessonId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send a delete request to the server
                    fetch(`delete_lesson.php?lesson_id=${lessonId}`)
                        .then(() => {
                            // Refresh the lesson list
                            fetchLessons(modalCourseId.value);
                        });
                }
            });
        }
    </script>
</body>

</html>