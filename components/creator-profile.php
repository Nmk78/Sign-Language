]
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creator Profile - EDUTOCK</title>
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
                        'background': '#f8fafb',
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
        <!-- Creator Profile Header -->
        <div class="bg-surface rounded-xl shadow-lg p-8 mb-8">
            <div class="flex items-start gap-8">
                <div class="w-40 h-40 rounded-xl bg-primary/10 overflow-hidden">
                    <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/image-jNLOT8D3ifET6hAc7vRlsBqodzVLf8.png" alt="Creator" class="w-full h-full object-cover">
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-3xl font-bold text-text mb-2">Sarah Johnson</h1>
                            <p class="text-text-light mb-4">Senior Web Development Instructor</p>
                            <div class="flex gap-4 mb-6">
                                <span class="text-text-light">⭐ 4.9 Instructor Rating</span>
                                <span class="text-text-light">👥 15,000+ Students</span>
                                <span class="text-text-light">📚 12 Courses</span>
                            </div>
                        </div>
                        <button class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-light transition-colors">
                            Follow
                        </button>
                    </div>
                    <p class="text-text-light">
                        Passionate about teaching web development and helping students achieve their coding goals.
                        Specialized in JavaScript, React, and Modern Web Technologies.
                    </p>
                </div>
            </div>
        </div>

        <!-- Courses and Stats -->
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Popular Courses -->
            <div class="col-span-2 space-y-6">
                <h2 class="text-2xl font-bold text-text">Popular Courses</h2>
                <?php
                $courses = [
                    [
                        'title' => 'Complete JavaScript Course 2024',
                        'students' => '5,234',
                        'rating' => '4.9',
                        'price' => '$89.99',
                        'level' => 'All Levels'
                    ],
                    [
                        'title' => 'React & Redux Masterclass',
                        'students' => '3,892',
                        'rating' => '4.8',
                        'price' => '$94.99',
                        'level' => 'Intermediate'
                    ],
                    [
                        'title' => 'Web Development Bootcamp',
                        'students' => '7,123',
                        'rating' => '4.9',
                        'price' => '$129.99',
                        'level' => 'Beginner'
                    ]
                ];

                foreach ($courses as $course) {
                    echo "
                    <div class='bg-surface rounded-lg shadow-lg p-6'>
                        <h3 class='text-xl font-semibold text-text mb-4'>{$course['title']}</h3>
                        <div class='grid grid-cols-2 gap-4 text-sm'>
                            <div class='text-text-light'>👥 {$course['students']} students</div>
                            <div class='text-text-light'>⭐ {$course['rating']} rating</div>
                            <div class='text-text-light'>💰 {$course['price']}</div>
                            <div class='text-text-light'>📚 {$course['level']}</div>
                        </div>
                        <button class='mt-4 w-full bg-secondary text-white py-2 rounded-lg hover:bg-secondary/90 transition-colors'>
                            View Course
                        </button>
                    </div>";
                }
                ?>
            </div>

            <!-- Stats and Achievements -->
            <div class="space-y-8">
                <div class="bg-surface rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold text-text mb-4">Statistics</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-text-light">Total Students</span>
                            <span class="font-semibold text-text">15,249</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-text-light">Reviews</span>
                            <span class="font-semibold text-text">4,892</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-text-light">Avg. Rating</span>
                            <span class="font-semibold text-text">4.9</span>
                        </div>
                    </div>
                </div>

                <div class="bg-surface rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold text-text mb-4">Certifications</h2>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">🏆</span>
                            <span class="text-text">Master Web Developer</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">📜</span>
                            <span class="text-text">Certified JavaScript Expert</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">🎓</span>
                            <span class="text-text">React Native Specialist</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>