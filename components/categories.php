<?php
// Database connection
$host = 'localhost';
$dbname = 'sign_language';
$username = 'root'; // Consider moving to a config file or env vars
$password = 'root';

$mysqli = new mysqli($host, $username, $password, $dbname);
if ($mysqli->connect_error) {
    die("Could not connect to the database $dbname: " . $mysqli->connect_error);
}

// Pagination settings
$itemsPerPage = 6;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Category filter
$currentCategory = isset($_GET['category']) ? $mysqli->real_escape_string($_GET['category']) : '';

// Fetch total courses for pagination
$countQuery = "SELECT COUNT(*) FROM courses c WHERE ('$currentCategory' = '' OR c.category = '$currentCategory')";
$countResult = $mysqli->query($countQuery);
$totalCourses = $countResult->fetch_row()[0];
$totalPages = ceil($totalCourses / $itemsPerPage);

// Fetch courses with filtering and pagination
$query = "
    SELECT 
        c.id, c.title, c.description, c.category, c.thumbnail_url, c.price, c.status, c.created_at,
        u.id AS creator_id, u.username AS creator_name,
        COUNT(DISTINCT ce.user_id) AS enrolled_students
    FROM courses c
    LEFT JOIN users u ON c.created_by = u.id
    LEFT JOIN course_enrollments ce ON ce.course_id = c.id
    WHERE ('$currentCategory' = '' OR c.category = '$currentCategory')
    GROUP BY 
        c.id, c.title, c.description, c.category, c.thumbnail_url, c.price, c.status, c.created_at,
        u.id, u.username
    ORDER BY c.created_at DESC
    LIMIT $itemsPerPage OFFSET $offset
";
$result = $mysqli->query($query);

if (!$result) {
    die("Query failed: " . $mysqli->error);
}

// Fetch courses into an array
$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

// Fetch distinct categories
$categoryQuery = "SELECT DISTINCT category FROM courses WHERE category IS NOT NULL";
$categoryResult = $mysqli->query($categoryQuery);
$categories = [];
while ($row = $categoryResult->fetch_row()) {
    $categories[] = $row[0];
}

// Output results
if (empty($courses)) {
    echo "No courses found.";
} else {
    echo '<pre>';
    print_r($courses);
    echo '</pre>';
}

// Close the connection
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Catalog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#4A90E2',
                        'primary-dark': '#2A69A4',
                        'secondary': '#7ED321',
                        'accent': '#F5A623',
                        'success': '#10B981',
                        'warning': '#F1C40F',
                        'error': '#E74C3C',
                        'background': '#f8fafb',
                        'surface': '#FFFFFF',
                        'text': '#333333',
                        'text-light': '#7F8C8D',
                    },
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes saveAnimation {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        .save-animation {
            animation: saveAnimation 0.3s ease-in-out;
        }
    </style>
</head>

<body class="bg-background font-sans">
    <div class="max-w-7xl mx-auto pt-4">
        <!-- <header class="mb-8 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-text">Course Catalog</h1>
            <p class="text-text-light mt-2">Explore our wide range of courses</p>
        </header> -->

        <!-- Category Filter -->
        <div id="categoryFilter" class="mb-6 flex flex-wrap justify-center gap-2">
            <a href="?category=" class="px-4 py-2 border rounded <?php echo $currentCategory === '' ? 'bg-primary text-white' : 'bg-white text-primary'; ?>">
                All Categories
            </a>
            <?php foreach ($categories as $category): ?>
                <a href="?category=<?php echo urlencode($category); ?>" class="px-4 py-2 border rounded <?php echo $currentCategory === $category ? 'bg-primary text-white' : 'bg-white text-primary'; ?>">
                    <?php echo htmlspecialchars($category); ?>
                </a>
            <?php endforeach; ?>
        </div>



        <!-- Courses Grid -->
        <div id="coursesGrid" class="grid md:grid-cols-3 gap-8">
            <?php foreach ($courses as $course): ?>
                <a href="/courseDetails?course=<?php echo htmlspecialchars($course['id']); ?>"
                    class="bg-white rounded-xl flex flex-col overflow-hidden shadow-lg course-card transition-all duration-300">
                    <div class="relative">
                        <img src="<?php echo $course['thumbnail_url'] ? htmlspecialchars($course['thumbnail_url']) : 'https://via.placeholder.com/300x200.png?text=Course+Thumbnail'; ?>"
                            alt="<?php echo htmlspecialchars($course['title']); ?>"
                            class="w-full aspect-video object-cover">
                        <div class="absolute top-4 right-4 rounded-full text-sm font-medium text-primary">
                            <button onclick="toggleSave(<?php echo $course['id']; ?>)"
                                class="p-2 rounded-full hover:bg-gray-100 transition-colors focus:outline-none"
                                aria-label="Save course">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6 transition-colors duration-300 ease-in-out text-blue-400"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col">
                        <h3 class="font-semibold text-primary text-xl mb-2">
                            <?php echo isset($course['title']) ? htmlspecialchars($course['title']) : 'Unknown Title'; ?>
                        </h3>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center text-text-light text-sm mb-4">
                                <i class="fas fa-users mr-2"></i>
                                <span><?php echo isset($course['enrolled_students']) && is_numeric($course['enrolled_students'])
                                            ? number_format($course['enrolled_students'])
                                            : '0'; ?> students</span>
                            </div>
                            <button onclick="window.location.href='/'" class="cursor-pointer text-primary z-40 hover:underline">
                                By <?php echo isset($course['creator_name']) ? htmlspecialchars($course['creator_name']) : 'Unknown Creator'; ?>
                            </button>

                        </div>
                    </div>

                </a>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div id="pagination" class="mt-8 flex justify-center space-x-2 mb-8">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?><?php echo $currentCategory ? '&category=' . urlencode($currentCategory) : ''; ?>"
                    class="px-4 py-2 border rounded <?php echo $i == $currentPage ? 'bg-primary text-white' : 'bg-white text-primary'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>

    <script>
        function toggleSave(courseId) {
            const button = document.querySelector(`button[onclick="toggleSave(${courseId})"]`);
            const icon = button.querySelector('svg');

            // Toggle visual state
            icon.classList.toggle('text-primary');
            icon.classList.toggle('fill-current');
            icon.classList.add('save-animation');
            setTimeout(() => {
                icon.classList.remove('save-animation');
            }, 300);

            // Get saved courses from localStorage
            let savedCourses = JSON.parse(localStorage.getItem('savedCourses')) || [];

            // Check if course is already saved
            const courseIndex = savedCourses.indexOf(courseId);

            // Toggle save state in localStorage
            if (courseIndex === -1) {
                // Course not saved, add it
                savedCourses.push(courseId);
                console.log(`Course ${courseId} saved`);
            } else {
                // Course already saved, remove it
                savedCourses.splice(courseIndex, 1);
                console.log(`Course ${courseId} removed from saved`);
            }

            // Save updated list back to localStorage
            localStorage.setItem('savedCourses', JSON.stringify(savedCourses));
        }

        // Load saved courses from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const savedCourses = JSON.parse(localStorage.getItem('savedCourses')) || [];
            savedCourses.forEach(courseId => {
                const button = document.querySelector(`button[onclick="toggleSave(${courseId})"]`);
                if (button) {
                    const icon = button.querySelector('svg');
                    icon.classList.add('text-primary', 'fill-current');
                }
            });
        });
    </script>
</body>

</html>