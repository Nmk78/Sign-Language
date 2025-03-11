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

// Get search query from URL parameter
$search_query = isset($_GET['query']) ? trim($_GET['query']) : '';

// Initialize results arrays
$courses = [];
$lessons = [];

if (!empty($search_query)) {
    // Search in courses
    $course_sql = "SELECT c.id, c.title, c.description, c.category, c.thumbnail_url, c.price, 
                        c.status, u.username as creator_name
                   FROM courses c
                   LEFT JOIN users u ON c.created_by = u.id
                   WHERE (c.title LIKE ? OR c.description LIKE ?) 
                   AND c.status = 'published'
                   ORDER BY c.created_at DESC
                   LIMIT 20";
    
    $stmt = $conn->prepare($course_sql);
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $course_result = $stmt->get_result();
    
    while ($row = $course_result->fetch_assoc()) {
        $courses[] = $row;
    }
    
    // Search in lessons
    $lesson_sql = "SELECT l.id, l.title, l.description, l.category, l.video_url, 
                        c.id as course_id, c.title as course_title, c.thumbnail_url
                   FROM lesson l
                   INNER JOIN courses c ON l.course_id = c.id
                   WHERE (l.title LIKE ? OR l.description LIKE ?) 
                   AND c.status = 'published'
                   ORDER BY l.created_at DESC
                   LIMIT 20";
    
    $stmt = $conn->prepare($lesson_sql);
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $lesson_result = $stmt->get_result();
    
    while ($row = $lesson_result->fetch_assoc()) {
        $lessons[] = $row;
    }
}

$total_results = count($courses) + count($lessons);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results for "<?php echo htmlspecialchars($search_query); ?>" | Learning Platform</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container max-w-6xl mx-auto px-4 py-8">

        <?php if (!empty($search_query)): ?>
            <!-- Search results summary -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold mb-2">Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h1>
                <p class="text-gray-600"><?php echo $total_results; ?> results found</p>
            </div>

            <?php if ($total_results > 0): ?>
                <!-- Courses Section -->
                <?php if (count($courses) > 0): ?>
                    <section class="mb-10">
                        <h2 class="text-xl font-bold mb-4 pb-2 border-b">Courses (<?php echo count($courses); ?>)</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach ($courses as $course): ?>
                                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                                    <a href="course.php?id=<?php echo $course['id']; ?>">
                                        <div class="h-40 bg-gray-200 relative">
                                            <?php if (!empty($course['thumbnail_url'])): ?>
                                                <img 
                                                    src="<?php echo htmlspecialchars($course['thumbnail_url']); ?>" 
                                                    alt="<?php echo htmlspecialchars($course['title']); ?>"
                                                    class="w-full h-full object-cover"
                                                >
                                            <?php else: ?>
                                                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                                    <span class="text-gray-400">No image</span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($course['category'])): ?>
                                                <span class="absolute top-2 right-2 bg-primary-500 text-white text-xs px-2 py-1 rounded">
                                                    <?php echo htmlspecialchars($course['category']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="p-4">
                                            <h3 class="font-bold text-lg mb-1 line-clamp-1">
                                                <?php echo htmlspecialchars($course['title']); ?>
                                            </h3>
                                            <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                                <?php echo htmlspecialchars($course['description']); ?>
                                            </p>
                                            <div class="flex justify-between items-center">
                                                <span class="text-primary-600 font-bold">
                                                    <?php if ($course['price'] > 0): ?>
                                                        $<?php echo number_format($course['price'], 2); ?>
                                                    <?php else: ?>
                                                        Free
                                                    <?php endif; ?>
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
                <?php if (count($lessons) > 0): ?>
                    <section class="mb-10">
                        <h2 class="text-xl font-bold mb-4 pb-2 border-b">Lessons (<?php echo count($lessons); ?>)</h2>
                        <div class="space-y-4">
                            <?php foreach ($lessons as $lesson): ?>
                                <div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition-shadow">
                                    <div class="flex flex-col md:flex-row gap-4">
                                        <div class="w-full md:w-48 h-32 bg-gray-200 rounded-lg flex-shrink-0">
                                            <?php if (!empty($lesson['thumbnail_url'])): ?>
                                                <img 
                                                    src="<?php echo htmlspecialchars($lesson['thumbnail_url']); ?>" 
                                                    alt="<?php echo htmlspecialchars($lesson['course_title']); ?>"
                                                    class="w-full h-full object-cover rounded-lg"
                                                >
                                            <?php else: ?>
                                                <div class="w-full h-full flex items-center justify-center bg-gray-100 rounded-lg">
                                                    <span class="text-gray-400">No image</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="flex-grow">
                                            <h3 class="font-bold text-lg mb-1">
                                                <a href="lesson.php?id=<?php echo $lesson['id']; ?>" class="hover:text-primary-600">
                                                    <?php echo htmlspecialchars($lesson['title']); ?>
                                                </a>
                                            </h3>
                                            
                                            <p class="text-sm text-gray-500 mb-2">
                                                From course: 
                                                <a href="course.php?id=<?php echo $lesson['course_id']; ?>" class="text-primary-600 hover:underline">
                                                    <?php echo htmlspecialchars($lesson['course_title']); ?>
                                                </a>
                                            </p>
                                            
                                            <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                                <?php echo htmlspecialchars($lesson['description']); ?>
                                            </p>
                                            
                                            <?php if (!empty($lesson['category'])): ?>
                                                <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">
                                                    <?php echo htmlspecialchars($lesson['category']); ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($lesson['video_url'])): ?>
                                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded ml-2">
                                                    Has Video
                                                </span>
                                            <?php endif; ?>
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
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold mb-2">No results found</h2>
                    <p class="text-gray-600 mb-4">Try different keywords or check your spelling</p>
                    <div class="max-w-md mx-auto">
                        <h3 class="font-medium mb-2">Suggestions:</h3>
                        <ul class="text-gray-600 text-left list-disc pl-5">
                            <li>Make sure all words are spelled correctly</li>
                            <li>Try more general keywords</li>
                            <li>Try different keywords</li>
                            <li>Try fewer keywords</li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <!-- Empty search -->
            <div class="py-10 text-center">
                <h2 class="text-xl font-bold mb-2">Enter a search term</h2>
                <p class="text-gray-600 mb-4">Search for courses and lessons across our platform</p>
            </div>
        <?php endif; ?>
        
        <!-- Related categories (you could fetch popular categories here) -->
        <!-- 4
         hj3
          -->
    </div>

    <script>
        // Highlight search terms in the results
        document.addEventListener('DOMContentLoaded', function() {
            const searchTerm = '<?php echo addslashes($search_query); ?>';
            if (searchTerm) {
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                
                // Function to highlight text in an element
                function highlightText(element) {
                    if (element.nodeType === 3) { // Text node
                        const content = element.nodeValue;
                        if (regex.test(content)) {
                            const fragment = document.createDocumentFragment();
                            const parts = content.split(regex);
                            
                            parts.forEach((part, i) => {
                                if (i % 2 === 1) { // This is a match
                                    const span = document.createElement('span');
                                    span.className = 'bg-yellow-200';
                                    span.textContent = part;
                                    fragment.appendChild(span);
                                } else {
                                    fragment.appendChild(document.createTextNode(part));
                                }
                            });
                            
                            element.parentNode.replaceChild(fragment, element);
                            return true;
                        }
                    } else if (element.nodeType === 1) { // Element node
                        // Don't process certain elements or their children
                        if (['SCRIPT', 'STYLE', 'INPUT', 'TEXTAREA', 'SELECT', 'OPTION', 'A', 'BUTTON'].includes(element.tagName)) {
                            return false;
                        }
                    }
                    return false;
                }
                
                // Process text nodes in the results sections
                const resultSections = document.querySelectorAll('section');
                resultSections.forEach(section => {
                    const iterator = document.createNodeIterator(
                        section,
                        NodeFilter.SHOW_TEXT,
                        { acceptNode: node => node.nodeValue.trim() ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT }
                    );
                    
                    let node;
                    const nodesToProcess = [];
                    while (node = iterator.nextNode()) {
                        nodesToProcess.push(node);
                    }
                    
                    // Process nodes in reverse order to avoid iterator issues when replacing nodes
                    nodesToProcess.reverse().forEach(highlightText);
                });
            }
        });
    </script>
</body>
</html>
