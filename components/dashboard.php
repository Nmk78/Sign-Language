<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EDUTOCK</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                        'background': '#F5F5F5',
                        'surface': '#FFFFFF',
                        'text': '#333333',
                        'text-light': '#7F8C8D',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-background">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <?php
            $stats = [
                ['label' => 'Courses in Progress', 'value' => '4', 'color' => 'primary'],
                ['label' => 'Completed Courses', 'value' => '12', 'color' => 'success'],
                ['label' => 'Hours Learned', 'value' => '156', 'color' => 'secondary'],
                ['label' => 'Certificates', 'value' => '8', 'color' => 'accent']
            ];

            foreach ($stats as $stat) {
                echo "
                <div class='bg-surface p-6 rounded-xl shadow-lg'>
                    <p class='text-text-light text-sm'>{$stat['label']}</p>
                    <p class='text-3xl font-bold text-{$stat['color']}'>{$stat['value']}</p>
                </div>";
            }
            ?>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Today's Schedule -->
            <div class="col-span-2 bg-surface rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-text mb-6">Today's Schedule</h2>
                <div class="space-y-4">
                    <?php
                    $schedule = [
                        ['time' => '09:00 AM', 'title' => 'JavaScript Advanced Concepts', 'type' => 'Live Session'],
                        ['time' => '11:30 AM', 'title' => 'React Components Workshop', 'type' => 'Practice'],
                        ['time' => '02:00 PM', 'title' => 'Database Design', 'type' => 'Lecture'],
                        ['time' => '04:30 PM', 'title' => 'Code Review Session', 'type' => 'Group Work']
                    ];

                    foreach ($schedule as $item) {
                        echo "
                        <div class='flex items-center gap-4 p-4 border border-gray-200 rounded-lg'>
                            <div class='w-24 text-sm text-text-light'>{$item['time']}</div>
                            <div>
                                <h3 class='font-medium text-text'>{$item['title']}</h3>
                                <p class='text-sm text-text-light'>{$item['type']}</p>
                            </div>
                        </div>";
                    }
                    ?>
                </div>
            </div>

            <!-- Recommended Courses -->
            <div class="bg-surface rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-text mb-6">Recommended</h2>
                <div class="space-y-4">
                    <?php
                    $recommended = [
                        ['title' => 'Vue.js Essentials', 'rating' => 4.8, 'students' => '2.3k'],
                        ['title' => 'Python for AI', 'rating' => 4.9, 'students' => '3.1k'],
                        ['title' => 'AWS Basics', 'rating' => 4.7, 'students' => '1.8k']
                    ];

                    foreach ($recommended as $course) {
                        echo "
                        <div class='p-4 border border-gray-200 rounded-lg'>
                            <h3 class='font-medium text-text mb-2'>{$course['title']}</h3>
                            <div class='flex justify-between text-sm text-text-light'>
                                <span>⭐ {$course['rating']}</span>
                                <span>{$course['students']} students</span>
                            </div>
                        </div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>