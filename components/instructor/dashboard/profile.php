<?php
// Database connection
$host = 'localhost';
$dbname = 'sign_language';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get instructor ID from URL parameter
$instructor_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$instructor_id) {
    die("Instructor ID is required");
}

// Fetch instructor information
$stmt = $pdo->prepare("SELECT id, username, email, profile, created_at FROM users WHERE id = ? AND role = 'teacher'");
$stmt->execute([$instructor_id]);
$instructor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$instructor) {
    die("Instructor not found");
}

// Fetch instructor's courses
$stmt = $pdo->prepare("
    SELECT id, title, description, category, thumbnail_url, price, status, created_at 
    FROM courses 
    WHERE created_by = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$instructor_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total courses
$totalCourses = count($courses);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Profile - <?= htmlspecialchars($instructor['username']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        }
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .card-shadow {
                @apply shadow-md hover:shadow-lg transition-shadow duration-300;
            }
            .status-draft {
                @apply bg-gray-100 text-gray-600;
            }
            .status-published {
                @apply bg-green-100 text-green-800;
            }
            .status-archived {
                @apply bg-red-100 text-red-800;
            }
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 font-sans">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Profile Header -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="md:flex md:items-center">
                <div class="md:w-1/4 flex justify-center md:justify-start mb-4 md:mb-0">
                    <img src="<?= htmlspecialchars($instructor['profile']) ?>"
                        alt="<?= htmlspecialchars($instructor['username']) ?>"
                        class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
                </div>
                <div class=" flex space-x-5 justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold mb-2"><?= htmlspecialchars($instructor['username']) ?></h1>
                        <div class="space-y-2 text-gray-600">
                            <p class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-primary-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                                <span><?= htmlspecialchars($instructor['email']) ?></span>
                            </p>
                            <p class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-primary-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                                <span>Instructor since <?= date('F Y', strtotime($instructor['created_at'])) ?></span>
                            </p>
                        </div>
                    </div>
                    <div class="bg-white flex items-center flex-col rounded-xl border border-primary px-5 py-3 ml-auto ">
                        <p class="text-gray-500 font-medium">Total Courses</p>

                        <h3 class="text-3xl font-bold text-primary-600 mb-1"><?= $totalCourses ?></h3>
                    </div>
                </div>

            </div>
        </div>

        <!-- Courses Section -->
        <h2 class="text-2xl font-bold mb-6 pb-2 border-b-2 border-gray-200">Courses</h2>

        <?php if (empty($courses)): ?>
            <div class="bg-blue-50 text-blue-800 p-4 rounded-lg">
                This instructor hasn't created any courses yet.
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <?php foreach ($courses as $course): ?>
                    <div class="bg-white rounded-xl overflow-hidden card-shadow">
                        <div class="h-48 overflow-hidden">
                            <img src="<?= htmlspecialchars($course['thumbnail_url'] ?: 'path/to/default-thumbnail.jpg') ?>"
                                class="w-full h-full object-cover"
                                alt="<?= htmlspecialchars($course['title']) ?>">
                        </div>
                        <div class="p-5">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800 mb-3">
                                <?= htmlspecialchars($course['category']) ?>
                            </span>
                            <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($course['title']) ?></h3>
                            <p class="text-gray-600 mb-4 line-clamp-2">
                                <?= htmlspecialchars(substr($course['description'], 0, 100)) ?>
                                <?= strlen($course['description']) > 100 ? '...' : '' ?>
                            </p>
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-primary-700">
                                    <?= $course['price'] > 0 ? '$' . number_format($course['price'], 2) : 'Free' ?>
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-medium status-<?= $course['status'] ?>">
                                    <?= ucfirst(htmlspecialchars($course['status'])) ?>
                                </span>
                            </div>
                        </div>
                        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                            <div class="flex items-center text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <?= date('M d, Y', strtotime($course['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // JavaScript for course card hover effects
        document.addEventListener('DOMContentLoaded', function() {
            const courseCards = document.querySelectorAll('.card-shadow');

            courseCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.classList.add('transform', 'translate-y-[-5px]', 'transition-transform', 'duration-300');
                });

                card.addEventListener('mouseleave', function() {
                    this.classList.remove('transform', 'translate-y-[-5px]');
                });
            });
        });
    </script>
</body>

</html>