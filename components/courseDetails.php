<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "sign_language";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get course and lesson IDs from URL parameters
$course_id = isset($_GET['course']) ? intval($_GET['course']) : 0;
$lesson_id = isset($_GET['lesson']) ? intval($_GET['lesson']) : 0;

// Fetch course information
$course_stmt = $conn->prepare("SELECT title, description FROM courses WHERE id = ?");
$course_stmt->bind_param("i", $course_id);
$course_stmt->execute();
$course_result = $course_stmt->get_result();
$course = $course_result->fetch_assoc();
$course_stmt->close();

if (!$course) {
    $course = ['title' => 'Course Not Found', 'description' => 'The requested course could not be found.'];
}

// Fetch current lesson information if a lesson ID is provided
$current_lesson = null;
if ($lesson_id > 0) {
    $lesson_stmt = $conn->prepare("SELECT id, title, description, category, created_by, created_at, course_id, rating, video_url FROM lesson WHERE id = ?");
    $lesson_stmt->bind_param("i", $lesson_id);
    $lesson_stmt->execute();
    $lesson_result = $lesson_stmt->get_result();
    $current_lesson = $lesson_result->fetch_assoc();
    $lesson_stmt->close();
}

// Fetch all lessons for this course
$lessons_stmt = $conn->prepare("SELECT id, title, description, created_at FROM lesson WHERE course_id = ? ORDER BY id ASC");
$lessons_stmt->bind_param("i", $course_id);
$lessons_stmt->execute();
$lessons_result = $lessons_stmt->get_result();
$lessons = $lessons_result->fetch_all(MYSQLI_ASSOC);
$lessons_stmt->close();

// Fetch comments for the current lesson
$comments = [];
if ($lesson_id > 0) {
    $comments_stmt = $conn->prepare("
        SELECT lc.id, lc.comment, lc.created_at, u.username, u.profile 
        FROM lesson_comments lc
        JOIN users u ON lc.user_id = u.id
        WHERE lc.lesson_id = ?
        ORDER BY lc.created_at DESC
    ");

    // If the join with users table fails (e.g., if the users table has a different structure),
    // use this simpler query instead:
    /*
    $comments_stmt = $conn->prepare("
        SELECT id, user_id, comment, created_at
        FROM lesson_comments
        WHERE lesson_id = ? AND parent_comment_id IS NULL
        ORDER BY created_at DESC
    ");
    */

    $comments_stmt->bind_param("i", $lesson_id);
    $comments_stmt->execute();
    $comments_result = $comments_stmt->get_result();
    $comments = $comments_result->fetch_all(MYSQLI_ASSOC);
    $comments_stmt->close();
}

// Close the database connection
$conn->close();

// Helper function to format dates
function formatTimeAgo($datetime)
{
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;

    if ($diff < 60) {
        return "just now";
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . " minute" . ($mins > 1 ? "s" : "") . " ago";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . " day" . ($days > 1 ? "s" : "") . " ago";
    } elseif ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return $weeks . " week" . ($weeks > 1 ? "s" : "") . " ago";
    } else {
        return date("M j, Y", $time);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title'] ?? 'Course View'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-50">
    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column (2/3 width on large screens) -->
            <div class="lg:col-span-2">

                <!-- Loading State -->
                <div id="loadingState" class="bg-white p-6 rounded-lg shadow-sm <?php echo $current_lesson ? 'hidden' : ''; ?>">
                    <div class="animate-pulse flex flex-col space-y-4">
                        <div class="rounded-lg bg-gray-200 h-64 w-full"></div>
                        <div class="h-6 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-4 bg-gray-200 rounded w-full"></div>
                        <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                    </div>
                </div>

                <!-- Error State -->
                <div id="errorState" class="bg-white p-6 rounded-lg shadow-sm hidden">
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900" id="errorMessage">Error loading lesson</h3>
                        <p class="mt-1 text-sm text-gray-500" id="errorDetails"></p>
                        <div class="mt-6">
                            <button onclick="window.location.href='/'" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-[#4A90E2] hover:bg-[#357abd] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4A90E2]">
                                Return to Home
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Lesson Content -->
                <div id="courseContent" class="<?php echo $current_lesson ? '' : 'hidden'; ?>">
                    <?php if ($current_lesson): ?>
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <div class="aspect-video bg-">
                                <?php if (!empty($current_lesson['video_url'])): ?>
                                    <video controls class="w-full h-full" id="lessonVideo" preload="metadata">
                                        <source src="<?php echo htmlspecialchars($current_lesson['video_url']); ?>" type="video/mp4">
                                        <source src="<?php echo htmlspecialchars($current_lesson['video_url']); ?>" type="video/mov">
                                        <source src="<?php echo htmlspecialchars($current_lesson['video_url']); ?>" type="video/webm">
                                        Your browser does not support the video tag.
                                    </video>
                                    <div id="videoError" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-2">
                                        <p>Video playback error. Please try a different browser or contact support.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="flex items-center justify-center h-full bg-gray-800 text-white">
                                        <p>No video available for this lesson</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="p-4">
                                <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($current_lesson['title']); ?></h2>
                                <p class="text-sm text-gray-600 mb-4"><?php echo htmlspecialchars($current_lesson['description']); ?></p>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Category: <?php echo htmlspecialchars($current_lesson['category'] ?? 'Uncategorized'); ?></span>
                                    <div class="flex space-x-2">
                                        <button onclick="navigateToNextLesson(<?php echo $current_lesson['id']; ?>)" class="bg-gray-200 text-gray-800 px-3 py-1 rounded-md text-sm hover:bg-gray-300">
                                            <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg>
                                            Next Lesson
                                        </button>
                                        <button onclick="markLessonComplete(<?php echo $current_lesson['id']; ?>)" class="bg-[#4A90E2] text-white px-3 py-1 rounded-md text-sm hover:bg-[#357abd]">
                                            Complete Lesson
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-5">
                <?php include 'components/comments.php'; ?>

                </div>
            </div>

            <!-- Right Column (1/3 width on large screens) -->
            <!-- <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold mb-4">Course Lessons</h3>
                    <div class="space-y-3">
                        <?php if (empty($lessons)): ?>
                            <p class="text-gray-500">No lessons available for this course.</p>
                        <?php else: ?>
                            <?php foreach ($lessons as $lesson): ?>
                                <a
                                    href="?course=<?php echo $course_id; ?>&lesson=<?php echo $lesson['id']; ?>"
                                    class="block p-3 rounded-md hover:bg-gray-50 <?php echo ($lesson_id == $lesson['id']) ? 'bg-blue-50 border-l-4 border-[#4A90E2]' : ''; ?>">
                                    <h4 class="font-medium"><?php echo htmlspecialchars($lesson['title']); ?></h4>
                                    <p class="text-sm text-gray-500 truncate"><?php echo htmlspecialchars(substr($lesson['description'], 0, 60) . (strlen($lesson['description']) > 60 ? '...' : '')); ?></p>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div> -->

            <div class="lg:col-span-1">
                <!-- Course Title -->
                <div class="bg-white p-6 rounded-t-lg shadow-sm border border-gray-100">
                    <h1 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($course['title']); ?></h1>
                    <p class="mt-2 text-gray-600"><?php echo htmlspecialchars($course['description']); ?></p>
                </div>
                <div class="bg-white p-6 rounded-b-lg shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-semibold">Course Lessons</h3>
                        <div class="text-sm text-gray-500">
                            <span class="font-medium text-indigo-600"><?php echo count(array_filter($lessons, function ($l) {
                                                                            return isset($l['completed']) && $l['completed'];
                                                                        })); ?></span>
                            <span>/</span>
                            <span><?php echo count($lessons); ?></span>
                            <span class="ml-1">completed</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <?php if (empty($lessons)): ?>
                            <div class="py-8 flex flex-col items-center justify-center text-center">
                                <div class="bg-gray-100 p-3 rounded-full mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <p class="text-gray-500">No lessons available for this course.</p>
                                <button class="mt-3 text-sm text-indigo-600 hover:text-indigo-800 font-medium">Refresh List</button>
                            </div>
                        <?php else: ?>
                            <?php foreach ($lessons as $index => $lesson): ?>
                                <?php
                                $isActive = ($lesson_id == $lesson['id']);
                                $isCompleted = isset($lesson['completed']) && $lesson['completed'];
                                $lessonNumber = $index + 1;
                                ?>
                                <a
                                    href="?course=<?php echo $course_id; ?>&lesson=<?php echo $lesson['id']; ?>"
                                    class="flex items-start gap-3 border  p-3 rounded-lg transition-all duration-200 relative
                            <?php echo $isActive
                                    ? 'bg-indigo-50 border-indigo-100'
                                    : 'hover:bg-gray-50  border-transparent hover:border-gray-100'; ?>">

                                    <!-- Lesson number or status indicator -->
                                    <!-- <div class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-sm font-medium
                            <?php if ($isCompleted): ?>
                                bg-green-100 text-green-700
                            <?php elseif ($isActive): ?>
                                bg-indigo-100 text-indigo-700
                            <?php else: ?>
                                bg-gray-100 text-gray-700
                            <?php endif; ?>">
                            <?php if ($isCompleted): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            <?php else: ?>
                                <?php echo $lessonNumber; ?>
                            <?php endif; ?>
                        </div> -->

                                    <div id="videoContainer" class="relative h-28 aspect-square bg-gray-300 rounded-lg flex items-center justify-center cursor-pointer group overflow-hidden">
                                        <!-- Play Button -->
                                        <div class=" size-8 bg-gray-500 rounded-full flex items-center justify-center">
                                            <i class="fa-solid fa-play text-white"></i>
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <h4 class="font-medium text-gray-900 line-clamp-1"><?php echo htmlspecialchars($lesson['title']); ?></h4>

                                            <?php if (isset($lesson['duration'])): ?>
                                                <span class="text-xs text-gray-500 flex items-center ml-2 flex-shrink-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <?php echo $lesson['duration']; ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <p class="text-sm text-gray-500 line-clamp-2 mt-0.5">
                                            <?php echo htmlspecialchars($lesson['description']); ?>
                                        </p>

                                        <?php if ($isActive && !$isCompleted): ?>
                                            <span class="inline-flex items-center mt-2 text-xs font-medium text-indigo-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Continue Learning
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($isActive): ?>
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500 rounded-l-lg"></div>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <!-- Chat Button -->
    <div class="fixed bottom-8 right-8">
        <button class="bg-[#4A90E2] text-white p-4 rounded-full shadow-lg hover:bg-[#357abd]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
            </svg>
        </button>
    </div>

    <script>
        // DOM elements
        const courseContent = document.getElementById('courseContent');
        const loadingState = document.getElementById('loadingState');
        const errorState = document.getElementById('errorState');
        const errorMessage = document.getElementById('errorMessage');
        const errorDetails = document.getElementById('errorDetails');
        const commentForm = document.getElementById('commentForm');
        const commentText = document.getElementById('commentText');
        const commentsList = document.getElementById('commentsList');

        // Add video error handling
        const video = document.getElementById('lessonVideo');
        const videoError = document.getElementById('videoError');

        if (video) {
            video.addEventListener('error', (e) => {
                console.error('Video Error:', e);
                if (videoError) {
                    videoError.classList.remove('hidden');
                }
            });
        }

        // Handle comment form submission
        if (commentForm) {
            commentForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!commentText.value.trim()) {
                    alert('Please enter a comment');
                    return;
                }

                const lessonId = <?php echo $lesson_id ?: 0; ?>;

                if (!lessonId) {
                    alert('No lesson selected');
                    return;
                }

                // In a real application, you would send this to the server
                // For now, we'll just add it to the UI
                const now = new Date();
                const comment = {
                    username: 'You',
                    comment: commentText.value,
                    created_at: now.toISOString()
                };

                // Add the new comment to the top of the list
                const commentElement = document.createElement('div');
                commentElement.className = 'border-b pb-4 mb-4';
                commentElement.innerHTML = `
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-semibold">
                        Y
                    </div>
                    <div>
                        <p class="font-medium">${comment.username}</p>
                        <p class="text-gray-600 mb-2">${comment.comment}</p>
                        <p class="text-sm text-gray-500">just now</p>
                    </div>
                </div>
            `;

                // If there's a "no comments" message, remove it
                const noCommentsMessage = commentsList.querySelector('.text-center');
                if (noCommentsMessage) {
                    noCommentsMessage.remove();
                }

                // Add the new comment at the top
                commentsList.insertBefore(commentElement, commentsList.firstChild);

                // Clear the form
                commentText.value = '';

                // In a real application, you would send an AJAX request like this:
                /*
                fetch('/handlers/addComment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        lesson_id: lessonId,
                        comment: comment.comment
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to post comment. Please try again.');
                });
                */
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const userId = localStorage.getItem('id');
            if (userId) {
                document.getElementById('userId').value = userId;
            } else {
                alert('User ID not found in local storage.');
            }
        });
        // Navigate to next lesson
        function navigateToNextLesson(currentLessonId) {
            // Get all lesson links
            const lessonLinks = document.querySelectorAll('a[href^="?course="]');
            let nextLessonLink = null;
            let foundCurrent = false;

            // Find the current lesson and get the next one
            for (const link of lessonLinks) {
                if (foundCurrent) {
                    nextLessonLink = link;
                    break;
                }

                if (link.href.includes(`lesson=${currentLessonId}`)) {
                    foundCurrent = true;
                }
            }

            // Navigate to the next lesson if found
            if (nextLessonLink) {
                window.location.href = nextLessonLink.href;
            } else {
                alert('This is the last lesson in the course.');
            }
        }

        // Mark lesson as complete
        function markLessonComplete(lessonId) {
            // In a real application, you would send an AJAX request to mark the lesson as complete
            alert(`Lesson ${lessonId} marked as complete!`);

            // Then navigate to the next lesson
            navigateToNextLesson(lessonId);
        }
    </script>
</body>

</html>