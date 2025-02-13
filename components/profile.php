<?php include 'components/layout.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - EDUTOCK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: <?php echo json_encode(include('config/colors.php')['colors']); ?>
                }
            }
        }
    </script>
</head>
<body class="bg-background">
    <?php renderHeader('profile'); ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Profile Header -->
        <div class="bg-surface rounded-xl shadow-lg p-8 mb-8">
            <div class="flex items-start gap-8">
                <div class="w-32 h-32 rounded-full bg-primary flex items-center justify-center text-white text-4xl">
                    JD
                </div>
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-text mb-2">John Doe</h1>
                    <p class="text-text-light mb-4">Web Development Enthusiast</p>
                    <div class="flex gap-4">
                        <div class="bg-primary/10 px-4 py-2 rounded-lg">
                            <span class="text-primary font-bold">12</span>
                            <span class="text-text-light"> Courses</span>
                        </div>
                        <div class="bg-success/10 px-4 py-2 rounded-lg">
                            <span class="text-success font-bold">85%</span>
                            <span class="text-text-light"> Completion</span>
                        </div>
                        <div class="bg-accent/10 px-4 py-2 rounded-lg">
                            <span class="text-accent font-bold">24</span>
                            <span class="text-text-light"> Certificates</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Learning Journey Map -->
        <div class="bg-surface rounded-xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-text mb-6">Learning Journey</h2>
            <div class="relative">
                <!-- Journey Path -->
                <div class="absolute top-1/2 left-0 right-0 h-2 bg-primary/20 -translate-y-1/2"></div>
                <div class="relative grid grid-cols-4 gap-8">
                    <?php
                    $journeyPoints = [
                        ['title' => 'Web Basics', 'status' => 'completed'],
                        ['title' => 'Frontend Dev', 'status' => 'completed'],
                        ['title' => 'Backend Dev', 'status' => 'in-progress'],
                        ['title' => 'Full Stack', 'status' => 'locked']
                    ];

                    foreach($journeyPoints as $point) {
                        $statusColor = match($point['status']) {
                            'completed' => 'bg-success',
                            'in-progress' => 'bg-warning',
                            'locked' => 'bg-gray-300'
                        };
                        echo "
                        <div class='flex flex-col items-center'>
                            <div class='w-8 h-8 {$statusColor} rounded-full mb-4 z-10'></div>
                            <h3 class='font-medium text-text'>{$point['title']}</h3>
                            <span class='text-sm text-text-light'>" . ucfirst($point['status']) . "</span>
                        </div>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Current Courses -->
        <div class="grid md:grid-cols-2 gap-8">
            <div class="bg-surface rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-text mb-6">Current Courses</h2>
                <div class="space-y-4">
                    <?php
                    $currentCourses = [
                        ['title' => 'Advanced JavaScript', 'progress' => 75],
                        ['title' => 'React Fundamentals', 'progress' => 45],
                        ['title' => 'Node.js Basics', 'progress' => 30]
                    ];

                    foreach($currentCourses as $course) {
                        echo "
                        <div class='p-4 border border-gray-200 rounded-lg'>
                            <div class='flex justify-between items-center mb-2'>
                                <h3 class='font-medium text-text'>{$course['title']}</h3>
                                <span class='text-sm text-text-light'>{$course['progress']}%</span>
                            </div>
                            <div class='w-full h-2 bg-gray-200 rounded-full overflow-hidden'>
                                <div class='h-full bg-primary rounded-full' style='width: {$course['progress']}%'></div>
                            </div>
                        </div>";
                    }
                    ?>
                </div>
            </div>

            <!-- Achievements -->
            <div class="bg-surface rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-text mb-6">Achievements</h2>
                <div class="grid grid-cols-3 gap-4">
                    <?php
                    $achievements = [
                        ['icon' => '🏆', 'title' => 'Fast Learner', 'desc' => 'Completed 5 courses in a month'],
                        ['icon' => '⭐', 'title' => 'Top Student', 'desc' => 'Achieved 95% in Web Dev'],
                        ['icon' => '🎯', 'title' => 'Goal Setter', 'desc' => 'Completed all goals'],
                        ['icon' => '🌟', 'title' => 'Expert', 'desc' => 'Mastered JavaScript'],
                        ['icon' => '📚', 'title' => 'Bookworm', 'desc' => '100 hours of learning'],
                        ['icon' => '🎓', 'title' => 'Graduate', 'desc' => 'Finished Full Stack']
                    ];

                    foreach($achievements as $achievement) {
                        echo "
                        <div class='p-4 border border-gray-200 rounded-lg text-center'>
                            <div class='text-3xl mb-2'>{$achievement['icon']}</div>
                            <h3 class='font-medium text-text text-sm'>{$achievement['title']}</h3>
                            <p class='text-xs text-text-light'>{$achievement['desc']}</p>
                        </div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>