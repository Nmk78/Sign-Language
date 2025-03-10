<?php
// Initialize database connection
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'root';
$db_name = 'sign_language';

$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch only lessons that have quizzes using JOIN
$lessons_query = "SELECT DISTINCT l.id, l.title, l.description, l.course_id 
                 FROM lesson l
                 INNER JOIN quizzes q ON l.id = q.lesson_id
                 ORDER BY l.id ASC";
$lessons_result = $conn->query($lessons_query);
$lessons = [];

if ($lessons_result && $lessons_result->num_rows > 0) {
    while ($row = $lessons_result->fetch_assoc()) {
        $lessons[] = $row;
    }
}

// Fetch quizzes for all lessons
$quizzes_query = "SELECT id, lesson_id, question, option_a, option_b, option_c, correct_option FROM quizzes ORDER BY lesson_id, id ASC";
$quizzes_result = $conn->query($quizzes_query);
$quizzes = [];

if ($quizzes_result && $quizzes_result->num_rows > 0) {
    while ($row = $quizzes_result->fetch_assoc()) {
        $quizzes[] = $row;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Language Lessons & Quizzes</title>
    <!-- Tailwind CSS from CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Inter font from Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <!-- Heroicons (for icons) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@heroicons/react@2.0.18/outline/esm/index.css">
    <script>
        tailwind.config = {
            theme: {
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                },
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        },
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-out',
                        'slide-up': 'slideUp 0.4s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                    },
                }
            }
        }
    </script>
    <style>
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Fade in animation for modal */
        .modal-fade-in {
            animation: modalFadeIn 0.3s ease-out forwards;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Slide up animation for modal content */
        .modal-slide-up {
            animation: modalSlideUp 0.4s ease-out forwards;
        }
        @keyframes modalSlideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen">

    <div class="container mx-auto px-4 py-8 -mt-6 relative z-10">
        <!-- Lessons Section -->
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl mb-12">
                <div class="bg-gradient-to-r from-primary-500 to-primary-600 px-6 py-5 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <h2 class="text-xl font-bold text-white">Lessons with Quizzes</h2>
                </div>
                
                <div class="p-6">
                    <p class="text-gray-600 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Click on a lesson to view its quizzes
                    </p>
                    
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <div class="max-h-[500px] overflow-y-auto custom-scrollbar">
                            <?php if (empty($lessons)): ?>
                                <div class="p-8 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-gray-500 text-lg">No lessons with quizzes available.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($lessons as $lesson): 
                                    // Count quizzes for this lesson
                                    $quiz_count = 0;
                                    foreach ($quizzes as $quiz) {
                                        if ($quiz['lesson_id'] == $lesson['id']) {
                                            $quiz_count++;
                                        }
                                    }
                                ?>
                                    <div class="group border-b border-gray-200 last:border-b-0 hover:bg-gray-50 transition-all duration-200 cursor-pointer" 
                                         onclick="showQuizzes(<?php echo $lesson['id']; ?>, '<?php echo addslashes(htmlspecialchars($lesson['title'])); ?>')">
                                        <div class="p-5 flex justify-between items-center">
                                            <div class="flex-1">
                                                <div class="flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 mr-2 group-hover:text-primary-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                    </svg>
                                                    <h3 class="font-semibold text-gray-800 group-hover:text-primary-600 transition-colors"><?php echo htmlspecialchars($lesson['title']); ?></h3>
                                                </div>
                                                <?php if (!empty($lesson['description'])): ?>
                                                    <p class="text-sm text-gray-500 mt-1 ml-7"><?php echo htmlspecialchars($lesson['description']); ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="bg-primary-100 text-primary-800 text-sm font-medium px-3 py-1 rounded-full mr-3">
                                                    <?php echo $quiz_count; ?> <?php echo $quiz_count === 1 ? 'quiz' : 'quizzes'; ?>
                                                </span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-primary-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quiz Modal/Popup -->
    <div id="quizModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden overflow-y-auto modal-fade-in flex items-start justify-center pt-10 pb-10">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full mx-4 my-8 modal-slide-up relative">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-t-xl px-6 py-4 flex justify-between items-center">
                <h2 id="modalLessonTitle" class="text-xl font-bold">Lesson Quizzes</h2>
                <button onclick="closeModal()" class="text-white hover:text-primary-200 transition-colors focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div id="quizContent" class="p-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                <!-- Quiz content will be loaded here -->
            </div>
        </div>
    </div>
    
    <script>
        // Store quizzes data in JavaScript
        const quizzes = <?php echo json_encode($quizzes); ?>;
        const lessons = <?php echo json_encode($lessons); ?>;
        
        // Function to show quizzes for a specific lesson
        function showQuizzes(lessonId, lessonTitle) {
            // Update modal title
            document.getElementById('modalLessonTitle').textContent = `Quizzes for: ${lessonTitle}`;
            
            // Filter quizzes for this lesson
            const lessonQuizzes = quizzes.filter(q => q.lesson_id == lessonId);
            
            // Generate quiz content HTML
            let quizHtml = '';
            
            if (lessonQuizzes.length === 0) {
                quizHtml = `
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-gray-500 text-lg mb-6">No quizzes available for this lesson yet.</p>
                        <a href="create-quiz.php?lesson_id=${lessonId}" class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-md hover:shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Create First Quiz
                        </a>
                    </div>
                `;
            } else {
                // quizHtml += `
                //     <div class="mb-6 flex justify-between items-center">
                //         <p class="text-gray-600">
                //             <span class="font-medium text-primary-600">${lessonQuizzes.length}</span> 
                //             ${lessonQuizzes.length === 1 ? 'quiz' : 'quizzes'} available
                //         </p>
                //         <a href="create-quiz.php?lesson_id=${lessonId}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm hover:shadow-md">
                //             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                //                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                //             </svg>
                //             Add Quiz
                //         </a>
                //     </div>
                // `;
                
                lessonQuizzes.forEach((quiz, index) => {
                    quizHtml += `
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-8 hover:shadow-md transition-shadow">
                            <div class="bg-primary-50 border-b border-gray-200 px-6 py-4">
                                <div class="flex items-center">
                                    <span class="flex items-center justify-center bg-primary-600 text-white text-sm font-bold h-6 w-6 rounded-full mr-3">
                                        ${index + 1}
                                    </span>
                                    <h3 class="font-semibold text-gray-800">${quiz.question}</h3>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="border-2 ${quiz.correct_option === 'A' ? 'border-green-500 bg-green-50' : 'border-gray-200'} rounded-xl overflow-hidden transition-all duration-200 hover:shadow-md">
                                        <div class="relative bg-gray-100 h-48 flex items-center justify-center overflow-hidden">
                                            <img src="${quiz.option_a}" alt="Option A" class="w-full h-full object-contain p-2">
                                            ${quiz.correct_option === 'A' ? 
                                                `<div class="absolute top-2 right-2 bg-green-500 text-white rounded-full p-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>` : ''}
                                        </div>
                                        <div class="p-4 flex justify-between items-center ${quiz.correct_option === 'A' ? 'bg-green-50' : 'bg-white'}">
                                            <span class="font-medium ${quiz.correct_option === 'A' ? 'text-green-700' : 'text-gray-700'}">Option A</span>
                                            ${quiz.correct_option === 'A' ? 
                                                `<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full">Correct</span>` : ''}
                                        </div>
                                    </div>
                                    
                                    <div class="border-2 ${quiz.correct_option === 'B' ? 'border-green-500 bg-green-50' : 'border-gray-200'} rounded-xl overflow-hidden transition-all duration-200 hover:shadow-md">
                                        <div class="relative bg-gray-100 h-48 flex items-center justify-center overflow-hidden">
                                            <img src="${quiz.option_b}" alt="Option B" class="w-full h-full object-contain p-2">
                                            ${quiz.correct_option === 'B' ? 
                                                `<div class="absolute top-2 right-2 bg-green-500 text-white rounded-full p-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>` : ''}
                                        </div>
                                        <div class="p-4 flex justify-between items-center ${quiz.correct_option === 'B' ? 'bg-green-50' : 'bg-white'}">
                                            <span class="font-medium ${quiz.correct_option === 'B' ? 'text-green-700' : 'text-gray-700'}">Option B</span>
                                            ${quiz.correct_option === 'B' ? 
                                                `<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full">Correct</span>` : ''}
                                        </div>
                                    </div>
                                    
                                    <div class="border-2 ${quiz.correct_option === 'C' ? 'border-green-500 bg-green-50' : 'border-gray-200'} rounded-xl overflow-hidden transition-all duration-200 hover:shadow-md">
                                        <div class="relative bg-gray-100 h-48 flex items-center justify-center overflow-hidden">
                                            <img src="${quiz.option_c}" alt="Option C" class="w-full h-full object-contain p-2">
                                            ${quiz.correct_option === 'C' ? 
                                                `<div class="absolute top-2 right-2 bg-green-500 text-white rounded-full p-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>` : ''}
                                        </div>
                                        <div class="p-4 flex justify-between items-center ${quiz.correct_option === 'C' ? 'bg-green-50' : 'bg-white'}">
                                            <span class="font-medium ${quiz.correct_option === 'C' ? 'text-green-700' : 'text-gray-700'}">Option C</span>
                                            ${quiz.correct_option === 'C' ? 
                                                `<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full">Correct</span>` : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
            
            // Update modal content
            document.getElementById('quizContent').innerHTML = quizHtml;
            
            // Show the modal
            document.getElementById('quizModal').classList.remove('hidden');
            
            // Prevent scrolling on the body when modal is open
            document.body.classList.add('overflow-hidden');
        }
        
        // Function to close the modal
        function closeModal() {
            document.getElementById('quizModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('quizModal');
            if (event.target === modal) {
                closeModal();
            }
        }
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>