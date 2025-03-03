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
                    colors: {
                        'primary': '#4A90E2',                        'primary-dark': '#2A69A4',
                        'secondary': '#7ED321',
                        'accent': '#F5A623',
                        'success': '#10B981',
                        'warning': '#F1C40F',
                        'error': '#E74C3C',
                        'background': '#f8fafb',
                        'surface': '#FFFFFF',
                        'text': '#333333',
                        'text-light': '#7F8C8D',
                    }
                }
            }
        };
    </script>
</head>

<body class="bg-background">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Profile Header -->
        <div class="bg-surface rounded-xl shadow-lg p-8 mb-8">
            <div class="flex items-start gap-8">
                <!-- Avatar -->
                <div class="w-32 h-32 relative group rounded-full overflow-hidden bg-primary flex items-center justify-center">
                    <img id="profile-avatar" src="assets\avatar1.svg" alt="User Avatar" class="w-full h-full object-cover">
                    <button onclick="openAvatarModal()" class="absolute hidden group-hover:block size-18 top-1/2 right-1/2 transform translate-x-1/2 -translate-y-1/2">
                        <img src="/assets/changePen.svg" alt="">
                    </button>
                </div>

                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <h1 class="text-3xl font-bold text-text mb-2">John Doe</h1>

                    </div>
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

                    foreach ($journeyPoints as $point) {
                        $statusColor = match ($point['status']) {
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

                    foreach ($currentCourses as $course) {
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

                    foreach ($achievements as $achievement) {
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

    <!-- Avatar Selection Modal -->
    <div id="avatar-modal" class="fixed inset-0 bg-black bg-opacity-50 flex z-10 items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-text mb-4">Select an Avatar</h2>
                <button onclick="closeAvatarModal()" class=" size-7">
                    <img src="/assets/close.svg" alt="">
                </button>
            </div>
            <div class="flex justify-center items-center flex-wrap gap-4">
                <!-- PHP Loop to generate avatar images -->
                <?php
                for ($i = 1; $i <= 15; $i++) {
                    $avatarPath = "assets/avatar{$i}.svg"; // Dynamic path to avatar
                    echo "
                        <img src='{$avatarPath}' class='w-16 h-16 rounded-full cursor-pointer border-2 border-transparent hover:border-primary' onclick='selectAvatar(\"{$avatarPath}\")'>
                    ";
                }
                ?>
            </div>
        </div>
    </div>

</body>

</html>