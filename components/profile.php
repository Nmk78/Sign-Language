<?php
// session_start();
$conn = new mysqli("localhost", "root", "root", "sign_language");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from localStorage using JavaScript
echo "<script>
    var userId = localStorage.getItem('id');
    if (userId) {
        document.cookie = 'user_id=' + userId + '; path=/; expires=' + new Date(Date.now() + 30*24*60*60*1000).toUTCString(); 
    }
</script>";

// Read user ID from cookies in PHP
if (isset($_COOKIE['user_id'])) {
    $user_id = intval($_COOKIE['user_id']);
    $_SESSION['user_id'] = $user_id; // Store in session for extra persistence
} else {
    $user_id = 0;
}

// Fetch approved enrolled courses
$sql = "SELECT c.id, c.title, ce.enrolled_at FROM course_enrollments ce
        JOIN courses c ON ce.course_id = c.id
        WHERE ce.user_id = ? AND ce.status = 'approved'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$currentCourses = [];

while ($row = $result->fetch_assoc()) {
    $currentCourses[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'enrolled_at' => $row['enrolled_at']
    ];
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - EDUTOCK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#1e5dac',
                        'primary-dark': '#154785',
                        'secondary': '#b7c5da',
                        'accent': '#eae2e4',
                        'success': '#10B981',
                        'warning': '#F1C40F',
                        'error': '#E74C3C',
                        'background': '#f8fafb',
                        'surface': '#FFFFFF',
                        'text': '#333333',
                        'text-light': '#7F8C8D',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    boxShadow: {
                        'card': '0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.03)',
                    }
                }
            }
        };


    </script>
    <script>
        // Function to update the URL with saved courses from localStorage
        function updateSavedCoursesUrl() {
            const savedCourses = JSON.parse(localStorage.getItem('savedCourses') || '[]');
            // console.log(savedCourses);
            const url = new URL(window.location.href);
            url.searchParams.set('savedCourses', JSON.stringify(savedCourses));
            window.history.replaceState({}, document.title, url);
        }

        // Function to remove a saved course
        function removeSavedCourse(courseId) {
                let savedCourses = JSON.parse(localStorage.getItem('savedCourses') || '[]');
                savedCourses = savedCourses.filter(id => id !== courseId);
                localStorage.setItem('savedCourses', JSON.stringify(savedCourses));
                updateSavedCoursesUrl();
                location.reload(); // Refresh to update the display
        }

        // Run on page load to sync URL with localStorage
        document.addEventListener('DOMContentLoaded', function () {
            updateSavedCoursesUrl();
        });
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .progress-bar {
            height: 8px;
            border-radius: 4px;
            background-color: #E2E8F0;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        .avatar-option {
            transition: all 0.2s ease;
        }

        .avatar-option:hover {
            transform: scale(1.1);
            border-color: #1e5dac;
        }
    </style>
</head>

<body class="bg-background min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Profile Header -->
        <div class="bg-surface rounded-2xl shadow-card p-8 mb-8 transition-all duration-300">
            <div class="flex flex-col md:flex-row items-start gap-8">
                <!-- Avatar -->
                <div
                    class="w-32 h-32 relative group rounded-full overflow-hidden bg-primary flex items-center justify-center shadow-lg border-4 border-white">
                    <img id="profile-avatar" src="/placeholder.svg" alt="User Avatar"
                        class="w-full h-full object-cover">
                    <button onclick="openAvatarModal()"
                        class="absolute opacity-0 group-hover:opacity-100 inset-0 bg-black bg-opacity-50 flex items-center justify-center transition-opacity duration-300">
                        <i class="fas fa-camera text-white text-xl"></i>
                    </button>
                </div>

                <div class="flex-1">
                    <div class="flex flex-col md:flex-row md:items-center gap-4 mb-4">
                        <div>
                            <h1 id="profile-name" class="text-3xl font-bold text-text">Loading...</h1>
                            <p id="user-email" class="text-text-light mt-1 flex items-center">
                                <i class="fas fa-envelope mr-2 text-primary"></i>
                                <span>loading...</span>
                            </p>
                        </div>
                        <button onclick="logout()"
                            class="md:ml-auto bg-error hover:bg-red-600 text-white px-6 py-2.5 rounded-lg transition-colors duration-300 flex items-center gap-2 shadow-sm">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                        <?php
                        // session_start();
                        include 'utils/db.php'; // Include database connection
                        
                        $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

                        if ($user_id > 0) {
                            // Query to count completed courses
                            $query = "SELECT COUNT(DISTINCT l.course_id) AS completed_courses 
                                    FROM lesson l
                                    JOIN completed_lessons cl ON l.id = cl.lesson_id
                                    WHERE cl.user_id = ?";

                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $stmt->bind_result($completed_courses);
                            $stmt->fetch();
                            $stmt->close();
                        } else {
                            $completed_courses = 0;
                        }
                        ?>

                        <div class="bg-primary/10 px-6 py-4 rounded-xl flex items-center gap-4 card-hover">
                            <div class="bg-primary bg-opacity-20 p-3 rounded-full">
                                <i class="fas fa-graduation-cap text-primary text-xl"></i>
                            </div>
                            <div>
                                <span class="text-primary text-2xl font-bold"><?php echo $completed_courses; ?></span>
                                <p class="text-text-light text-sm"> Courses Completed</p>
                            </div>
                        </div>

                        <?php
                        // session_start();
                        include 'utils/db.php'; // Database connection
                        
                        $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
                        $completion_percentage = 0;

                        if ($user_id > 0) {
                            // Query to calculate the correct completion percentage
                            $query = "SELECT 
                                        (COUNT(DISTINCT completed_courses.course_id) / NULLIF(COUNT(DISTINCT enrolled_courses.course_id), 0)) * 100 AS completion_percentage
                                    FROM course_enrollments ce
                                    LEFT JOIN (
                                        SELECT l.course_id, cl.user_id
                                        FROM lesson l
                                        LEFT JOIN completed_lessons cl 
                                            ON l.id = cl.lesson_id 
                                            AND cl.user_id = ?
                                        WHERE l.id = (SELECT MAX(id) FROM lesson WHERE course_id = l.course_id) -- Get last lesson of course
                                    ) AS completed_courses 
                                    ON ce.course_id = completed_courses.course_id AND ce.user_id = completed_courses.user_id
                                    JOIN (
                                        SELECT DISTINCT course_id FROM course_enrollments WHERE user_id = ? AND status = 'approved'
                                    ) AS enrolled_courses 
                                    ON ce.course_id = enrolled_courses.course_id
                                    WHERE ce.user_id = ? AND ce.status = 'approved';
                                    ";

                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("iii", $user_id, $user_id, $user_id);
                            $stmt->execute();
                            $stmt->bind_result($completion_percentage);
                            $stmt->fetch();
                            $stmt->close();

                            // Ensure valid percentage
                            $completion_percentage = $completion_percentage ? round($completion_percentage, 2) : 0;
                        }
                        ?>

                        <div class="bg-success/10 px-6 py-4 rounded-xl flex items-center gap-4 card-hover">
                            <div class="bg-success bg-opacity-20 p-3 rounded-full">
                                <i class="fas fa-chart-line text-success text-xl"></i>
                            </div>
                            <div>
                                <span
                                    class="text-success text-2xl font-bold"><?php echo $completion_percentage; ?>%</span>
                                <p class="text-text-light text-sm"> Completion Rate</p>
                            </div>
                        </div>

                        <div class="bg-warning/10 px-6 py-4 rounded-xl flex items-center gap-4 card-hover">
                            <div class="bg-warning bg-opacity-20 p-3 rounded-full">
                                <i class="fas fa-clock text-warning text-xl"></i>
                            </div>
                            <div>
                                <span
                                    class="text-warning text-2xl font-bold"><?php echo count($currentCourses); ?></span>
                                <p class="text-text-light text-sm"> Active Courses</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // session_start();
        include 'utils/db.php'; // Database connection
        
        $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
        $journeyPoints = [];

        if ($user_id > 0) {
            // Fetch all enrolled courses
            $query = "SELECT c.id, c.title, 
                    (SELECT COUNT(*) FROM completed_lessons cl 
                    JOIN lesson l ON cl.lesson_id = l.id 
                    WHERE l.course_id = c.id AND cl.user_id = ce.user_id) AS completed_lessons,
                    (SELECT COUNT(*) FROM lesson WHERE course_id = c.id) AS total_lessons
                FROM course_enrollments ce
                JOIN courses c ON ce.course_id = c.id
                WHERE ce.user_id = ? AND ce.status = 'approved'";

            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $status = 'ongoing'; // Default status
        
                if ($row['total_lessons'] > 0) {
                    if ($row['completed_lessons'] == $row['total_lessons']) {
                        $status = 'completed';
                    } elseif ($row['completed_lessons'] > 0) {
                        $status = 'in-progress';
                    }
                }

                $journeyPoints[] = [
                    'title' => $row['title'],
                    'status' => $status,
                    'completed' => $row['completed_lessons'],
                    'total' => $row['total_lessons']
                ];
            }
            $stmt->close();
        }
        ?>

        <!-- Learning Journey Map -->
        <div class="bg-surface rounded-2xl shadow-card p-8 mb-8 transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-text">Learning Journey</h2>
                <div class="flex gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-success rounded-full"></div>
                        <span class="text-sm text-text-light">Completed</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-warning rounded-full"></div>
                        <span class="text-sm text-text-light">In Progress</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                        <span class="text-sm text-text-light">Not Started</span>
                    </div>
                </div>
            </div>

            <?php if (empty($journeyPoints)): ?>
                <div class="text-center py-10">
                    <i class="fas fa-map-signs text-4xl text-gray-300 mb-4"></i>
                    <p class="text-text-light">Your learning journey will appear here once you enroll in courses.</p>
                </div>
            <?php else: ?>
                <div class="relative">
                    <!-- Journey Path -->
                    <div class="absolute top-1/2 left-0 right-0 h-2 bg-primary/20 -translate-y-1/2 rounded-full"></div>
                    <div
                        class="relative grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-<?php echo min(count($journeyPoints), 5); ?> gap-8 py-8">
                        <?php
                        foreach ($journeyPoints as $point) {
                            $statusColor = match ($point['status']) {
                                'completed' => 'bg-success',
                                'in-progress' => 'bg-warning',
                                'ongoing' => 'bg-gray-300'
                            };

                            $statusIcon = match ($point['status']) {
                                'completed' => '<i class="fas fa-check text-white"></i>',
                                'in-progress' => '<i class="fas fa-spinner text-white"></i>',
                                'ongoing' => '<i class="fas fa-hourglass text-white"></i>'
                            };

                            $progressPercentage = $point['total'] > 0 ? ($point['completed'] / $point['total']) * 100 : 0;

                            echo "
                            <div class='flex flex-col items-center'>
                                <div class='w-10 h-10 {$statusColor} rounded-full mb-4 z-10 flex items-center justify-center shadow-md'>
                                    {$statusIcon}
                                </div>
                                <h3 class='font-medium text-text text-center'>{$point['title']}</h3>
                                <span class='text-sm text-text-light mb-2'>" . ucfirst($point['status']) . "</span>
                                <div class='w-full max-w-[120px] progress-bar mt-2'>
                                    <div class='progress-bar-fill {$statusColor}' style='width: {$progressPercentage}%'></div>
                                </div>
                                <span class='text-xs text-text-light mt-1'>{$point['completed']}/{$point['total']} lessons</span>
                            </div>";
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Current Courses -->
        <div class="bg-surface rounded-2xl shadow-card p-8 transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-text">Enrolled Courses</h2>
                <a href="category"
                    class="text-primary hover:text-primary-dark flex items-center gap-1 transition-colors">
                    <span>Browse more courses</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <?php if (empty($currentCourses)): ?>
                <div class="text-center py-10 bg-gray-50 rounded-xl">
                    <i class="fas fa-book-open text-4xl text-gray-300 mb-4"></i>
                    <p class="text-text-light mb-4">You haven't enrolled in any courses yet.</p>
                    <a href="/courses"
                        class="inline-block bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded-lg transition-colors">
                        Explore Courses
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php
                    foreach ($currentCourses as $course) {
                        $enrolledDate = new DateTime($course['enrolled_at']);
                        $formattedDate = $enrolledDate->format('M d, Y');

                        echo "
                        <div class='p-6 border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 bg-white card-hover'>
                            <div class='flex items-start gap-4'>
                                <div class='bg-primary/10 p-3 rounded-lg'>
                                    <i class='fas fa-book text-primary'></i>
                                </div>
                                <div class='flex-1'>
                                    <h3 class='font-medium text-text mb-1'>{$course['title']}</h3>
                                    <div class='flex items-center text-sm text-text-light'>
                                        <i class='fas fa-calendar-alt mr-2'></i>
                                        <span>Enrolled on {$formattedDate}</span>
                                    </div>
                                    <a href='/courseDetails?course={$course['id']}' class='mt-4 inline-block text-primary hover:text-primary-dark text-sm font-medium'>
                                        Continue Learning →
                                    </a>
                                </div>
                            </div>
                        </div>";
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <?php
        // Assume saved course IDs are passed somehow (e.g., via GET parameter for simplicity)
// For this example, we'll simulate with a default value if not provided
        $savedCourseIds = isset($_GET['savedCourses']) ? json_decode($_GET['savedCourses'], true) : [68, 72]; // Default for testing
        
        $savedCourses = [];
        if (!empty($savedCourseIds) && is_array($savedCourseIds)) {
            // Ensure we have a valid database connection
            if (!isset($conn)) {
                $conn = new mysqli("localhost", "root", "root", "sign_language");
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
            }

            // Convert array to comma-separated string for SQL IN clause
            $placeholders = implode(',', array_fill(0, count($savedCourseIds), '?'));
            $sql_saved = "SELECT id, title, thumbnail_url, created_at 
                  FROM courses 
                  WHERE id IN ($placeholders)";

            $stmt_saved = $conn->prepare($sql_saved);
            if ($stmt_saved) {
                $types = str_repeat('i', count($savedCourseIds)); // All IDs are integers
                $stmt_saved->bind_param($types, ...$savedCourseIds);
                $stmt_saved->execute();
                $result_saved = $stmt_saved->get_result();

                while ($row = $result_saved->fetch_assoc()) {
                    $savedCourses[] = [
                        'id' => $row['id'],
                        'title' => $row['title'],
                        'thumbnail_url' => $row['thumbnail_url'],
                        'saved_at' => $row['created_at'] // Using created_at as a proxy for saved_at
                    ];
                }
                $stmt_saved->close();
            }
        }
        ?>

        <!-- Saved Courses -->
        <div class="bg-surface rounded-2xl shadow-card p-8 my-8 transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-text">Saved Courses</h2>
                <a href="category"
                    class="text-primary hover:text-primary-dark flex items-center gap-1 transition-colors">
                    <span>Browse more courses</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <?php if (empty($savedCourses)): ?>
                <div class="text-center py-10 bg-gray-50 rounded-xl">
                    <i class="fas fa-bookmark text-4xl text-gray-300 mb-4"></i>
                    <p class="text-text-light mb-4">You haven't saved any courses yet.</p>
                    <a href="/courses"
                        class="inline-block bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded-lg transition-colors">
                        Explore Courses
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php
                    foreach ($savedCourses as $course) {
                        $savedDate = new DateTime($course['saved_at']);
                        $formattedDate = $savedDate->format('M d, Y');

                        echo "
                <div class='p-6 border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 bg-white card-hover'>
                    <div class='flex items-start gap-4'>
                        <div class='w-16 h-16 rounded-lg overflow-hidden flex-shrink-0'>
                            <img src='" . ($course['thumbnail_url'] ?? '/placeholder.svg') . "' alt='Course thumbnail' class='w-full h-full object-cover'>
                        </div>
                        <div class='flex-1'>
                            <h3 class='font-medium text-text mb-1'>" . htmlspecialchars($course['title']) . "</h3>
                            <div class='flex items-center text-sm text-text-light'>
                                <i class='fas fa-bookmark mr-2'></i>
                                <span>Saved on {$formattedDate}</span>
                            </div>
                            <div class='mt-4 flex gap-2'>
                                <a href='/courseDetails?course={$course['id']}' class='text-primary hover:text-primary-dark text-sm font-medium'>
                                    View Details →
                                </a>
                                <button onclick='removeSavedCourse({$course['id']})' class='text-error hover:text-red-600 text-sm font-medium'>
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>";
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>



        <!-- Recent Activity Feed -->
        <div class="bg-surface rounded-2xl shadow-card p-8 my-8 transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-text">Recent Activity</h2>
                <button class="text-primary hover:text-primary-dark flex items-center gap-1 transition-colors">
                    <!-- <span>View all</span> -->
                    <!-- <i class="fas fa-arrow-right"></i> -->
                </button>
            </div>


            <?php
            // Start session to get user_id
// session_start();
            $user_id = $_SESSION['user_id']; // Default to 24 for testing, replace with actual session value
            
            // Database connection
            $conn = mysqli_connect("localhost", "root", "root", "sign_language");
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Prepare query to fetch profile updates from logs and completed lessons from completed_lessons
            $stmt = $conn->prepare("
    SELECT 
        'profile' AS activity_type,
        l.id AS activity_id,
        NULL AS lesson_id,
        l.timestamp AS activity_time,
        'Profile updated' AS details
    FROM logs l
    WHERE l.user_id = ? 
    AND l.entity_type = 'profile' 
    AND l.action = 'update'
    
    UNION ALL
    
    SELECT 
        'lesson_completed' AS activity_type,
        cl.id AS activity_id,
        cl.lesson_id,
        cl.completed_at AS activity_time,
        CONCAT('Completed lesson \"', les.title, '\" in \"', c.title, '\"') AS details
    FROM completed_lessons cl
    LEFT JOIN lesson les ON cl.lesson_id = les.id
    LEFT JOIN courses c ON les.course_id = c.id
    WHERE cl.user_id = ?
    
    ORDER BY activity_time DESC
    LIMIT 10
");
            if ($stmt === false) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("ii", $user_id, $user_id); // Two integer parameters for the two ? placeholders
            $stmt->execute();
            $result = $stmt->get_result();
            $recentActivities = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            // Display recent activities
            if (empty($recentActivities)) {
                echo '<div class="text-center py-10 bg-gray-50 rounded-xl">
            <i class="fas fa-history text-4xl text-gray-300 mb-4"></i>
            <p class="text-text-light">No recent activity to display.</p>
          </div>';
            } else {
                echo '<div class="space-y-4">';
                foreach ($recentActivities as $activity) {
                    // Determine icon based on activity_type
                    $icon = match ($activity['activity_type']) {
                        'lesson_completed' => '<div class="bg-success/10 p-3 rounded-full"><i class="fas fa-check-circle text-success"></i></div>',
                        'profile' => '<div class="bg-primary/10 p-3 rounded-full"><i class="fas fa-user-edit text-primary"></i></div>',
                        default => '<div class="bg-gray-100 p-3 rounded-full"><i class="fas fa-circle-info text-gray-500"></i></div>'
                    };

                    // Title is directly from details
                    $title = htmlspecialchars($activity['details']);

                    // Use raw date from activity_time
                    $date = date('F j, Y, g:i A', strtotime($activity['activity_time'])); // e.g., "March 11, 2025, 11:35 AM"
            
                    echo "<div class='flex items-start gap-4 p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition-colors'>
                {$icon}
                <div class='flex-1'>
                    <p class='text-text'>{$title}</p>
                    <p class='text-sm text-text-light'>{$date}</p>
                </div>
              </div>";
                }
                echo '</div>';
            }

            $conn->close();
            ?>
        </div>

        <!-- Add this right before the closing </body> tag -->
        <script>
            // Add this to the existing script section

            // Toggle for notes section
            document.addEventListener('DOMContentLoaded', function () {
                // Simulate loading states
                setTimeout(() => {
                    document.querySelectorAll('.progress-bar-fill').forEach(bar => {
                        const width = bar.style.width;
                        bar.style.width = '0';
                        setTimeout(() => {
                            bar.style.width = width;
                        }, 300);
                    });
                }, 1000);
            });
        </script>
    </div>

    <!-- Avatar Selection Modal -->
    <div id="avatar-modal"
        class="fixed inset-0 bg-black bg-opacity-50 flex z-50 items-center justify-center hidden backdrop-blur-sm">
        <div class="bg-white p-8 rounded-2xl shadow-lg max-w-md w-full transform transition-all duration-300">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-text">Select Your Avatar</h2>
                <button onclick="closeAvatarModal()" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="grid grid-cols-3 sm:grid-cols-5 gap-4 justify-items-center">
                <!-- PHP Loop to generate avatar images -->
                <?php
                for ($i = 1; $i <= 15; $i++) {
                    $avatarPath = "assets/avatar{$i}.svg"; // Dynamic path to avatar
                
                    echo "
                        <div class='avatar-option cursor-pointer p-1 border-2 border-transparent rounded-full hover:border-primary' onclick='selectAvatar(\"{$avatarPath}\")'>
                            <img src='{$avatarPath}' class='w-16 h-16 rounded-full object-cover'>
                        </div>
                    ";
                }
                ?>
            </div>
            <div class="mt-6 flex justify-end">
                <button onclick="closeAvatarModal()"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg mr-2 transition-colors">
                    Cancel
                </button>
                <button id="save-avatar"
                    class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg transition-colors">
                    Save Selection
                </button>
            </div>
        </div>
    </div>

    <script>


        let selectedAvatarPath = '';

        function openAvatarModal() {
            document.getElementById('avatar-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAvatarModal() {
            document.getElementById('avatar-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function selectAvatar(path) {
            // Remove selection from all avatars
            document.querySelectorAll('.avatar-option').forEach(el => {
                el.classList.remove('border-primary');
                el.classList.add('border-transparent');
            });

            // Add selection to clicked avatar
            const clickedAvatar = Array.from(document.querySelectorAll('.avatar-option')).find(
                el => el.querySelector('img').src.includes(path.split('/').pop())
            );

            if (clickedAvatar) {
                clickedAvatar.classList.remove('border-transparent');
                clickedAvatar.classList.add('border-primary');
            }

            selectedAvatarPath = path;
        }

        document.getElementById('save-avatar').addEventListener('click', function () {
            if (selectedAvatarPath) {
                const storedUsername = localStorage.getItem("username");

                if (storedUsername) {
                    // Update avatar in localStorage
                    localStorage.setItem("selectedAvatar", selectedAvatarPath);

                    // Update avatar in database
                    fetch("components/update_avatar.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({ username: storedUsername, avatar: selectedAvatarPath })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update avatar on page
                                document.getElementById("profile-avatar").src = selectedAvatarPath;
                                closeAvatarModal();
                            } else {
                                console.error("Failed to update avatar:", data.message);
                            }
                        })
                        .catch(error => console.error("Error:", error));
                }
            }
        });

        document.addEventListener("DOMContentLoaded", function () {
            let storedUsername = localStorage.getItem("username");
            let avatarPath = localStorage.getItem("selectedAvatar");

            if (storedUsername) {
                fetch(`components/get_user.php?username=${encodeURIComponent(storedUsername)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.error) {
                            // Set the username and profile image on the profile page
                            document.getElementById("profile-name").textContent = data.username;
                            document.getElementById("profile-avatar").src = data.profile;
                            document.getElementById("user-email").textContent = data.email;
                        } else {
                            console.error(data.error);  // Handle error if user is not found
                        }
                    })
                    .catch(error => console.error("Error fetching user data:", error));
            }

            if (storedUsername && avatarPath) {
                fetch("components/update_avatar.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ username: storedUsername, avatar: avatarPath })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log("Avatar updated successfully!");
                        } else {
                            console.error("Failed to update avatar:", data.message);
                        }
                    })
                    .catch(error => console.error("Error:", error));
            }
        });

        function logout() {
            localStorage.removeItem("username");
            localStorage.removeItem("role");
            localStorage.removeItem("selectedAvatar");

            fetch("components/logout.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                }
            }).then(() => {
                window.location.href = "/";
            }).catch(error => console.error("Error:", error));
        }
    </script>
</body>

</html>