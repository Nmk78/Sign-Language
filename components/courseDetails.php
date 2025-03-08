<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$a = 34;
$isEnrolled = true;
$current_lesson_id = 0;

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

// Get course ID from URL parameter
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// if ($course_id <= 0) {
//     die("Invalid course ID");
// }

// Fetch course information
$course_stmt = $conn->prepare("SELECT title, description FROM courses WHERE id = ?");
if (!$course_stmt) {
    die("Prepare failed: " . $conn->error);
}
// $course_stmt->bind_param("i", $course_id);
$course_stmt->bind_param("i", $a);
if (!$course_stmt->execute()) {
    die("Execute failed: " . $course_stmt->error);
}
$course_result = $course_stmt->get_result();
$course = $course_result->fetch_assoc();
$course_stmt->close();

if (!$course) {
    die("Course not found");
}

// Fetch all lessons for this course
$lessons_stmt = $conn->prepare("SELECT id, title, description, created_at FROM lesson WHERE course_id = ? ORDER BY id ASC");
if (!$lessons_stmt) {
    die("Prepare failed: " . $conn->error);
}
// $lessons_stmt->bind_param("i", $course_id);
$lessons_stmt->bind_param("i", $a);

if (!$lessons_stmt->execute()) {
    die("Execute failed: " . $lessons_stmt->error);
}
$lessons_result = $lessons_stmt->get_result();
$lessons = $lessons_result->fetch_all(MYSQLI_ASSOC);
$lessons_stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title'] ?? 'Course View'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column -->
            <div class="space-y-8">
                <!-- Loading State -->
                <div id="loadingState" class="bg-white p-6 rounded-lg shadow-sm">
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
                            <button onclick="window.location.href='index.php'" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-[#4A90E2] hover:bg-[#357abd] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4A90E2]">
                                Return to Home
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Lesson Content -->
                <div id="courseContent" class="hidden"></div>

                <!-- Comments Section -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold mb-4">Comments</h3>
                    <?php
                    // You could fetch comments here
                    ?>
                    <div class="border-b pb-4">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                            <div>
                                <p class="font-medium">User123</p>
                                <p class="text-gray-600 mb-2">Great course! Really informative.</p>
                                <p class="text-sm text-gray-500">2 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Right Column -->
            <?php include 'components/currentLessons.php'; ?>
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
    // Get lesson ID from URL
    function getLessonId() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('lesson');
    }

    // Check if user is enrolled (this should be replaced with actual authentication logic)
    // For now, we'll assume the user is enrolled if a lesson ID is provided
    const isEnrolled = getLessonId() ? true : false;

    // DOM elements
    const courseContent = document.getElementById('courseContent');
    const loadingState = document.getElementById('loadingState');
    const errorState = document.getElementById('errorState');
    const errorMessage = document.getElementById('errorMessage');
    const errorDetails = document.getElementById('errorDetails');

    // Show loading state
    function showLoading() {
        loadingState.classList.remove('hidden');
        courseContent.classList.add('hidden');
        errorState.classList.add('hidden');
    }

    // Show error state
    function showError(message, details = '') {
        loadingState.classList.add('hidden');
        courseContent.classList.add('hidden');
        errorState.classList.remove('hidden');
        errorMessage.textContent = message;
        errorDetails.textContent = details;
    }

    // Show content
    function showContent() {

        console.log("Show")
        loadingState.classList.add('hidden');
        courseContent.classList.remove('hidden');
        errorState.classList.add('hidden');
    }

    // Helper function to validate base64 data
    function isValidBase64(str) {
        try {
            return btoa(atob(str)) == str;
        } catch (err) {
            return false;
        }
    }

    // Helper function to create Blob URL from base64
    function createVideoUrl(base64Data) {
        try {
            // Remove any potential data URL prefix
            const base64String = base64Data.replace(/^data:video\/\w+;base64,/, '');
            
            // Validate base64 data
            if (!isValidBase64(base64String)) {
                throw new Error('Invalid video data');
            }

            // Convert base64 to binary
            const binaryString = atob(base64String);
            const bytes = new Uint8Array(binaryString.length);
            
            for (let i = 0; i < binaryString.length; i++) {
                bytes[i] = binaryString.charCodeAt(i);
            }

            // Create blob and URL
            const blob = new Blob([bytes], { type: 'video/mp4' });
            return URL.createObjectURL(blob);
        } catch (error) {
            console.error('Error creating video URL:', error);
            return null;
        }
    }

    // Fetch lesson data
    function fetchLesson(lessonId) {
        if (!lessonId) {
            showError('No Lesson Selected', 'Please select a lesson from the list.');
            return;
        }

        showLoading();

        fetch(`/handlers/getLesson.php?lesson=${lessonId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(response.status === 404 ? 'Lesson not found' : 'Failed to load lesson');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }

                let videoElement = '';
                if (data.video_data) {
                    const videoUrl = createVideoUrl(data.video_data);
                    
                    if (videoUrl) {
                        videoElement = `
                            <div class="aspect-video bg-black">
                                <video 
                                    controls 
                                    class="w-full h-full" 
                                    id="lessonVideo"
                                    preload="metadata"
                                >
                                    <source src="${videoUrl}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                            <div id="videoError" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-2">
                                <p>Video playback error. Please try a different browser or contact support.</p>
                            </div>
                        `;
                    } else {
                        videoElement = `
                            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
                                <p>Error loading video. Please try refreshing the page or contact support.</p>
                            </div>
                        `;
                    }
                } else {
                    videoElement = `
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
                            <p>No video available for this lesson.</p>
                        </div>
                    `;
                }

                courseContent.innerHTML = `
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        ${videoElement}
                        <div class="p-4">
                            <h2 class="text-xl font-semibold mb-2">${data.title}</h2>
                            <p class="text-sm text-gray-600 mb-4">${data.description}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Category: ${data.category}</span>
                                <div class="flex space-x-2">
                                    <button onclick="navigateToNextLesson(${data.id})" class="bg-gray-200 text-gray-800 px-3 py-1 rounded-md text-sm hover:bg-gray-300">
                                        <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                        Next Lesson
                                    </button>
                                    <button onclick="markLessonComplete(${data.id})" class="bg-[#4A90E2] text-white px-3 py-1 rounded-md text-sm hover:bg-[#357abd]">
                                        Complete Lesson
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                showContent();

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

                    // Clean up Blob URL when video is removed
                    video.addEventListener('emptied', () => {
                        if (video.src.startsWith('blob:')) {
                            URL.revokeObjectURL(video.src);
                        }
                    });
                }
            })
            .catch(error => {
                showError('Error Loading Lesson', error.message);
                console.error('Fetch error:', error);
            });
    }

    // Navigate to next lesson
    function navigateToNextLesson(currentLessonId) {
        // This is a placeholder - in a real app, you would fetch the next lesson ID from the server
        const nextLessonId = currentLessonId + 1;
        changeLesson(nextLessonId);
    }

    // Mark lesson as complete
    function markLessonComplete(lessonId) {
        // This is a placeholder - in a real app, you would send a request to mark the lesson as complete
        alert(`Lesson ${lessonId} marked as complete!`);
        // Then potentially navigate to the next lesson
        navigateToNextLesson(lessonId);
    }

    // Change lesson and update URL
    function changeLesson(lessonId) {
        const newUrl = `${window.location.pathname}?lesson=${lessonId}`;
        history.pushState({}, '', newUrl);
        fetchLesson(lessonId);
    }

    // Listen for changes in URL parameters (browser back/forward)
    window.addEventListener('popstate', function () {
        fetchLesson(getLessonId());
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        const lessonId = getLessonId();
        if (lessonId) {
            fetchLesson(lessonId);
        } else {
            showError('No Lesson Selected', 'Please select a lesson from the list.');
        }
    });

    // Clean up any Blob URLs when navigating away
    window.addEventListener('beforeunload', () => {
        const video = document.getElementById('lessonVideo');
        if (video && video.src.startsWith('blob:')) {
            URL.revokeObjectURL(video.src);
        }
    });
    </script>
</body>
</html>

