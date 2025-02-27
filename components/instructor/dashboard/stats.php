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
                            echo "<li class='flex justify-between bg-gray-100 p-2 rounded'>";
                            echo "<span>{$assignment['name']}</span>";
                            echo "<span class='text-gray-600'>{$assignment['date']}</span>";
                            echo "</li>";
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
                            echo "<div>";
                            echo "<h3 class='font-medium'>{$performance['class']}</h3>";
                            echo "<div class='bg-gray-200 rounded-full h-4 mt-2'>";
                            echo "<div class='bg-green-500 rounded-full h-4' style='width: {$performance['average']}%'></div>";
                            echo "</div>";
                            echo "<p class='text-sm text-gray-600 mt-1'>Class Average: {$performance['average']}%</p>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Recent Messages -->
            <div class="mt-5 max-h-14vh bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Recent Messages</h2>
                <ul class="space-y-4">
                    <?php
                    // Replace with actual PHP code to fetch and display messages
                    $messages = [
                        ["from" => "John Doe", "subject" => "Question about homework", "time" => "2 hours ago"],
                        ["from" => "Jane Smith", "subject" => "Absence notification", "time" => "1 day ago"],
                        ["from" => "Admin", "subject" => "Staff meeting reminder", "time" => "3 days ago"]
                    ];
                    foreach ($messages as $message) {
                        echo "<li class='flex items-center justify-between border-b pb-2'>";
                        echo "<div>";
                        echo "<h3 class='font-medium'>{$message['from']}</h3>";
                        echo "<p class='text-sm text-gray-600'>{$message['subject']}</p>";
                        echo "</div>";
                        echo "<span class='text-xs text-gray-500'>{$message['time']}</span>";
                        echo "</li>";
                    }
                    ?>
                </ul>
            </div>
        </main>
    </div>
</body>
</html>