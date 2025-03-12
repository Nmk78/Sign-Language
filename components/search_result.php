<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "sign_language";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search query from URL parameter, sanitize it
$search_query = isset($_GET['query']) ? trim($_GET['query']) : '';

// Initialize results arrays
$courses = [];
$lessons = [];

if (!empty($search_query)) {
    // Search in courses
    $course_sql = "SELECT c.id, c.title, c.description, c.category, c.thumbnail_url, c.price, 
                          c.status, u.username AS creator_name
                   FROM courses c
                   LEFT JOIN users u ON c.created_by = u.id
                   WHERE (c.title LIKE ? OR c.description LIKE ?) 
                   ORDER BY c.created_at DESC
                   LIMIT 20";
    
    $stmt = $conn->prepare($course_sql);
    $search_param = "%" . $conn->real_escape_string($search_query) . "%"; // Sanitize input
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    // Search in lessons
    $lesson_sql = "SELECT l.id, l.title, l.description, l.category, l.video_url, 
                          c.id AS course_id, c.title AS course_title, c.thumbnail_url
                   FROM lesson l
                   INNER JOIN courses c ON l.course_id = c.id
                   WHERE (l.title LIKE ? OR l.description LIKE ?) 
                   ORDER BY l.created_at DESC
                   LIMIT 20";
    
    $stmt = $conn->prepare($lesson_sql);
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $lessons = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$total_results = count($courses) + count($lessons);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results for "<?php echo htmlspecialchars($search_query); ?>" | Silent Voice</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .line-clamp-1 { overflow: hidden; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; }
        .line-clamp-2 { overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container max-w-6xl mx-auto px-4 py-8">
        <?php if (!empty($search_query)): ?>
            <!-- Search results summary -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold mb-2">Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h1>
                <p class="text-gray-600"><?php echo $total_results; ?> result<?php echo $total_results !== 1 ? 's' : ''; ?> found</p>
            </div>

            <?php if ($total_results > 0): ?>
                <!-- Courses Section -->
                <?php if (!empty($courses)): ?>
                    <section class="mb-10">
                        <h2 class="text-xl font-bold mb-4 pb-2 border-b">Courses (<?php echo count($courses); ?>)</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach ($courses as $course): ?>
                                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                                    <a href="course.php?id=<?php echo urlencode($course['id']); ?>" class="block">
                                        <div class="h-40 bg-gray-200 relative">
                                            <?php if (!empty($course['thumbnail_url'])): ?>
                                                <img 
                                                    src="<?php echo htmlspecialchars($course['thumbnail_url']); ?>" 
                                                    alt="<?php echo htmlspecialchars($course['title']); ?>"
                                                    class="w-full h-full object-cover"
                                                    loading="lazy"
                                                >
                                            <?php else: ?>
                                                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                                    <span class="text-gray-400">No image</span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($course['category'])): ?>
                                                <span class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded">
                                                    <?php echo htmlspecialchars($course['category']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="p-4">
                                            <h3 class="font-bold text-lg mb-1 line-clamp-1"><?php echo htmlspecialchars($course['title']); ?></h3>
                                            <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?php echo htmlspecialchars($course['description']); ?></p>
                                            <div class="flex justify-between items-center">
                                                <span class="text-blue-600 font-bold">
                                                    <?php echo $course['price'] > 0 ? '$' . number_format($course['price'], 2) : 'Free'; ?>
                                                </span>
                                                <span class="text-gray-500 text-sm">
                                                    By <?php echo htmlspecialchars($course['creator_name'] ?? 'Unknown'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Lessons Section -->
                <?php if (!empty($lessons)): ?>
                    <section class="mb-10">
                        <h2 class="text-xl font-bold mb-4 pb-2 border-b">Lessons (<?php echo count($lessons); ?>)</h2>
                        <div class="space-y-4">
                            <?php foreach ($lessons as $lesson): ?>
                                <div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition-shadow duration-200">
                                    <div class="flex flex-col md:flex-row gap-4">
                                        <div class="w-full md:w-48 h-32 bg-gray-200 rounded-lg flex-shrink-0">
                                            <?php if (!empty($lesson['thumbnail_url'])): ?>
                                                <img 
                                                    src="<?php echo htmlspecialchars($lesson['thumbnail_url']); ?>" 
                                                    alt="<?php echo htmlspecialchars($lesson['course_title']); ?>"
                                                    class="w-full h-full object-cover rounded-lg"
                                                    loading="lazy"
                                                >
                                            <?php else: ?>
                                                <div class="w-full h-full flex items-center justify-center bg-gray-100 rounded-lg">
                                                    <span class="text-gray-400">No image</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow">
                                            <h3 class="font-bold text-lg mb-1">
                                                <a href="lesson.php?id=<?php echo urlencode($lesson['id']); ?>" class="hover:text-blue-600">
                                                    <?php echo htmlspecialchars($lesson['title']); ?>
                                                </a>
                                            </h3>
                                            <p class="text-sm text-gray-500 mb-2">
                                                From course: 
                                                <a href="course.php?id=<?php echo urlencode($lesson['course_id']); ?>" class="text-blue-600 hover:underline">
                                                    <?php echo htmlspecialchars($lesson['course_title']); ?>
                                                </a>
                                            </p>
                                            <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?php echo htmlspecialchars($lesson['description']); ?></p>
                                            <div class="flex gap-2">
                                                <?php if (!empty($lesson['category'])): ?>
                                                    <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">
                                                        <?php echo htmlspecialchars($lesson['category']); ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (!empty($lesson['video_url'])): ?>
                                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                                        Has Video
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            <?php else: ?>
                <!-- No results found -->
                <div class="py-10 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <h2 class="text-xl font-bold mb-2">No results found</h2>
                    <p class="text-gray-600 mb-4">Try different keywords or check your spelling</p>
                    <div class="max-w-md mx-auto text-left">
                        <h3 class="font-medium mb-2">Suggestions:</h3>
                        <ul class="text-gray-600 list-disc pl-5">
                            <li>Check spelling</li>
                            <li>Use more general terms</li>
                            <li>Try different keywords</li>
                            <li>Use fewer keywords</li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Empty search -->
            <div class="py-10 text-center">
                <h2 class="text-xl font-bold mb-2">Enter a search term</h2>
                <p class="text-gray-600 mb-4">Search for courses and lessons across Silent Voice</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchTerm = <?php echo json_encode($search_query); ?>;
            if (searchTerm) {
                const regex = new RegExp(searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
                
                document.querySelectorAll('.bg-white p:not(.text-gray-500)').forEach(p => {
                    const text = p.textContent;
                    if (regex.test(text)) {
                        p.innerHTML = text.replace(regex, match => `<span class="bg-yellow-200">${match}</span>`);
                    }
                });
            }
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>