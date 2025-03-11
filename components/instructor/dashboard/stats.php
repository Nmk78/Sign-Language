<?php
// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "root", "sign_language");

if ($conn->connect_error) {
    $db_error = "Database connection failed: " . $conn->connect_error;
}

// TODO  i call the user_id for localstorage and use for my classes and recent activity
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

// Get teacher data
$teacher_id = $_SESSION['user_id'];
// echo $teacher_id; 

// Fetch classes
$classes = [];
if (!isset($db_error)) {
    $stmt = $conn->prepare("SELECT id, title, status, 
                          (SELECT COUNT(*) FROM course_enrollments WHERE course_id = courses.id) as student_count 
                          FROM courses WHERE created_by = ? ORDER BY created_at DESC LIMIT 5");
    if ($stmt) {
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $classes = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

// Fetch assignments
// $assignments = [];
// if (!isset($db_error)) {
//     $stmt = $conn->prepare("SELECT id, title, due_date, course_id FROM assignments 
//                           WHERE teacher_id = ? AND due_date >= CURDATE() 
//                           ORDER BY due_date ASC LIMIT 5");
//     if ($stmt) {
//         $stmt->bind_param("i", $teacher_id);
//         $stmt->execute();
//         $result = $stmt->get_result();
//         $assignments = $result->fetch_all(MYSQLI_ASSOC);
//         $stmt->close();
//     }
// }

$performances = [
    [
        'id' => 1,
        'title' => 'Introduction to Sign Language',
        'average_score' => 85,
        'min_score' => 70,
        'max_score' => 95,
        'student_count' => 30
    ],
    [
        'id' => 2,
        'title' => 'Advanced Sign Language',
        'average_score' => 90,
        'min_score' => 80,
        'max_score' => 100,
        'student_count' => 25
    ],
    [
        'id' => 3,
        'title' => 'Sign Language for Beginners',
        'average_score' => 75,
        'min_score' => 60,
        'max_score' => 85,
        'student_count' => 40
    ],
    [
        'id' => 4,
        'title' => 'Conversational Sign Language',
        'average_score' => 88,
        'min_score' => 75,
        'max_score' => 95,
        'student_count' => 20
    ],
    [
        'id' => 5,
        'title' => 'Sign Language Interpretation',
        'average_score' => 92,
        'min_score' => 85,
        'max_score' => 98,
        'student_count' => 15
    ]
];
if (!isset($db_error)) {
    $stmt = $conn->prepare("SELECT 
                            c.id, c.title, 
                            AVG(g.percentage_completion) AS average_score, 
                            MIN(g.percentage_completion) AS min_score, 
                            MAX(g.percentage_completion) AS max_score, 
                            COUNT(DISTINCT e.user_id) AS student_count
                        FROM courses c
                        JOIN course_enrollments e ON c.id = e.course_id
                        LEFT JOIN progress g ON e.user_id = g.learner_id AND c.id = g.course_id
                        WHERE c.created_by = ?
                        GROUP BY c.id
                        ORDER BY average_score DESC
                        LIMIT 5");
    if ($stmt) {
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $performances = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

// Fetch recent activities
$activities = [];
if (!isset($db_error)) {
    $stmt = $conn->prepare("SELECT users.username, logs.action, logs.details, logs.timestamp 
                            FROM logs 
                            JOIN users ON logs.user_id = users.id 
                            ORDER BY logs.timestamp DESC 
                            LIMIT 8");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $activities = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

// Get statistics
$stats = [
    'total_students' => 0,
    'total_courses' => 0,
    'avg_performance' => 0,
    'total_lessons' => 0
];

if (!isset($db_error)) {
    // Total students
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT user_id) as count FROM course_enrollments 
                            JOIN courses ON course_enrollments.course_id = courses.id 
                            WHERE courses.created_by = ?");
    if ($stmt) {
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['total_students'] = $row['count'];
        }
        $stmt->close();
    }
    
    // Total courses
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM courses WHERE created_by = ?");
    if ($stmt) {
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['total_courses'] = $row['count'];
        }
        $stmt->close();
    }
    
    // Average performance
    $stmt = $conn->prepare("SELECT AVG(g.percentage_completion) as avg_score
                            FROM progress g
                            JOIN courses c ON g.course_id = c.id
                            WHERE c.created_by = ?");
    if ($stmt) {
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['avg_performance'] = round($row['avg_score'] ?? 0);
        }
        $stmt->close();
    }
    
    // Active assignments
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM lesson WHERE created_by = ?");
    if ($stmt) {
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['total_lessons'] = $row['count'];
        }
        $stmt->close();
    }
    
}

// Monthly performance data for chart
$monthly_data = [
    ['month' => 'Jan', 'year_month' => '2023-01', 'average' => 75],
    ['month' => 'Feb', 'year_month' => '2023-02', 'average' => 80],
    ['month' => 'Mar', 'year_month' => '2023-03', 'average' => 85],
    ['month' => 'Apr', 'year_month' => '2023-04', 'average' => 90],
    ['month' => 'May', 'year_month' => '2023-05', 'average' => 95],
    ['month' => 'Jun', 'year_month' => '2023-06', 'average' => 88],
];
// if (!isset($db_error)) {
//     $stmt = $conn->prepare("SELECT 
//                             DATE_FORMAT(g.created_at, '%b') as month,
//                             DATE_FORMAT(g.created_at, '%Y-%m') as year_month,
//                             AVG(g.percentage_completion) as average
//                             FROM progress g
//                             JOIN courses c ON g.course_id = c.id
//                             WHERE c.created_by = ? 
//                             AND g.created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
//                             GROUP BY year_month, month
//                             ORDER BY year_month ASC");
//     if ($stmt) {
//         $stmt->bind_param("i", $teacher_id);
//         $stmt->execute();
//         $result = $stmt->get_result();
//         $monthly_data = $result->fetch_all(MYSQLI_ASSOC);
//         $stmt->close();
//     }
// }

// Format data for chart
$chart_months = [];
$chart_averages = [];
foreach ($monthly_data as $data) {
    $chart_months[] = $data['month'];
    $chart_averages[] = $data['average'];
}


// Close connection
if (isset($conn)) {
    $conn->close();
}

// Helper function to format dates
function formatDate($date) {
    return date("M j, Y", strtotime($date));
}

// Helper function to format time ago
function timeAgo($datetime) {
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
    <title>Teacher Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }
        .scrollbar-thin::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 2px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Title -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Dashboard Overview</h2>
        </div>

        <?php if (isset($db_error)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="font-medium"><?php echo $db_error; ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Statistics Panel -->
        <div class="bg-white shadow rounded-lg overflow-hidden mb-6 fade-in" style="animation-delay: 0.1s">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Statistics</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Total Students Card -->
                    <div class="bg-gray-50 rounded-lg p-4 flex items-center space-x-4">
                        <div class="rounded-full bg-blue-100 p-3 flex-shrink-0">
                            <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Total Students</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_students']; ?></p>
                        </div>
                    </div>
                    
                    <!-- Total Courses Card -->
                    <div class="bg-gray-50 rounded-lg p-4 flex items-center space-x-4">
                        <div class="rounded-full bg-green-100 p-3 flex-shrink-0">
                            <i class="fas fa-book text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Total Courses</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_courses']; ?></p>
                        </div>
                    </div>
                    
                    <!-- Average Performance Card -->
                    <div class="bg-gray-50 rounded-lg p-4 flex items-center space-x-4">
                        <div class="rounded-full bg-yellow-100 p-3 flex-shrink-0">
                            <i class="fas fa-chart-line text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Avg. Performance</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['avg_performance']; ?>%</p>
                        </div>
                    </div>
                    
                    <!-- Active Assignments Card -->
                    <div class="bg-gray-50 rounded-lg p-4 flex items-center space-x-4">
                        <div class="rounded-full bg-purple-100 p-3 flex-shrink-0">
                            <i class="fas fa-tasks text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Total lessons</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_lessons']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Performance Trend Chart -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-base font-medium text-gray-800 mb-4">Performance Trend</h4>
                        <div class="h-64">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Class Performance Distribution -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-base font-medium text-gray-800 mb-4">Class Performance</h4>
                        <div class="space-y-4">
                            <?php if (empty($performances)): ?>
                            <p class="text-gray-500 text-center py-8">No performance data available</p>
                            <?php else: ?>
                                <?php foreach ($performances as $performance): ?>
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="font-medium text-gray-800 truncate max-w-[250px]" title="<?php echo htmlspecialchars($performance['title']); ?>">
                                            <?php echo htmlspecialchars($performance['title']); ?>
                                        </span>
                                        <span class="text-xs text-gray-500"><?php echo round($performance['average_score']); ?>% avg</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                                        <div class="bg-green-500 rounded-full h-2.5" style="width: <?php echo round($performance['average_score']); ?>%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-600">
                                        <span>Min: <?php echo round($performance['min_score']); ?>%</span>
                                        <span><?php echo $performance['student_count']; ?> students</span>
                                        <span>Max: <?php echo round($performance['max_score']); ?>%</span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Grid -->
         <!-- TODO CHANGE grid col to 2  -->
         <?php
         $teacher_id = $_SESSION['user_id']; // Default for demo

// Database connection
$conn = mysqli_connect("localhost", "root", "root", "sign_language");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch classes (courses created by the user)
$classes_stmt = $conn->prepare("
    SELECT 
        c.id,
        c.title,
        c.status,
        (SELECT COUNT(DISTINCT cl.user_id) 
         FROM completed_lessons cl 
         JOIN lesson l ON cl.lesson_id = l.id 
         WHERE l.course_id = c.id) AS student_count
    FROM courses c
    WHERE c.created_by = ?  -- created_by is the user_id of the creator
    ORDER BY c.created_at DESC
");
if ($classes_stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$classes_stmt->bind_param("i", $teacher_id);
$classes_stmt->execute();
$classes_result = $classes_stmt->get_result();
$classes = $classes_result->fetch_all(MYSQLI_ASSOC);
$classes_stmt->close();

// Fetch recent activities (profile updates and completed lessons)
$activities_stmt = $conn->prepare("
   SELECT 
        'profile' AS activity_type,
        l.id AS activity_id,
        NULL AS lesson_id,
        l.timestamp AS activity_time,
        'Profile updated' AS details,
        u.username
    FROM logs l
    JOIN users u ON l.user_id = u.id
    WHERE l.user_id = ? 
    AND l.entity_type = 'profile' 
    AND l.action = 'update'
    
    UNION ALL
    
    SELECT 
        'lesson_completed' AS activity_type,
        cl.id AS activity_id,
        cl.lesson_id,
        cl.completed_at AS activity_time,
        CONCAT('Completed lesson \"', les.title, '\" (ID: ', cl.lesson_id, ') in \"', c.title, '\"') AS details,
        u.username
    FROM completed_lessons cl
    JOIN users u ON cl.user_id = u.id
    LEFT JOIN lesson les ON cl.lesson_id = les.id
    LEFT JOIN courses c ON les.course_id = c.id
    WHERE cl.user_id = ?
    ORDER BY activity_time DESC
    LIMIT 10
");
if ($activities_stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$activities_stmt->bind_param("ii", $teacher_id, $teacher_id);
$activities_stmt->execute();
$activities_result = $activities_stmt->get_result();
$activities = $activities_result->fetch_all(MYSQLI_ASSOC);
$activities_stmt->close();
?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- My Classes Card -->
    <div class="bg-white shadow rounded-lg overflow-hidden fade-in" style="animation-delay: 0.2s">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">My Classes</h3>
        </div>
        <div class="p-6 max-h-80 overflow-y-auto scrollbar-thin">
            <?php if (empty($classes)): ?>
            <p class="text-gray-500">You haven't created any classes yet.</p>
            <a href="#" class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-500 hover:bg-blue-600">
                Create Your First Class
            </a>
            <?php else: ?>
            <ul class="space-y-2">
                <?php foreach ($classes as $class): ?>
                <li class="bg-gray-50 p-3 rounded-md hover:bg-gray-100 transition-colors">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-800"><?php echo htmlspecialchars($class['title']); ?></span>
                        <span class="text-xs px-2 py-1 rounded-full <?php echo $class['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                            <?php echo ucfirst($class['status'] ?? 'Draft'); ?>
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1"><?php echo $class['student_count'] ?? 0; ?> students enrolled</div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white shadow rounded-lg overflow-hidden fade-in" style="animation-delay: 0.4s">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Activities</h3>
        </div>
        <div class="p-6 max-h-80 overflow-y-auto scrollbar-thin">
            <?php if (empty($activities)): ?>
            <p class="text-gray-500">No recent activities</p>
            <?php else: ?>
            <ul class="space-y-4">
                <?php foreach ($activities as $activity): ?>
                <li class="flex items-start justify-between border-b pb-2 last:border-b-0">
                    <div>
                        <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($activity['username']); ?> 
                            <span class="text-gray-600 text-sm">(<?php echo htmlspecialchars($activity['activity_type'] === 'profile' ? 'updated profile' : 'completed lesson'); ?>)</span>
                        </h3>
                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($activity['details']); ?></p>
                    </div>
                    <span class="text-xs text-gray-500"><?php echo date('F j, Y, g:i A', strtotime($activity['activity_time'])); ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$conn->close();
?>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-8">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-gray-500">
                &copy; <?php echo date('Y'); ?> Teacher Dashboard. All rights reserved.
            </p>
        </div>
    </footer>

    <script>
    // Initialize performance chart
    document.addEventListener('DOMContentLoaded', function() {
        // Chart data
        const months = <?php echo json_encode($chart_months) ?: '[]'; ?>;
        const averages = <?php echo json_encode($chart_averages) ?: '[]'; ?>;
        
        if (months.length > 0) {
            const ctx = document.getElementById('performanceChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Average Performance',
                        data: averages,
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y + '%';
                                }
                            }
                        }
                    }
                }
            });
        } else {
            // If no data, show a message
            const chartContainer = document.getElementById('performanceChart');
            chartContainer.style.display = 'flex';
            chartContainer.style.alignItems = 'center';
            chartContainer.style.justifyContent = 'center';
            chartContainer.innerHTML = '<p class="text-gray-500">No performance data available yet</p>';
        }
        
        // Add fade-in animation to elements
        const fadeElements = document.querySelectorAll('.fade-in');
        fadeElements.forEach((element, index) => {
            element.style.animationDelay = (0.1 * index) + 's';
        });
    });

    // Toggle notifications panel
    document.getElementById('notifications-button')?.addEventListener('click', function() {
        // In a real application, you would toggle a notifications dropdown here
        alert('Notifications clicked - would show notifications in a real app');
    });

    // Toggle user menu
    document.getElementById('user-menu-button')?.addEventListener('click', function() {
        // In a real application, you would toggle a dropdown menu here
        alert('User menu clicked - would show profile options in a real app');
    });
    </script>
</body>
</html>

