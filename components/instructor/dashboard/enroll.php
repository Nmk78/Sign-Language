<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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


// echo '<pre>';
// print_r($enrollment_stats);
// echo '</pre>';
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            transition: all 0.2s ease;
        }

        .status-badge:hover {
            transform: scale(1.05);
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.5);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
            }
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Header with Navigation -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <i class="fas fa-chalkboard-teacher text-blue-600 text-2xl mr-3"></i>
                    <h1 class="text-xl font-bold text-gray-900">Teacher Dashboard</h1>
                </div>
                <nav class="flex space-x-4">
                    <a href="dashboard.php" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                    <a href="courses.php" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">My Courses</a>
                    <a href="teacher-enrollments.php" class="text-blue-600 border-b-2 border-blue-600 px-3 py-2 rounded-md text-sm font-medium">Enrollments</a>
                    <a href="profile.php" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Profile</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div id="successAlert" class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded fade-in" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <p><?php echo $success_message; ?></p>
                </div>
                <button onclick="document.getElementById('successAlert').style.display='none'" class="absolute top-0 right-0 mt-4 mr-4 text-green-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div id="errorAlert" class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded fade-in" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <p><?php echo $error_message; ?></p>
                </div>
                <button onclick="document.getElementById('errorAlert').style.display='none'" class="absolute top-0 right-0 mt-4 mr-4 text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Enrollment Management</h2>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white shadow rounded-lg p-5 card-hover fade-in">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-book text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Courses</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $total_courses; ?></p>
                        </div>
                    </div>
                    <div class="mt-3 text-sm text-gray-500">
                        <span class="text-green-600 font-medium">
                            <i class="fas fa-arrow-up mr-1"></i>
                            <?php echo round(($total_courses > 0 ? $total_enrollments / $total_courses : 0), 1); ?>
                        </span>
                        enrollments per course
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-5 card-hover fade-in" style="animation-delay: 0.1s">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pending</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $total_pending; ?></p>
                        </div>
                    </div>
                    <div class="mt-3 text-sm text-gray-500">
                        <span class="<?php echo $total_pending > 0 ? 'text-yellow-600' : 'text-gray-500'; ?> font-medium">
                            <?php echo $total_pending > 0 ? '<i class="fas fa-exclamation-circle mr-1"></i> Needs attention' : 'No pending requests'; ?>
                        </span>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-5 card-hover fade-in" style="animation-delay: 0.2s">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Approved</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $total_approved; ?></p>
                        </div>
                    </div>
                    <div class="mt-3 text-sm text-gray-500">
                        <span class="text-green-600 font-medium">
                            <?php
                            $approval_rate = $total_enrollments > 0 ? round(($total_approved / $total_enrollments) * 100) : 0;
                            echo $approval_rate . '%';
                            ?>
                        </span>
                        approval rate
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-5 card-hover fade-in" style="animation-delay: 0.3s">
                    <div class="flex items-center">
                        <div class="bg-red-100 p-3 rounded-full">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Rejected</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $total_rejected; ?></p>
                        </div>
                    </div>
                    <div class="mt-3 text-sm text-gray-500">
                        <span class="text-red-600 font-medium">
                            <?php
                            $rejection_rate = $total_enrollments > 0 ? round(($total_rejected / $total_enrollments) * 100) : 0;
                            echo $rejection_rate . '%';
                            ?>
                        </span>
                        rejection rate
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Enrollment Stats -->
        <div class="mb-8 fade-in" style="animation-delay: 0.4s">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Enrollment Statistics</h3>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rejected</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($enrollment_stats)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No enrollment statistics available.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($enrollment_stats as $stat): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($stat['course_title']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo $stat['total_enrollments']; ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if ($stat['pending_count'] > 0): ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    <?php echo $stat['pending_count']; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-sm text-gray-500">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <?php echo $stat['approved_count']; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                <?php echo $stat['rejected_count']; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <a href="course-details.php?id=<?php echo $stat['course_id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pending Enrollments Section -->
        <div class="mb-8 fade-in" style="animation-delay: 0.5s">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Pending Enrollment Requests</h3>

                <?php if (!empty($pending_enrollments)): ?>
                    <div class="flex items-center">
                        <span class="relative flex h-3 w-3 mr-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                        </span>
                        <span class="text-sm text-gray-600"><?php echo count($pending_enrollments); ?> pending requests</span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($pending_enrollments)): ?>
                <div class="bg-white shadow rounded-lg mb-6 p-4">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="enrollmentSearch" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Search by student name or email...">
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <select id="courseFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Courses</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button id="refreshBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="pendingEnrollmentsContainer" class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="pendingEnrollmentsList">
                                <?php foreach ($pending_enrollments as $enrollment): ?>
                                    <tr class="enrollment-row hover:bg-gray-50 transition-colors" data-course-id="<?php echo $enrollment['course_id']; ?>" data-student-name="<?php echo strtolower(htmlspecialchars($enrollment['username'])); ?>" data-student-email="<?php echo strtolower(htmlspecialchars($enrollment['email'])); ?>">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <?php if (!empty($enrollment['profile'])): ?>
                                                        <img class="h-10 w-10 rounded-full object-cover" src="<?php echo htmlspecialchars($enrollment['profile']); ?>" alt="Profile">
                                                    <?php else: ?>
                                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                            <span class="text-blue-600 font-semibold"><?php echo strtoupper(substr($enrollment['username'], 0, 1)); ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($enrollment['username']); ?></div>
                                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($enrollment['email']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($enrollment['course_title']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500"><?php echo formatTimeAgo($enrollment['enrolled_at']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex justify-center items-center space-x-2">
                                                <form method="POST" class="inline-block">
                                                    <input type="hidden" name="enrollment_id" value="<?php echo $enrollment['enrollment_id']; ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="bg-green-100 mt-3 hover:bg-green-200 text-green-800 px-3 py-1 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-green-500">
                                                        <i class="fas fa-check mr-1"></i> Approve
                                                    </button>
                                                </form>
                                                <form method="POST" class="inline-block">
                                                    <input type="hidden" name="enrollment_id" value="<?php echo $enrollment['enrollment_id']; ?>">
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="bg-red-100 mt-3 hover:bg-red-200 text-red-800 px-3 py-1 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                                                        <i class="fas fa-times mr-1"></i> Reject
                                                    </button>
                                                </form>
                                                <button class="view-profile-btn bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500" data-user-id="<?php echo $enrollment['user_id']; ?>">
                                                    <i class="fas fa-user mr-1 "></i> Profile
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-white shadow rounded-lg p-6 text-center">
                    <div class="flex flex-col items-center">
                        <div class="bg-blue-100 p-4 rounded-full mb-4">
                            <i class="fas fa-check-circle text-blue-600 text-3xl"></i>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">All Caught Up!</h4>
                        <p class="text-gray-600 mb-4">You have no pending enrollment requests at this time.</p>
                        <a href="courses.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-plus mr-2"></i> Create New Course
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent Activity Section -->
        <div class="fade-in" style="animation-delay: 0.6s">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Enrollment Activity</h3>

            <?php if (!empty($recent_activity)): ?>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($recent_activity as $activity): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <?php if (!empty($activity['profile'])): ?>
                                                        <img class="h-8 w-8 rounded-full object-cover" src="<?php echo htmlspecialchars($activity['profile']); ?>" alt="Profile">
                                                    <?php else: ?>
                                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                            <span class="text-blue-600 font-semibold text-xs"><?php echo strtoupper(substr($activity['username'], 0, 1)); ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($activity['username']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($activity['course_title']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if ($activity['status'] === 'approved'): ?>
                                                <span class="status-badge px-2 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i> Approved
                                                </span>
                                            <?php else: ?>
                                                <span class="status-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    <i class="fas fa-times-circle mr-1"></i> Rejected
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo formatTimeAgo($activity['enrolled_at']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-white shadow rounded-lg p-6 text-center">
                    <p class="text-gray-500">No recent activity to display.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Student Profile Modal -->
    <div id="profileModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg max-w-md w-full mx-4 overflow-hidden">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 class="text-lg font-medium text-gray-900">Student Profile</h3>
                <button id="closeProfileModal" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="profileContent" class="p-6">
                <div class="flex justify-center mb-4">
                    <div class="animate-pulse flex space-x-4">
                        <div class="rounded-full bg-gray-200 h-12 w-12"></div>
                        <div class="flex-1 space-y-4 py-1">
                            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                            <div class="space-y-2">
                                <div class="h-4 bg-gray-200 rounded"></div>
                                <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-center text-gray-500">Loading student information...</p>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Search functionality
            $("#enrollmentSearch").on("keyup", function() {
                const value = $(this).val().toLowerCase();
                $(".enrollment-row").filter(function() {
                    const studentName = $(this).data("student-name");
                    const studentEmail = $(this).data("student-email");
                    const matchesSearch = studentName.includes(value) || studentEmail.includes(value);
                    const courseId = $("#courseFilter").val();
                    const matchesCourse = courseId === "" || $(this).data("course-id") == courseId;
                    $(this).toggle(matchesSearch && matchesCourse);
                });
                updateEmptyState();
            });

            // Course filter
            $("#courseFilter").on("change", function() {
                const courseId = $(this).val();
                const searchValue = $("#enrollmentSearch").val().toLowerCase();

                $(".enrollment-row").filter(function() {
                    const studentName = $(this).data("student-name");
                    const studentEmail = $(this).data("student-email");
                    const matchesSearch = studentName.includes(searchValue) || studentEmail.includes(searchValue);
                    const matchesCourse = courseId === "" || $(this).data("course-id") == courseId;
                    $(this).toggle(matchesSearch && matchesCourse);
                });
                updateEmptyState();
            });

            // Refresh button
            $("#refreshBtn").on("click", function() {
                $(this).addClass("animate-spin");
                setTimeout(() => {
                    location.reload();
                }, 500);
            });

            // View profile button
            $(".view-profile-btn").on("click", function() {
                const userId = $(this).data("user-id");
                $("#profileModal").removeClass("hidden");

                // Fetch user data from database
                $.ajax({
                    url: 'handlers/getUserProfile.php',
                    type: 'GET',
                    data: {
                        user_id: userId
                    },
                    dataType: 'json',
                    success: function(data) {
                        // If AJAX call is successful, display the user data
                        let profileImage = '';

                        if (data.profile && data.profile !== '') {
                            profileImage = `<img src="${data.profile}" alt="${data.username}" class="h-24 w-24 rounded-full object-cover">`;
                        } else {
                            profileImage = `
                                <div class="h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-blue-600 font-bold text-2xl">${data.username.charAt(0).toUpperCase()}</span>
                                </div>
                            `;
                        }

                        $("#profileContent").html(`
                            <div class="flex flex-col items-center mb-6">
                                ${profileImage}
                                <h4 class="text-xl font-medium text-gray-900 mt-4">${data.username}</h4>
                                <p class="text-gray-500">${data.email}</p>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500 mb-1">Role</h5>
                                    <p class="text-gray-900">${data.role}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500 mb-1">Joined</h5>
                                    <p class="text-gray-900">${new Date(data.created_at).toLocaleDateString()}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500 mb-1">Enrolled Courses</h5>
                                    <p class="text-gray-900">${data.enrolled_courses} courses</p>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <a href="student-details.php?id=${data.id}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    View Full Profile
                                </a>
                            </div>
                        `);
                    },
                    error: function() {
                        // If AJAX call fails, display a fallback
                        $("#profileContent").html(`
                            <div class="flex flex-col items-center mb-6">
                                <div class="h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center mb-4">
                                    <span class="text-blue-600 font-bold text-2xl">U</span>
                                </div>
                                <h4 class="text-xl font-medium text-gray-900">User #${userId}</h4>
                                <p class="text-gray-500">student${userId}@example.com</p>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500 mb-1">Enrolled Courses</h5>
                                    <p class="text-gray-900">3 courses</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500 mb-1">Joined</h5>
                                    <p class="text-gray-900">January 15, 2023</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500 mb-1">Last Active</h5>
                                    <p class="text-gray-900">2 days ago</p>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <a href="student-details.php?id=${userId}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    View Full Profile
                                </a>
                            </div>
                        `);
                    }
                });
            });

            // Close profile modal
            $("#closeProfileModal").on("click", function() {
                $("#profileModal").addClass("hidden");
                // Reset content to loading state for next time
                $("#profileContent").html(`
                    <div class="flex justify-center mb-4">
                        <div class="animate-pulse flex space-x-4">
                            <div class="rounded-full bg-gray-200 h-12 w-12"></div>
                            <div class="flex-1 space-y-4 py-1">
                                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                <div class="space-y-2">
                                    <div class="h-4 bg-gray-200 rounded"></div>
                                    <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-center text-gray-500">Loading student information...</p>
                `);
            });

            // Close modal when clicking outside
            $("#profileModal").on("click", function(e) {
                if (e.target === this) {
                    $(this).addClass("hidden");
                }
            });

            // Function to update empty state when filtering
            function updateEmptyState() {
                const visibleRows = $(".enrollment-row:visible").length;
                if (visibleRows === 0) {
                    if ($("#pendingEnrollmentsList").find(".no-results").length === 0) {
                        $("#pendingEnrollmentsList").append(`
                            <tr class="no-results">
                                <td colspan="4" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-search text-gray-400 text-2xl mb-2"></i>
                                        <p class="text-gray-500 mb-1">No matching enrollment requests found</p>
                                        <p class="text-sm text-gray-400">Try adjusting your search or filter</p>
                                    </div>
                                </td>
                            </tr>
                        `);
                    }
                } else {
                    $("#pendingEnrollmentsList").find(".no-results").remove();
                }
            }

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $("#successAlert, #errorAlert").fadeOut(500);
            }, 5000);
        });
    </script>
</body>

</html>