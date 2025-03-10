<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
    // Redirect to login page
    header("Location: login.php");
    exit;
}

// Database connection
function getDbConnection()
{
    $host = "localhost";
    $user = "root";  // Change if needed
    $pass = "root";  // Change if needed
    $dbname = "sign_language"; // Replace with your actual database name

    $conn = new mysqli($host, $user, $pass, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// Get courses for the logged-in user
function getUserCourses($userId)
{
    $conn = getDbConnection();

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM courses WHERE created_by = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $result = $stmt->get_result();
    $courses = [];

    while ($row = $result->fetch_assoc()) {
        // Get lesson count for each course
        $lessonStmt = $conn->prepare("SELECT COUNT(*) as count FROM lesson WHERE course_id = ?");
        $lessonStmt->bind_param("i", $row['id']);
        $lessonStmt->execute();
        $lessonResult = $lessonStmt->get_result();
        $lessonCount = $lessonResult->fetch_assoc()['count'];

        // Get enrollment count
        $enrollStmt = $conn->prepare("SELECT COUNT(*) as count FROM course_enrollments WHERE course_id = ?");
        $enrollStmt->bind_param("i", $row['id']);
        $enrollStmt->execute();
        $enrollResult = $enrollStmt->get_result();
        $enrollCount = $enrollResult->fetch_assoc()['count'] ?? 0;

        // Add counts to course data
        $row['lesson_count'] = $lessonCount;
        $row['enrollment_count'] = $enrollCount;

        $courses[] = $row;
    }

    $stmt->close();
    $conn->close();

    return $courses;
}


// Handle lesson deletion
if (isset($_GET['delete_lesson']) && is_numeric($_GET['delete_lesson'])) {
    $conn = getDbConnection();
    $lesson_id = (int)$_GET['delete_lesson'];

    // First check if the lesson belongs to a course owned by the current user
    $stmt = $conn->prepare("
        SELECT l.id FROM lesson l
        JOIN courses c ON l.course_id = c.id
        WHERE l.id = ? AND c.created_by = ?
    ");
    $stmt->bind_param("ii", $lesson_id, $_SESSION['user']['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Delete the lesson
        $deleteStmt = $conn->prepare("DELETE FROM lesson WHERE id = ?");
        $deleteStmt->bind_param("i", $lesson_id);
        $deleteStmt->execute();
        $deleteStmt->close();

        // Redirect with success message
        header("Location: course-manager.php?deleted=1");
        exit;
    } else {
        // Unauthorized deletion attempt
        header("Location: course-manager.php?error=unauthorized");
        exit;
    }

    $stmt->close();
    $conn->close();
}

// Get courses for the current user
$user_id = $_SESSION['user']['user_id'];
$courses = getUserCourses($user_id);
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100 relative">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Course Manager</h1>
            <button onclick="openCourseModal()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-[#4A90E2] hover:bg-[#357abd]">New Course</button>
        </div>

        <!-- Status Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">Lesson added successfully.</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg onclick="this.parentElement.parentElement.remove()" class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <title>Close</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                    </svg>
                </span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">Lesson deleted successfully.</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg onclick="this.parentElement.parentElement.remove()" class="fill-current h-6 w-6 text-blue-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <title>Close</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                    </svg>
                </span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">
                    <?php
                    if ($_GET['error'] === 'unauthorized') {
                        echo "You don't have permission to perform this action.";
                    } else {
                        echo "An error occurred. Please try again.";
                    }
                    ?>
                </span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg onclick="this.parentElement.parentElement.remove()" class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <title>Close</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                    </svg>
                </span>
            </div>
        <?php endif; ?>

        <!-- Course Cards Grid -->
        <?php if (count($courses) > 0): ?>
            <div class="flex flex-wrap gap-6">
                <?php foreach ($courses as $course): ?>
                    <div id="courseCard-<?php echo $course['id']; ?>" data-course-id="<?php echo $course['id']; ?>" class="w-full bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 ease-in-out hover:shadow-lg border border-gray-100">
                        <!-- Course Header -->
                        <div class="flex flex-col sm:flex-row">
                            <!-- Course Thumbnail with Status Badge -->
                            <div class="relative w-full sm:w-1/3">
                                <img class="w-full max-h-60 sm:h-full object-cover object-center" src="<?php echo !empty($course['thumbnail_url']) ? htmlspecialchars($course['thumbnail_url']) : 'assets/images/default-course.jpg'; ?>" alt="<?php echo htmlspecialchars($course['title']); ?>">

                                <!-- Status Badge -->
                                <div class="absolute top-3 left-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?php
                                                                                                if ($course['status'] === 'published') echo 'bg-green-100 text-green-800';
                                                                                                elseif ($course['status'] === 'draft') echo 'bg-gray-100 text-gray-800';
                                                                                                else echo 'bg-red-100 text-red-800';
                                                                                                ?>">
                                        <?php echo ucfirst(htmlspecialchars($course['status'])); ?>
                                    </span>
                                </div>

                                <!-- Price Badge (if applicable) -->
                                <?php if (isset($course['price']) && $course['price'] > 0): ?>
                                    <div class="absolute top-3 right-3">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            $<?php echo number_format($course['price'], 2); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Course Info -->
                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div>
                                    <!-- Title and Category -->
                                    <div class="flex justify-between items-start mb-2">
                                        <h2 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($course['title']); ?></h2>
                                        <?php if (!empty($course['category'])): ?>
                                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-md">
                                                <?php echo htmlspecialchars($course['category']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Description -->
                                    <p class="text-gray-600 text-sm mb-4">
                                        <?php
                                        $desc = strip_tags($course['description']);
                                        echo strlen($desc) > 120 ? substr($desc, 0, 120) . '...' : $desc;
                                        ?>
                                    </p>

                                    <!-- Course Stats -->
                                    <div class="grid grid-cols-3 gap-2 mb-4">
                                        <div class="text-center p-2 bg-gray-50 rounded">
                                            <span class="block text-sm font-semibold text-gray-800">
                                                <?php echo $course['lesson_count']; ?>
                                            </span>
                                            <span class="text-xs text-gray-500">Lessons</span>
                                        </div>
                                        <div class="text-center p-2 bg-gray-50 rounded">
                                            <span class="block text-sm font-semibold text-gray-800">
                                                <?php echo $course['enrollment_count']; ?>
                                            </span>
                                            <span class="text-xs text-gray-500">Students</span>
                                        </div>
                                        <div class="text-center p-2 bg-gray-50 rounded">
                                            <span class="block text-sm font-semibold text-gray-800">
                                                <?php echo date('M Y', strtotime($course['created_at'])); ?>
                                            </span>
                                            <span class="text-xs text-gray-500">Created</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <button class="manage-course-btn flex-1 bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition duration-300 flex items-center justify-center" data-course-id="<?php echo $course['id']; ?>" data-course-title="<?php echo htmlspecialchars($course['title']); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Manage Course
                                    </button>
                                    <button class="toggle-lessons-btn flex-1 bg-gray-100 text-gray-700 py-2 px-4 rounded hover:bg-gray-200 transition duration-300 flex items-center justify-center" data-course-id="<?php echo $course['id']; ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                        View Lessons
                                    </button>
                                    <a href="edit-course.php?id=<?php echo $course['id']; ?>" class="flex-1 bg-gray-100 text-gray-700 py-2 px-4 rounded hover:bg-gray-200 transition duration-300 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Expanded lessons section (hidden by default) -->
                        <div id="lessonList-<?php echo $course['id']; ?>" class="lessons-list hidden transition-all duration-300 ease-in-out">
                            <div class="p-5 bg-gray-50 border-t border-gray-100">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800">Course Lessons</h3>
                                    <button class="add-lesson-btn text-sm text-blue-500 hover:text-blue-700 flex items-center" data-course-id="<?php echo $course['id']; ?>" data-course-title="<?php echo htmlspecialchars($course['title']); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Add Lesson
                                    </button>
                                </div>

                                <div class="lesson-container-<?php echo $course['id']; ?>">
                                    <!-- Lessons will be loaded here via AJAX -->
                                    <div class="text-center py-8 lesson-loading-<?php echo $course['id']; ?>">
                                        <svg class="animate-spin h-8 w-8 mx-auto text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <p class="mt-2 text-gray-500">Loading lessons...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="p-6 mx-auto mt-40 text-center">
                <p class="text-gray-500">You haven't created any courses yet.</p>
                <button onclick="openCourseModal()" class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-[#4A90E2] hover:bg-[#357abd]">
                    Create Your First Course
                </button>
            </div>
        <?php endif; ?>

        <!-- Create Course Modal -->
        <div id="courseModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-40 hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-xl font-semibold mb-4">Create New Course</h2>

                <form id="courseForm" enctype="multipart/form-data">
                    <div class="mb-2">
                        <label class="block text-gray-600">Title</label>
                        <input type="text" name="title" class="w-full p-2 border rounded" required>
                    </div>

                    <div class="mb-2">
                        <label class="block text-gray-600">Description</label>
                        <textarea name="description" class="w-full p-2 border rounded" required></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="block text-gray-600">Category</label>
                        <input type="text" name="category" class="w-full p-2 border rounded" required>
                    </div>

                    <div class="mb-2">
                        <label class="block text-gray-600">Price</label>
                        <input type="number" name="price" step="0.01" class="w-full p-2 border rounded" required>
                    </div>

                    <div class="mb-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900" for="file_input">Upload Thumbnail</label>
                        <input type="file" name="thumbnail" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50" id="file_input">
                    </div>

                    <div class="mb-2">
                        <label class="block text-gray-600">Status</label>
                        <select name="status" class="w-full p-2 border rounded">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>

                    <input type="hidden" name="created_by" value="<?php echo $_SESSION['user']['user_id']; ?>">

                    <div id="formMessage" class="text-sm mt-2"></div>

                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" class="px-4 py-2 bg-gray-400 text-white rounded" onclick="closeCourseModal()">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save Course</button>
                    </div>
                </form>
            </div>
        </div>


        
        <!-- quiz modal -->
        <div id="myModal" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg max-h-[80vh] overflow-y-auto">
                <div class="flex flex-col justify-between items-center">

                    <div class="flex float-left">
                        <h2 class="text-lg font-bold">Form Title</h2>
                        <button class="text-gray-500 hover:text-red-500" onclick="closeQuizModal()">&times;</button>
                    </div>

                    <form id="quizForm" method="POST" action="handlers/addQuiz.php" enctype="multipart/form-data">
                        <input type="hidden" id="lessonIdInput" name="lesson_id">
                        <?php include 'quizForm.php' ?>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" name="submit_quiz" class="px-6 py-3 bg-primary hover:bg-primary/90 text-white font-medium rounded-lg transition-colors shadow-md hover:shadow-lg">
                                Create Quiz
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <!-- Course Manager Modal -->
        <div id="courseManagerModal" class="fixed z-40 inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-gray-900" id="modalCourseTitle"></h3>
                        <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-2 px-7 py-3">
                        <!-- Add Lesson Form -->
                        <form id="addLessonForm" action="/upload_lesson.php" method="POST" enctype="multipart/form-data">
                            <!-- <form id="addLessonForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data"> -->
                            <input type="hidden" id="modalCourseId" name="course_id">
                            <div class="mb-4">
                                <label for="lessonTitle" class="block text-sm font-medium text-gray-700">Lesson Title</label>
                                <input type="text" id="lessonTitle" name="title" placeholder="Enter lesson title" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="mb-4">
                                <label for="lessonContent" class="block text-sm font-medium text-gray-700">Lesson Content</label>
                                <textarea id="lessonContent" name="content" placeholder="Enter lesson content" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3"></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="lessonVideo" class="block text-sm font-medium text-gray-700">Lesson Video (optional)</label>
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
        function openQuizModal(lessonId) {
            document.getElementById('myModal').classList.remove('hidden');

            // let lessonId = event.target.getAttribute('data-lesson-id');
            console.log("🚀 ~ openQuizModal ~ lessonId:", lessonId)
            document.getElementById('lessonIdInput').value = lessonId;
            document.getElementById('myModal').classList.remove('hidden');
        }

        function closeQuizModal() {
            document.getElementById('myModal').classList.add('hidden');
        }
        // Function to open the course creation modal
        function openCourseModal() {
            document.getElementById('courseModal').classList.remove('hidden');
        }

        // Function to close the course creation modal
        function closeCourseModal() {
            document.getElementById('courseModal').classList.add('hidden');
        }

        // Function to fetch lessons for a course
        function fetchLessons(courseId) {
            const lessonContainer = document.querySelector(`.lesson-container-${courseId}`);
            const loadingElement = document.querySelector(`.lesson-loading-${courseId}`);

            if (!lessonContainer) return;

            // Show loading indicator
            if (loadingElement) {
                loadingElement.classList.remove('hidden');
            }

            // Fetch lessons via AJAX
            fetch(`fetch_lessons.php?course_id=${courseId}`)
                .then(response => response.json())
                .then(data => {
                    // Hide loading indicator
                    if (loadingElement) {
                        loadingElement.classList.add('hidden');
                    }

                    // If no lessons, show empty state
                    if (data.length === 0) {
                        lessonContainer.innerHTML = `
                            <div class="text-center py-8">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <p class="text-gray-500">No lessons have been added to this course yet.</p>
                                <button class="add-lesson-btn mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-500 hover:bg-blue-600" data-course-id="${courseId}">
                                    Create First Lesson
                                </button>
                            </div>
                        `;
                    } else {
                        // Render lessons list
                        let lessonsHTML = '<ul class="divide-y divide-gray-100">';

                        data.forEach((lesson, index) => {
                            lessonsHTML += `
                                <li class="py-3 flex items-center justify-between hover:bg-gray-100 px-2 rounded transition-colors duration-200">
                                    <div class="flex items-center">
                                        <span class="bg-gray-200 text-gray-700 rounded-full w-6 h-6 flex items-center justify-center text-xs mr-3">
                                            ${index + 1}
                                        </span>
                                        <div>
                                            <a href="lesson.php?id=${lesson.id}" class="text-gray-800 hover:text-blue-600 font-medium">
                                                ${lesson.title}
                                            </a>
                                            ${lesson.description ? `
                                            <p class="text-xs text-gray-500 mt-1">
                                                ${lesson.description.length > 60 ? lesson.description.substring(0, 60) + '...' : lesson.description}
                                            </p>
                                            ` : ''}
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        ${lesson.rating ? `
                                        <span class="text-xs text-yellow-500 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            ${lesson.rating}
                                        </span>
                                        ` : ''}
                                        <div class="flex space-x-1">
                                            <button id="quizBtn" class="text-gray-500 hover:text-blue-500 p-1" onclick="openQuizModal(${lesson.id})" data-lesson-id="${lesson.id}">
                                                <i class="fas fa-q"></i>
                                            </button>

                                            <button class="text-gray-500 hover:text-red-500 p-1" onclick="confirmDeleteLesson(${lesson.id})">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            `;
                        });

                        lessonsHTML += '</ul>';
                        lessonContainer.innerHTML = lessonsHTML;
                    }
                })
                .catch(error => {
                    console.error('Error fetching lessons:', error);
                    lessonContainer.innerHTML = `
                        <div class="text-center py-4">
                            <p class="text-red-500">Error loading lessons. Please try again.</p>
                        </div>
                    `;
                });
        }

        // Function to confirm lesson deletion
        function confirmDeleteLesson(lessonId) {
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
                    window.location.href = `course-manager.php?delete_lesson=${lessonId}`;
                }
            });
        }

        // Document ready event
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle lessons section
            const toggleButtons = document.querySelectorAll('.toggle-lessons-btn');

            toggleButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const courseId = this.getAttribute('data-course-id');
                    const lessonList = document.getElementById(`lessonList-${courseId}`);
                    const icon = this.querySelector('svg');

                    if (lessonList.classList.contains('hidden')) {
                        lessonList.classList.remove('hidden');
                        icon.style.transform = 'rotate(180deg)';
                        this.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="transform: rotate(180deg)">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            Hide Lessons
                        `;
                        fetchLessons(courseId);
                    } else {
                        lessonList.classList.add('hidden');
                        icon.style.transform = 'rotate(0)';
                        this.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            View Lessons
                        `;
                    }
                });
            });

            // Manage course button
            const manageButtons = document.querySelectorAll('.manage-course-btn');

            manageButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const courseId = this.getAttribute('data-course-id');
                    const courseTitle = this.getAttribute('data-course-title');
                    openCourseManager(courseId, courseTitle);
                });
            });

            // Add lesson buttons
            const addLessonButtons = document.querySelectorAll('.add-lesson-btn');

            addLessonButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const courseId = this.getAttribute('data-course-id');
                    const courseTitle = this.getAttribute('data-course-title');
                    openCourseManager(courseId, courseTitle);
                });
            });

            // Close modal button
            const closeModalBtn = document.getElementById('closeModal');
            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', function() {
                    document.getElementById('courseManagerModal').classList.add('hidden');
                });
            }

            // Course form submission
            const courseForm = document.getElementById('courseForm');
            if (courseForm) {
                courseForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    fetch('handlers/createCourse.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            const formMessage = document.getElementById('formMessage');
                            formMessage.textContent = data.message;
                            formMessage.className = data.success ? 'text-green-500 text-sm mt-2' : 'text-red-500 text-sm mt-2';

                            if (data.success) {
                                setTimeout(() => {
                                    closeCourseModal();
                                    location.reload();
                                }, 1500);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('formMessage').textContent = 'An error occurred. Please try again.';
                            document.getElementById('formMessage').className = 'text-red-500 text-sm mt-2';
                        });
                });
            }
        });

        // Function to open course manager modal
        function openCourseManager(courseId, courseTitle) {
            document.getElementById('modalCourseTitle').textContent = courseTitle;
            document.getElementById('modalCourseId').value = courseId;
            document.getElementById('courseManagerModal').classList.remove('hidden');
        }
    </script>
</body>

</html>