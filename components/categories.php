<?php
// Simulated course data - in a real application, this would come from a database
$courses = [
    'beginner' => [
        ['title' => 'ASL Basics', 'description' => 'Learn the fundamentals of American Sign Language', 'image' => 'asl_basics.jpg'],
        ['title' => 'BSL for Beginners', 'description' => 'Start your journey in British Sign Language', 'image' => 'bsl_beginners.jpg'],
    ],
    'intermediate' => [
        ['title' => 'Conversational ASL', 'description' => 'Improve your ASL conversation skills', 'image' => 'conversational_asl.jpg'],
        ['title' => 'Medical BSL', 'description' => 'Learn BSL for medical environments', 'image' => 'medical_bsl.jpg'],
    ],
    'advanced' => [
        ['title' => 'ASL Literature', 'description' => 'Explore ASL poetry and storytelling', 'image' => 'asl_literature.jpg'],
        ['title' => 'BSL Interpreter Skills', 'description' => 'Advanced course for aspiring BSL interpreters', 'image' => 'bsl_interpreter.jpg'],
    ],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Language Courses</title>
    <style>
        /* Base styles */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2 {
            color: #2c3e50;
        }

        /* Utility classes (Tailwind-like) */
        .mb-4 { margin-bottom: 1rem; }
        .mt-8 { margin-top: 2rem; }
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .gap-4 { gap: 1rem; }
        .p-4 { padding: 1rem; }
        .bg-white { background-color: white; }
        .rounded { border-radius: 0.25rem; }
        .shadow { box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); }

        /* Custom styles */
        .course-card {
            transition: transform 0.3s ease-in-out;
        }
        .course-card:hover {
            transform: translateY(-5px);
        }
        .course-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 0.25rem 0.25rem 0 0;
        }

        /* Responsive grid */
        @media (min-width: 640px) {
            .grid-cols-1 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (min-width: 768px) {
            .grid-cols-1 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Sign Language Courses</h1>

        <?php foreach ($courses as $level => $levelCourses): ?>
            <section class="mt-8">
                <h2 class="mb-4"><?= ucfirst($level) ?> Courses</h2>
                <div class="grid grid-cols-1 gap-4">
                    <?php foreach ($levelCourses as $course): ?>
                        <div class="course-card bg-white rounded shadow p-4">
                            <img src="<?= $course['image'] ?>" alt="<?= $course['title'] ?>" class="course-image mb-4">
                            <h3><?= $course['title'] ?></h3>
                            <p><?= $course['description'] ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
</body>
</html>