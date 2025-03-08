<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex flex-col">

        <!-- Main Content -->
        <main class="flex-grow container mx-auto mt-8 px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Classes Card -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4">My Classes</h2>
                    <ul class="space-y-2">
                        <?php
                        // Replace with actual PHP code to fetch and display classes
                        $classes = ["Math 101", "Science 202", "History 303"];
                        foreach ($classes as $class) {
                            echo "<li class='bg-gray-100 p-2 rounded'>{$class}</li>";
                        }
                        ?>
                    </ul>
                </div>

                <!-- Upcoming Assignments Card -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Upcoming Assignments</h2>
                    <ul class="space-y-2">
                        <?php
                        // Replace with actual PHP code to fetch and display assignments
                        $assignments = [
                            ["name" => "Math Quiz", "date" => "2023-05-15"],
                            ["name" => "Science Project", "date" => "2023-05-20"],
                            ["name" => "History Essay", "date" => "2023-05-25"]
                        ];
                        foreach ($assignments as $assignment) {
                            echo "<li class='flex justify-between bg-gray-100 p-2 rounded'> 
                                    <span>{$assignment['name']}</span> 
                                    <span class='text-gray-600'>{$assignment['date']}</span> 
                                  </li>";
                        }
                        ?>
                    </ul>
                </div>

                <!-- Student Performance Card -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Student Performance</h2>
                    <div class="space-y-4">
                        <?php
                        // Replace with actual PHP code to fetch and display student performance
                        $performances = [
                            ["class" => "Math 101", "average" => 85],
                            ["class" => "Science 202", "average" => 78],
                            ["class" => "History 303", "average" => 92]
                        ];
                        foreach ($performances as $performance) {
                            echo "<div>
                                    <h3 class='font-medium'>{$performance['class']}</h3>
                                    <div class='bg-gray-200 rounded-full h-4 mt-2'>
                                        <div class='bg-green-500 rounded-full h-4' style='width: {$performance['average']}%'></div>
                                    </div>
                                    <p class='text-sm text-gray-600 mt-1'>Class Average: {$performance['average']}%</p>
                                  </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Recent Activities (Using Logs Table) -->
            <div class="mt-5 max-h-64 overflow-y-auto bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Recent Activities</h2>
                <ul class="space-y-4">
                    <?php
                    // Connect to MySQL Database
                    $conn = new mysqli("localhost", "root", "root", "sign_language");
                    
                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Fetch recent activities from logs table
                    $sql = "SELECT users.username, logs.action, logs.details, logs.timestamp 
                            FROM logs 
                            JOIN users ON logs.user_id = users.id 
                            ORDER BY logs.timestamp DESC 
                            LIMIT 5"; // Show latest 5 activities

                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<li class='flex items-center justify-between border-b pb-2'>
                                    <div>
                                        <h3 class='font-medium'>{$row['username']} <span class='text-gray-600 text-sm'>({$row['action']})</span></h3>
                                        <p class='text-sm text-gray-600'>{$row['details']}</p>
                                    </div>
                                    <span class='text-xs text-gray-500'>{$row['timestamp']}</span>
                                  </li>";
                        }
                    } else {
                        echo "<li class='text-gray-500'>No recent activities</li>";
                    }

                    $conn->close();
                    ?>
                </ul>
            </div>
        </main>
    </div>
</body>
</html>
