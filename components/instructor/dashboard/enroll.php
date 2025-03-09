<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user']['user_id']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'teacher') {
    // Redirect to login page
    header("Location: login.php?redirect=teacher-enrollments.php");
    exit();
}

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

$teacher_id = $_SESSION['user']['user_id'];

// Handle enrollment status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['enrollment_id'])) {
    $enrollment_id = intval($_POST['enrollment_id']);
    $action = $_POST['action'];
    
    if ($action === 'approve' || $action === 'reject') {
        $status = ($action === 'approve') ? 'approved' : 'rejected';
        
        // Verify this enrollment belongs to a course created by this teacher
        $verify_stmt = $conn->prepare("
            SELECT ce.id 
            FROM course_enrollments ce
            JOIN courses c ON ce.course_id = c.id
            WHERE ce.id = ? AND c.created_by = ?
        ");
        $verify_stmt->bind_param("ii", $enrollment_id, $teacher_id);
        $verify_stmt->execute();
        $verify_result = $verify_stmt->get_result();
        
        if ($verify_result->num_rows > 0) {
            // Update the enrollment status
            $update_stmt = $conn->prepare("UPDATE course_enrollments SET status = ? WHERE id = ?");
            $update_stmt->bind_param("si", $status, $enrollment_id);
            
            if ($update_stmt->execute()) {
                $success_message = "Enrollment successfully " . $action . "d.";
            } else {
                $error_message = "Error updating enrollment: " . $conn->error;
            }
            $update_stmt->close();
        } else {
            $error_message = "You don't have permission to update this enrollment.";
        }
        $verify_stmt->close();
    }
}

// Get all courses created by this teacher
$courses_stmt = $conn->prepare("
    SELECT id, title, description, category, created_at, status, 
           (SELECT COUNT(*) FROM course_enrollments WHERE course_id = courses.id) as enrollment_count
    FROM courses 
    WHERE created_by = ?
    ORDER BY created_at DESC
");
$courses_stmt->bind_param("i", $teacher_id);
$courses_stmt->execute();
$courses_result = $courses_stmt->get_result();
$courses = $courses_result->fetch_all(MYSQLI_ASSOC);
$courses_stmt->close();

// Get enrollment statistics for all teacher's courses
$stats_stmt = $conn->prepare("
    SELECT 
        c.id as course_id,
        c.title as course_title,
        COUNT(ce.id) as total_enrollments,
        SUM(CASE WHEN ce.status = 'pending' THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN ce.status = 'approved' THEN 1 ELSE 0 END) as approved_count,
        SUM(CASE WHEN ce.status = 'rejected' THEN 1 ELSE 0 END) as rejected_count
    FROM courses c
    LEFT JOIN course_enrollments ce ON c.id = ce.course_id
    WHERE c.created_by = ?
    GROUP BY c.id
    ORDER BY total_enrollments DESC
");
$stats_stmt->bind_param("i", $teacher_id);
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result();
$enrollment_stats = $stats_result->fetch_all(MYSQLI_ASSOC);
$stats_stmt->close();

// Get pending enrollments for all courses created by this teacher
$enrollments_stmt = $conn->prepare("
    SELECT 
        ce.id as enrollment_id,
        ce.user_id,
        ce.course_id,
        ce.enrolled_at,
        ce.status,
        u.username,
        u.email,
        u.profile,
        c.title as course_title
    FROM course_enrollments ce
    JOIN users u ON ce.user_id = u.id
    JOIN courses c ON ce.course_id = c.id
    WHERE c.created_by = ? AND ce.status = 'pending'
    ORDER BY ce.enrolled_at DESC
");
$enrollments_stmt->bind_param("i", $teacher_id);
$enrollments_stmt->execute();
$enrollments_result = $enrollments_stmt->get_result();
$pending_enrollments = $enrollments_result->fetch_all(MYSQLI_ASSOC);
$enrollments_stmt->close();

// Get recent enrollment activity (approved/rejected) for all courses created by this teacher
$activity_stmt = $conn->prepare("
    SELECT 
        ce.id as enrollment_id,
        ce.user_id,
        ce.course_id,
        ce.enrolled_at,
        ce.status,
        u.username,
        u.email,
        u.profile,
        c.title as course_title
    FROM course_enrollments ce
    JOIN users u ON ce.user_id = u.id
    JOIN courses c ON ce.course_id = c.id
    WHERE c.created_by = ? AND ce.status IN ('approved', 'rejected')
    ORDER BY ce.enrolled_at DESC
    LIMIT 10
");
$activity_stmt->bind_param("i", $teacher_id);
$activity_stmt->execute();
$activity_result = $activity_stmt->get_result();
$recent_activity = $activity_result->fetch_all(MYSQLI_ASSOC);
$activity_stmt->close();

// Close the database connection
$conn->close();

// Helper function to format dates
function formatTimeAgo($datetime) {
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

// Get total counts for dashboard summary
$total_courses = count($courses);
$total_pending = 0;
$total_approved = 0;
$total_rejected = 0;

foreach ($enrollment_stats as $stat) {
    $total_pending += $stat['pending_count'];
    $total_approved += $stat['approved_count'];
    $total_rejected += $stat['rejected_count'];
}

$total_enrollments = $total_pending + $total_approved + $total_rejected;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Enrollment Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
<body class="bg-gray-50 min-h-screen">

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Title and Summary Cards -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Enrollment Management</h2>
            
            <!-- Summary Cards -->

        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded fade-in" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500"></i>
                </div>
                <div class="ml-3">
                    <p class="font-medium"><?php echo $success_message; ?></p>
                </div>
                <button class="ml-auto" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times text-green-500"></i>
                </button>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded fade-in" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="font-medium"><?php echo $error_message; ?></p>
                </div>
                <button class="ml-auto" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times text-red-500"></i>
                </button>
            </div>
        </div>
        <?php endif; ?>

        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Enrollment Stats and Activity -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Enrollment Statistics -->
                <div class="bg-white shadow rounded-lg overflow-hidden fade-in" style="animation-delay: 0.5s">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Enrollment Statistics</h3>
                        <span class="text-xs text-gray-500">By Course</span>
                    </div>
                    
                    <div class="p-6 max-h-96 overflow-y-auto scrollbar-thin">
                        <?php if (empty($enrollment_stats)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-chart-bar text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">No enrollment data available.</p>
                        </div>
                        <?php else: ?>
                            <?php foreach ($enrollment_stats as $stat): ?>
                            <div class="mb-5 pb-5 border-b border-gray-100 last:border-0 last:pb-0 last:mb-0">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-medium text-gray-800 truncate max-w-[200px]" title="<?php echo htmlspecialchars($stat['course_title']); ?>">
                                        <?php echo htmlspecialchars($stat['course_title']); ?>
                                    </h4>
                                    <span class="text-xs text-gray-500"><?php echo $stat['total_enrollments']; ?> total</span>
                                </div>
                                
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-3">
                                    <?php 
                                    $total = max(1, $stat['total_enrollments']); // Avoid division by zero
                                    $pending_width = ($stat['pending_count'] / $total) * 100;
                                    $approved_width = ($stat['approved_count'] / $total) * 100;
                                    $rejected_width = ($stat['rejected_count'] / $total) * 100;
                                    ?>
                                    <div class="flex h-2.5 rounded-full overflow-hidden">
                                        <div class="bg-yellow-400" style="width: <?php echo $pending_width; ?>%"></div>
                                        <div class="bg-green-500" style="width: <?php echo $approved_width; ?>%"></div>
                                        <div class="bg-red-500" style="width: <?php echo $rejected_width; ?>%"></div>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-3 gap-2 text-center">
                                    <div class="bg-yellow-50 p-2 rounded">
                                        <span class="block text-yellow-700 font-semibold"><?php echo $stat['pending_count']; ?></span>
                                        <span class="text-xs text-yellow-600">Pending</span>
                                    </div>
                                    <div class="bg-green-50 p-2 rounded">
                                        <span class="block text-green-700 font-semibold"><?php echo $stat['approved_count']; ?></span>
                                        <span class="text-xs text-green-600">Approved</span>
                                    </div>
                                    <div class="bg-red-50 p-2 rounded">
                                        <span class="block text-red-700 font-semibold"><?php echo $stat['rejected_count']; ?></span>
                                        <span class="text-xs text-red-600">Rejected</span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white shadow rounded-lg overflow-hidden fade-in" style="animation-delay: 0.6s">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                        <span class="text-xs text-gray-500">Last 10 actions</span>
                    </div>
                    
                    <div class="p-6 max-h-[400px] overflow-y-auto scrollbar-thin">
                        <?php if (empty($recent_activity)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-history text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">No recent activity.</p>
                        </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($recent_activity as $activity): ?>
                                <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                                    <div class="flex-shrink-0">
                                        <?php if ($activity['status'] === 'approved'): ?>
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-green-100">
                                            <i class="fas fa-check text-green-600"></i>
                                        </span>
                                        <?php else: ?>
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-red-100">
                                            <i class="fas fa-times text-red-600"></i>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($activity['username']); ?>
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <?php echo ucfirst($activity['status']); ?> for 
                                            <span class="font-medium"><?php echo htmlspecialchars($activity['course_title']); ?></span>
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            <?php echo formatTimeAgo($activity['enrolled_at']); ?>
                                        </p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Pending Enrollments and Courses -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Pending Enrollment Requests -->
                <div class="bg-white shadow rounded-lg overflow-hidden fade-in" style="animation-delay: 0.7s">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Pending Enrollment Requests</h3>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                            <?php echo count($pending_enrollments); ?> pending
                        </span>
                    </div>
                    
                    <?php if (empty($pending_enrollments)): ?>
                    <div class="p-6 text-center">
                        <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                            <i class="fas fa-check text-green-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">All caught up!</h3>
                        <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
                            You've processed all enrollment requests. New requests will appear here when students enroll in your courses.
                        </p>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Student
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Course
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Requested
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($pending_enrollments as $index => $enrollment): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <?php if (!empty($enrollment['profile']) && $enrollment['profile'] !== 'assets/avatar13.svg'): ?>
                                                <img class="h-10 w-10 rounded-full object-cover" src="<?php echo htmlspecialchars($enrollment['profile']); ?>" alt="Profile">
                                                <?php else: ?>
                                                <div class="h-10 w-10 rounded-full bg-[#4A90E2] flex items-center justify-center text-white font-semibold">
                                                    <?php echo strtoupper(substr($enrollment['username'], 0, 1)); ?>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($enrollment['username']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($enrollment['email']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium"><?php echo htmlspecialchars($enrollment['course_title']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500"><?php echo formatTimeAgo($enrollment['enrolled_at']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <form method="post" action="" class="inline-block">
                                                <input type="hidden" name="enrollment_id" value="<?php echo $enrollment['enrollment_id']; ?>">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="bg-green-100 text-green-700 hover:bg-green-200 px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                                                    <i class="fas fa-check mr-1"></i> Approve
                                                </button>
                                            </form>
                                            <form method="post" action="" class="inline-block">
                                                <input type="hidden" name="enrollment_id" value="<?php echo $enrollment['enrollment_id']; ?>">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                                                    <i class="fas fa-times mr-1"></i> Reject
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Course List -->
                <div class="bg-white shadow rounded-lg overflow-hidden fade-in" style="animation-delay: 0.8s">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Your Courses</h3>
                        <a href="create-course.php" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-[#4A90E2] hover:bg-[#357abd] transition-colors">
                            <i class="fas fa-plus mr-1"></i> New Course
                        </a>
                    </div>
                    
                    <?php if (empty($courses)): ?>
                    <div class="p-6 text-center">
                        <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-4">
                            <i class="fas fa-book text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">No courses yet</h3>
                        <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
                            You haven't created any courses yet. Click the "New Course" button above to get started.
                        </p>
                        <div class="mt-6">
                            <a href="create-course.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-[#4A90E2] hover:bg-[#357abd] transition-colors">
                                Create Your First Course
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Course
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Enrollments
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($courses as $course): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($course['title']); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo formatTimeAgo($course['created_at']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-medium rounded-full bg-gray-100 text-gray-800">
                                            <?php echo htmlspecialchars($course['category'] ?? 'Uncategorized'); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($course['status'] === 'published'): ?>
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-medium rounded-full bg-green-100 text-green-800">
                                            Published
                                        </span>
                                        <?php elseif ($course['status'] === 'draft'): ?>
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-medium rounded-full bg-gray-100 text-gray-800">
                                            Draft
                                        </span>
                                        <?php else: ?>
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-medium rounded-full bg-red-100 text-red-800">
                                            Archived
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-900 mr-2"><?php echo $course['enrollment_count']; ?></span>
                                            <div class="flex -space-x-1">
                                                <?php for ($i = 0; $i < min(3, $course['enrollment_count']); $i++): ?>
                                                <div class="h-6 w-6 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-xs text-gray-600">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <?php endfor; ?>
                                                <?php if ($course['enrollment_count'] > 3): ?>
                                                <div class="h-6 w-6 rounded-full bg-gray-100 border-2 border-white flex items-center justify-center text-xs text-gray-600">
                                                    +<?php echo $course['enrollment_count'] - 3; ?>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="course-enrollments.php?course_id=<?php echo $course['id']; ?>" class="text-[#4A90E2] hover:text-[#357abd] bg-blue-50 px-3 py-1.5 rounded-md transition-colors">
                                                <i class="fas fa-users mr-1"></i> Enrollments
                                            </a>
                                            <a href="edit-course.php?id=<?php echo $course['id']; ?>" class="text-gray-700 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-md transition-colors">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-8">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-gray-500">
                &copy; <?php echo date('Y'); ?> Sign Language Learning Platform. All rights reserved.
            </p>
        </div>
    </footer>

    <script>
    // Toggle notifications panel
    document.getElementById('notifications-button')?.addEventListener('click', function() {
        // In a real application, you would toggle a notifications dropdown here
        alert('Notifications clicked - would show pending enrollments in a real app');
    });

    // Toggle user menu
    document.getElementById('user-menu-button')?.addEventListener('click', function() {
        // In a real application, you would toggle a dropdown menu here
        alert('User menu clicked - would show profile options in a real app');
    });

    // Add fade-in animation to table rows
    document.addEventListener('DOMContentLoaded', function() {
        const tables = document.querySelectorAll('table');
        tables.forEach(table => {
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                row.classList.add('fade-in');
                row.style.animationDelay = (0.1 * index) + 's';
            });
        });
    });
    </script>
</body>
</html>

