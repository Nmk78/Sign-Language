<?php
require 'utils/db.php'; // Include database connection

// Pagination settings
$limit = 6; // Courses per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Category filter
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';

// Fetch distinct categories
$categoryQuery = "SELECT DISTINCT category FROM courses";
$categories = $conn->query($categoryQuery);

// Fetch courses with optional category filter
$sql = "SELECT * FROM courses";
if ($categoryFilter) {
    $sql .= " WHERE category = ?";
}
$sql .= " LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if ($categoryFilter) {
    $stmt->bind_param("sii", $categoryFilter, $limit, $offset);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

// Get total number of courses for pagination
$countQuery = "SELECT COUNT(*) AS total FROM courses";
if ($categoryFilter) {
    $countQuery .= " WHERE category = ?";
    $countStmt = $conn->prepare($countQuery);
    $countStmt->bind_param("s", $categoryFilter);
    $countStmt->execute();
    $countResult = $countStmt->get_result()->fetch_assoc();
} else {
    $countResult = $conn->query($countQuery)->fetch_assoc();
}
$totalCourses = $countResult['total'];
$totalPages = ceil($totalCourses / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Courses</title>
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-6xl mx-auto">

        <!-- Category Filter -->
        <div class="mb-4 flex justify-center space-x-2">
            <a href="?category=" class="px-4 py-2 border rounded <?php echo $categoryFilter === '' ? 'bg-blue-500 text-white' : 'bg-white text-blue-500'; ?>">
                All Categories
            </a>
            <?php while ($row = $categories->fetch_assoc()): ?>
                <?php if (!empty($row['category'])): ?>
                    <a href="?category=<?php echo urlencode($row['category']); ?>" class="px-4 py-2 border rounded <?php echo $categoryFilter === $row['category'] ? 'bg-blue-500 text-white' : 'bg-white text-blue-500'; ?>">
                        <?php echo $row['category']; ?>
                    </a>
                <?php endif; ?>
            <?php endwhile; ?>
        </div>

        <!-- Courses Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <img src="<?php echo $row['thumbnail_url'] ? $row['thumbnail_url'] : 'assets/defaultThumbnail.png'; ?>" alt="<?php echo $row['title']; ?>" class="w-full h-40 object-cover rounded-lg">
                    <h2 class="text-xl font-semibold mt-2"> <?php echo $row['title']; ?> </h2>
                    <p class="text-gray-600 text-sm"> <?php echo $row['category']; ?> </p>
                    <p class="mt-2 text-gray-800"> <?php echo substr($row['description'], 0, 80) . '...'; ?> </p>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-center space-x-2">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?><?php echo $categoryFilter ? '&category=' . urlencode($categoryFilter) : ''; ?>"
                    class="px-4 py-2 border rounded <?php echo $i == $page ? 'bg-blue-500 text-white' : 'bg-white text-blue-500'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
</body>

</html>