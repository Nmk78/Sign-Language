<?php
// Start session for user data persistence
// session_start();

// Database connection
$conn = new mysqli("localhost", "root", "root", "sign_language");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$success_message = "";
$error_message = "";
$lessonId = isset($_GET['lesson_id']) ? intval($_GET['lesson_id']) : 17;

// Handle quiz creation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_quiz'])) {
    $question = $_POST['question'] ?? '';
    $option_a = $_POST['option_a'] ?? '';
    $option_b = $_POST['option_b'] ?? '';
    $option_c = $_POST['option_c'] ?? '';
    $option_d = $_POST['option_d'] ?? '';
    $correct_option = $_POST['correct_option'] ?? '';
    
    // Validate form data
    if (empty($question) || empty($option_a) || empty($option_b) || empty($option_c) || empty($option_d) || empty($correct_option)) {
        $error_message = "All fields are required.";
    } else {
        // Insert new quiz question
        $sql = "INSERT INTO quizzes (lesson_id, question, option_a, option_b, option_c, option_d, correct_option) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        // Points based on difficulty
        $points = 1;
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssssssi", $lessonId, $question, $option_a, $option_b, $option_c, $option_d, $correct_option);
        
        if ($stmt->execute()) {
            $success_message = "Quiz question created successfully!";
        } else {
            $error_message = "Error creating quiz question: " . $conn->error;
        }
    }
}

// Fetch existing quiz questions for the lesson
$sql = "SELECT id, question, option_a, option_b, option_c, option_d, correct_option
        FROM quizzes WHERE lesson_id = ? ORDER BY id ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lessonId);
$stmt->execute();
$result = $stmt->get_result();
$quizQuestions = [];

while ($row = $result->fetch_assoc()) {
    $quizQuestions[] = $row;
}

// Get lesson information
$lessonQuery = "SELECT title, description FROM lesson WHERE id = ?";
$lessonStmt = $conn->prepare($lessonQuery);
$lessonStmt->bind_param("i", $lessonId);
$lessonStmt->execute();
$lessonResult = $lessonStmt->get_result();
$lessonInfo = $lessonResult->fetch_assoc();

// Close connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Language Quiz Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        },
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(10px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        pulse: {
                            '0%, 100%': { opacity: '1' },
                            '50%': { opacity: '0.5' },
                        },
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-out',
                        'slide-up': 'slideUp 0.4s ease-out',
                        'pulse-slow': 'pulse.4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                },
            },
        };
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Custom scrollbar for Webkit browsers */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #0ea5e9;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #0284c7;
        }
        
        /* Focus styles for better accessibility */
        input:focus, select:focus, textarea:focus {
            outline: 2px solid #0ea5e9;
            outline-offset: 2px;
        }
        
        /* Remove spinner from number inputs */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <!-- Header Section -->
    <header class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-hands text-primary-500 mr-3"></i>
                Sign Language Quiz Manager
            </h1>
            <div>
                <button onclick="openQuizModal()" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg shadow transition duration-200 flex items-center">
                    <i class="fas fa-play-circle mr-2"></i>
                    Take Quiz
                </button>
            </div>
        </div>
    </header>

    <main class=" py-8 ">
        <!-- Status Messages -->
        <?php if (!empty($success_message)): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <p class="text-green-700"><?php echo $success_message; ?></p>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    <p class="text-red-700"><?php echo $error_message; ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Create Quiz Section -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-primary-600 to-secondary-600 py-5 px-6">
                <h2 class="text-2xl font-bold text-white">Create New Sign Language Quiz Question</h2>
                <p class="text-primary-100 mt-1">Add new questions to the <?php echo htmlspecialchars($lessonInfo['title'] ?? 'sign language') ?> quiz</p>
            </div>
            
            <div class="p-6">
                <form method="POST" action="" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                        <div class="space-y-4">
                                <label for="question" class="block text-sm font-medium text-gray-700 mb-1">Question</label>
                                <input type="text" id="question" name="question" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="option_a" class="block text-sm font-medium text-gray-700 mb-1">Option A (URL)</label>
                                    <input type="text" id="option_a" name="option_a" required placeholder="https://example.com/image-a.jpg"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                
                                <div>
                                    <label for="option_b" class="block text-sm font-medium text-gray-700 mb-1">Option B (URL)</label>
                                    <input type="text" id="option_b" name="option_b" required placeholder="https://example.com/image-b.jpg"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                
                                <div>
                                    <label for="option_c" class="block text-sm font-medium text-gray-700 mb-1">Option C (URL)</label>
                                    <input type="text" id="option_c" name="option_c" required placeholder="https://example.com/image-c.jpg"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                
                                <div>
                                    <label for="option_d" class="block text-sm font-medium text-gray-700 mb-1">Option D (URL)</label>
                                    <input type="text" id="option_d" name="option_d" required placeholder="https://example.com/image-d.jpg"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Correct Answer</label>
                                <div class="flex items-center space-x-6">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="correct_option" value="A" checked
                                               class="w-5 h-5 text-primary-600 focus:ring-primary-500">
                                        <span class="ml-2 text-gray-700">Option A</span>
                                    </label>
                                    
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="correct_option" value="B"
                                               class="w-5 h-5 text-primary-600 focus:ring-primary-500">
                                        <span class="ml-2 text-gray-700">Option B</span>
                                    </label>
                                    
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="correct_option" value="C"
                                               class="w-5 h-5 text-primary-600 focus:ring-primary-500">
                                        <span class="ml-2 text-gray-700">Option C</span>
                                    </label>
                                    
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="correct_option" value="D"
                                               class="w-5 h-5 text-primary-600 focus:ring-primary-500">
                                        <span class="ml-2 text-gray-700">Option D</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" name="create_quiz" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:from-primary-700 hover:to-primary-800 shadow-lg hover:shadow-xl flex items-center gap-2">
                            <i class="fas fa-plus-circle"></i>
                            <span>Create Quiz Question</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
            <div class="bg-gradient-to-r from-secondary-600 to-primary-600 py-5 px-6">
                <h2 class="text-2xl font-bold text-white">Existing Quiz Questions</h2>
                <p class="text-primary-100 mt-1">Manage your existing sign language quiz questions</p>
            </div>
            
            <div class="p-6">
                <?php if (empty($quizQuestions)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-question-circle text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-xl font-medium text-gray-600">No quiz questions found</h3>
                        <p class="text-gray-500 mt-1">Start by creating your first question above</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Question</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correct Answer</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($quizQuestions as $index => $question): ?>
                                <tr class="<?php echo $index % 2 === 0 ? 'bg-white' : 'bg-gray-50'; ?> hover:bg-gray-100 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($question['question']); ?></td>                                
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Option <?php echo htmlspecialchars($question['correct_option']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="viewQuestion(<?php echo $question['id']; ?>)" class="text-primary-600 hover:text-primary-900 mr-3">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editQuestion(<?php echo $question['id']; ?>)" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteQuestion(<?php echo $question['id']; ?>)" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <!-- Quiz Modal (Pop-up) -->
    <div id="quizModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <div class="bg-gradient-to-r from-primary-600 to-secondary-600 py-5 px-6 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-white">Sign Language Quiz</h2>
                <button onclick="closeQuizModal()" class="text-white hover:text-gray-200 focus:outline-none">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]" id="quiz-content">
                <div id="quiz-progress" class="mb-8">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Progress</span>
                        <span id="progress-text">0%</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div id="progress-value" class="h-full bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                </div>
                
                <form id="quizForm">
                    <?php foreach ($quizQuestions as $index => $question): ?>
                    <div class="mb-10 question-container <?php echo $index > 0 ? 'hidden' : ''; ?>" data-question="<?php echo $index + 1; ?>">
                        <div class="flex items-center mb-4">
                            <div class="bg-primary-100 text-primary-700 rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3">
                                <?php echo $index + 1; ?>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($question['question']); ?></h3>
                        </div>
                        
                        <div class="grid grid-cols-4 gap-6">

                            <!-- option A -->
                            <label class="cursor-pointer">
                                <input type="radio" name="answer_<?php echo $question['id']; ?>" value="A" class="hidden peer" 
                                       data-correct-option="<?php echo $question['correct_option']; ?>" 
                                       data-question-num="<?php echo $index + 1; ?>"
                                       onclick="updateSelection(this)">
                                <div class="relative border-4 border-gray-200 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 peer-checked:border-primary-500 peer-checked:shadow-md peer-checked:shadow-primary-200 group">
                                    <div class="absolute top-3 left-3 z-10 bg-white/90 text-primary-600 font-bold w-8 h-8 rounded-full flex items-center justify-center shadow-sm">A</div>
                                    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/10 pointer-events-none"></div>
                                    <img src="<?php echo $question['option_a']; ?>" alt="Option A" class="w-full h-48 object-cover">
                                    <div class="p-3 bg-gray-50">
                                        <div class="flex justify-between items-center">
                                            <span class="font-medium text-gray-700">Option A</span>
                                            <span class="result-icon hidden"></span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <!-- option B -->
                            <label class="cursor-pointer">
                                <input type="radio" name="answer_<?php echo $question['id']; ?>" value="B" class="hidden peer" 
                                       data-correct-option="<?php echo $question['correct_option']; ?>" 
                                       data-question-num="<?php echo $index + 1; ?>"
                                       onclick="updateSelection(this)">
                                <div class="relative border-4 border-gray-200 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 peer-checked:border-primary-500 peer-checked:shadow-md peer-checked:shadow-primary-200 group">
                                    <div class="absolute top-3 left-3 z-10 bg-white/90 text-primary-600 font-bold w-8 h-8 rounded-full flex items-center justify-center shadow-sm">B</div>
                                    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/10 pointer-events-none"></div>
                                    <img src="<?php echo $question['option_b']; ?>" alt="Option B" class="w-full h-48 object-cover">
                                    <div class="p-3 bg-gray-50">
                                        <div class="flex justify-between items-center">
                                            <span class="font-medium text-gray-700">Option B</span>
                                            <span class="result-icon hidden"></span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <!-- option C -->
                            <label class="cursor-pointer">
                                <input type="radio" name="answer_<?php echo $question['id']; ?>" value="C" class="hidden peer" 
                                       data-correct-option="<?php echo $question['correct_option']; ?>" 
                                       data-question-num="<?php echo $index + 1; ?>"
                                       onclick="updateSelection(this)">
                                <div class="relative border-4 border-gray-200 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 peer-checked:border-primary-500 peer-checked:shadow-md peer-checked:shadow-primary-200 group">
                                    <div class="absolute top-3 left-3 z-10 bg-white/90 text-primary-600 font-bold w-8 h-8 rounded-full flex items-center justify-center shadow-sm">C</div>
                                    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/10 pointer-events-none"></div>
                                    <img src="<?php echo $question['option_c']; ?>" alt="Option C" class="w-full h-48 object-cover">
                                    <div class="p-3 bg-gray-50">
                                        <div class="flex justify-between items-center">
                                            <span class="font-medium text-gray-700">Option C</span>
                                            <span class="result-icon hidden"></span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <!-- option D -->
                            <label class="cursor-pointer">
                                <input type="radio" name="answer_<?php echo $question['id']; ?>" value="D" class="hidden peer" 
                                       data-correct-option="<?php echo $question['correct_option']; ?>" 
                                       data-question-num="<?php echo $index + 1; ?>"
                                       onclick="updateSelection(this)">
                                <div class="relative border-4 border-gray-200 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 peer-checked:border-primary-500 peer-checked:shadow-md peer-checked:shadow-primary-200 group">
                                    <div class="absolute top-3 left-3 z-10 bg-white/90 text-primary-600 font-bold w-8 h-8 rounded-full flex items-center justify-center shadow-sm">D</div>
                                    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/10 pointer-events-none"></div>
                                    <img src="<?php echo $question['option_d']; ?>" alt="Option D" class="w-full h-48 object-cover">
                                    <div class="p-3 bg-gray-50">
                                        <div class="flex justify-between items-center">
                                            <span class="font-medium text-gray-700">Option D</span>
                                            <span class="result-icon hidden"></span>
                                        </div>
                                    </div>
                                </div>
                            </label>

                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Quiz Navigation -->
                    <div class="flex justify-between items-center mt-8">
                        <div class="flex gap-2">
                            <button type="button" id="prev-btn" onclick="prevQuestion()" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-arrow-left"></i>
                                <span>Previous</span>
                            </button>
                            
                            <button type="button" id="next-btn" onclick="nextQuestion()" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span>Next</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                        
                        <div class="flex items-center">
                            <span id="question-counter" class="text-sm text-gray-600 mr-4">Question 1 of <?php echo count($quizQuestions); ?></span>
                            
                            <button type="button" onclick="submitQuiz()" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-2 rounded-xl font-medium hover:from-primary-700 hover:to-primary-800 transition shadow-lg hover:shadow-xl flex items-center gap-2">
                                <span>Submit Quiz</span>
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <div id="quizResult" class="mt-10 hidden">
                    <div class="bg-gradient-to-r from-primary-50 to-secondary-50 rounded-xl p-8 text-center transform transition-all duration-500">
                        <div id="result-icon" class="text-5xl mb-4 mx-auto w-20 h-20 rounded-full flex items-center justify-center"></div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Your Results</h3>
                        <p id="resultMessage" class="text-lg text-gray-600 mb-4"></p>
                        
                        <div class="w-full max-w-md mx-auto mb-6">
                            <div class="bg-white rounded-full h-6 shadow-inner overflow-hidden">
                                <div id="score-bar" class="h-full rounded-full transition-all duration-1000 ease-out" style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-6">
                            <button onclick="closeQuizModal(); resetQuiz();" class="bg-white text-primary-600 border border-primary-200 px-6 py-2 rounded-lg transition-colors duration-300 hover:bg-primary-50 flex items-center justify-center gap-2">
                                <i class="fas fa-redo"></i>
                                <span>Try Again</span>
                            </button>
                            <button onclick="closeQuizModal()" class="bg-primary-600 text-white px-6 py-2 rounded-lg transition-colors duration-300 hover:bg-primary-700 flex items-center justify-center gap-2">
                                <i class="fas fa-times"></i>
                                <span>Close</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- View Question Modal -->
    <div id="viewQuestionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden">
            <div class="bg-gradient-to-r from-secondary-600 to-primary-600 py-4 px-6 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Question Details</h3>
                <button onclick="closeViewModal()" class="text-white hover:text-gray-200 focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-6" id="question-details">
                <!-- Question details will be loaded here via AJAX -->
                <div class="animate-pulse">
                    <div class="h-6 bg-gray-200 rounded w-3/4 mb-4"></div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="h-48 bg-gray-200 rounded"></div>
                        <div class="h-48 bg-gray-200 rounded"></div>
                        <div class="h-48 bg-gray-200 rounded"></div>
                        <div class="h-48 bg-gray-200 rounded"></div>
                    </div>
                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Track answered questions with a Set to avoid duplicates
        const answeredQuestionsSet = new Set();
        const totalQuestions = <?php echo count($quizQuestions); ?>;
        let currentQuestion = 1;
        
        // Function to open quiz modal
        function openQuizModal() {
            document.getElementById('quizModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
            
            // Reset quiz if needed
            resetQuiz();
        }
        
        // Function to close quiz modal
        function closeQuizModal() {
            document.getElementById('quizModal').classList.add('hidden');
            document.body.style.overflow = 'auto'; // Re-enable scrolling
        }
        
        // Function to reset quiz
        function resetQuiz() {
            // Hide results, show questions
            document.getElementById('quizResult').classList.add('hidden');
            document.querySelectorAll('.question-container').forEach((container, index) => {
                container.classList.add('hidden');
                if (index === 0) container.classList.remove('hidden');
            });
            
            // Reset form
            document.getElementById('quizForm').reset();
            
            // Reset progress
            answeredQuestionsSet.clear();
            updateProgress();
            
            // Reset current question
            currentQuestion = 1;
            updateNavButtons();
            document.getElementById('question-counter').textContent = `Question 1 of ${totalQuestions}`;
            
            // Reset result icons
            document.querySelectorAll('.result-icon').forEach(icon => {
                icon.classList.add('hidden');
                icon.innerHTML = '';
            });
            
            // Reset card styling
            document.querySelectorAll('.option-card').forEach(card => {
                card.classList.remove('border-green-500', 'border-red-500', 'shadow-green-200', 'shadow-red-200');
            });
            
            // Reset explanations
            document.querySelectorAll('.explanation-box').forEach(box => {
                box.classList.add('hidden');
            });
        }
        
        // Function to update selection and progress
        function updateSelection(selectedInput) {
            // Get the question number
            const questionNum = selectedInput.getAttribute('data-question-num');
            
            // Update progress only if this question wasn't answered before
            if (!answeredQuestionsSet.has(questionNum)) {
                answeredQuestionsSet.add(questionNum);
                updateProgress();
            }
        }
        
        // Update progress bar
        function updateProgress() {
            const progressPercent = Math.round((answeredQuestionsSet.size / totalQuestions) * 100);
            document.getElementById('progress-value').style.width = `${progressPercent}%`;
            document.getElementById('progress-text').textContent = `${progressPercent}%`;
        }
        
        // Function to go to next question
        function nextQuestion() {
            if (currentQuestion < totalQuestions) {
                document.querySelector(`.question-container[data-question="${currentQuestion}"]`).classList.add('hidden');
                currentQuestion++;
                document.querySelector(`.question-container[data-question="${currentQuestion}"]`).classList.remove('hidden');
                
                updateNavButtons();
                document.getElementById('question-counter').textContent = `Question ${currentQuestion} of ${totalQuestions}`;
            }
        }
        
        // Function to go to previous question
        function prevQuestion() {
            if (currentQuestion > 1) {
                document.querySelector(`.question-container[data-question="${currentQuestion}"]`).classList.add('hidden');
                currentQuestion--;
                document.querySelector(`.question-container[data-question="${currentQuestion}"]`).classList.remove('hidden');
                
                updateNavButtons();
                document.getElementById('question-counter').textContent = `Question ${currentQuestion} of ${totalQuestions}`;
            }
        }
        
        // Function to update navigation buttons
        function updateNavButtons() {
            document.getElementById('prev-btn').disabled = currentQuestion === 1;
            document.getElementById('next-btn').disabled = currentQuestion === totalQuestions;
        }
        
        // Function to submit the quiz and display the correct/incorrect answers
        function submitQuiz() {
            const allQuestions = document.querySelectorAll('[name^="answer_"]');
            let correctCount = 0;
            let totalAnswered = 0;
            let questionsMap = new Map();
            
            allQuestions.forEach(input => {
                const correctOption = input.getAttribute('data-correct-option');
                const selectedCard = input.nextElementSibling;
                const resultIcon = selectedCard.querySelector('.result-icon');
                
                if (input.checked) {
                    totalAnswered++;
                    resultIcon.classList.remove('hidden');
                    
                    if (input.value === correctOption) {
                        selectedCard.classList.remove('peer-checked:border-primary-500');
                        selectedCard.classList.add('border-green-500', 'shadow-green-200');
                        resultIcon.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
                        correctCount++;
                    } else {
                        selectedCard.classList.remove('peer-checked:border-primary-500');
                        selectedCard.classList.add('border-red-500', 'shadow-red-200');
                        resultIcon.innerHTML = '<i class="fas fa-times-circle text-red-500"></i>';
                        
                        // Highlight the correct answer
                        const questionName = input.name;
                        const correctInput = document.querySelector(`input[name="${questionName}"][value="${correctOption}"]`);
                        const correctCard = correctInput.nextElementSibling;
                        const correctIcon = correctCard.querySelector('.result-icon');
                        
                        correctCard.classList.add('border-green-500', 'shadow-green-200');
                        correctIcon.classList.remove('hidden');
                        correctIcon.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
                    }
                    
                    // Show explanation
                    const questionContainer = selectedCard.closest('.question-container');
                    const explanationBox = questionContainer.querySelector('.explanation-box');
                    if (explanationBox) {
                        explanationBox.classList.remove('hidden');
                    }
                    
                    // Add to questions map to track which ones were answered
                    const questionNum = input.getAttribute('data-question-num');
                    questionsMap.set(questionNum, true);
                }
            });
            
            // Check if all questions are answered
            if (totalAnswered < totalQuestions) {
                // Find first unanswered question
                for (let i = 1; i <= totalQuestions; i++) {
                    if (!questionsMap.has(i.toString())) {
                        // Show the unanswered question
                        document.querySelectorAll('.question-container').forEach(container => {
                            container.classList.add('hidden');
                        });
                        document.querySelector(`.question-container[data-question="${i}"]`).classList.remove('hidden');
                        currentQuestion = i;
                        updateNavButtons();
                        document.getElementById('question-counter').textContent = `Question ${i} of ${totalQuestions}`;
                        
                        // Highlight it
                        document.querySelector(`.question-container[data-question="${i}"]`).classList.add('border-l-4', 'border-yellow-400', 'pl-3');
                        
                        alert(`Please answer all questions. ${totalQuestions - totalAnswered} question(s) remaining.`);
                        return;
                    }
                }
            }
            
            // Show results
            const resultMessage = document.getElementById('resultMessage');
            const quizResult = document.getElementById('quizResult');
            const scoreBar = document.getElementById('score-bar');
            const resultIcon = document.getElementById('result-icon');
            const score = (correctCount / totalQuestions) * 100;
            
            // Hide questions, show results
            document.querySelectorAll('.question-container').forEach(container => {
                container.classList.add('hidden');
            });
            quizResult.classList.remove('hidden');
            
            // Set result message and styling based on score
            if (score >= 80) {
                resultIcon.className = 'text-5xl mb-4 mx-auto w-20 h-20 rounded-full flex items-center justify-center bg-green-100 text-green-500';
                resultIcon.innerHTML = '<i class="fas fa-trophy"></i>';
                resultMessage.innerHTML = `<span class="font-bold">Excellent!</span> You got <span class="text-green-500 font-bold">${correctCount}</span> out of <span class="font-bold">${totalQuestions}</span> correct!<br>Your score: <span class="text-green-500 font-bold">${score.toFixed(0)}%</span>`;
                scoreBar.className = 'h-full rounded-full transition-all duration-1000 ease-out bg-gradient-to-r from-green-400 to-green-500';
                createConfetti();
            } else if (score >= 60) {
                resultIcon.className = 'text-5xl mb-4 mx-auto w-20 h-20 rounded-full flex items-center justify-center bg-blue-100 text-blue-500';
                resultIcon.innerHTML = '<i class="fas fa-medal"></i>';
                resultMessage.innerHTML = `<span class="font-bold">Good job!</span> You got <span class="text-blue-500 font-bold">${correctCount}</span> out of <span class="font-bold">${totalQuestions}</span> correct!<br>Your score: <span class="text-blue-500 font-bold">${score.toFixed(0)}%</span>`;
                scoreBar.className = 'h-full rounded-full transition-all duration-1000 ease-out bg-gradient-to-r from-blue-400 to-blue-500';
            } else {
                resultIcon.className = 'text-5xl mb-4 mx-auto w-20 h-20 rounded-full flex items-center justify-center bg-yellow-100 text-yellow-500';
                resultIcon.innerHTML = '<i class="fas fa-star-half-alt"></i>';
                resultMessage.innerHTML = `<span class="font-bold">Keep practicing!</span> You got <span class="text-yellow-500 font-bold">${correctCount}</span> out of <span class="font-bold">${totalQuestions}</span> correct!<br>Your score: <span class="text-yellow-500 font-bold">${score.toFixed(0)}%</span>`;
                scoreBar.className = 'h-full rounded-full transition-all duration-1000 ease-out bg-gradient-to-r from-yellow-400 to-yellow-500';
            }
            
            // Animate score bar
            setTimeout(() => {
                scoreBar.style.width = `${score}%`;
            }, 100);
        }
        
        // Function to create confetti effect
        function createConfetti() {
            const colors = ['#0ea5e9', '#14b8a6', '#f59e0b', '#ef4444', '#8b5cf6'];
            
            for (let i = 0; i < 100; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = `${Math.random() * 100}vw`;
                confetti.style.top = `-10px`;
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.width = `${Math.random() * 10 + 5}px`;
                confetti.style.height = `${Math.random() * 10 + 5}px`;
                confetti.style.animationDuration = `${Math.random() * 3 + 2}s`;
                confetti.style.animationDelay = `${Math.random() * 2}s`;
                
                document.body.appendChild(confetti);
                
                // Remove confetti after animation
                setTimeout(() => {
                    confetti.remove();
                }, 5000);
            }
        }
        
        // Functions for question management (these would connect to backend APIs)
        function viewQuestion(id) {
            document.getElementById('viewQuestionModal').classList.remove('hidden');
            document.getElementById('question-details').innerHTML = `<p class="text-center">Loading question details...</p>`;
            
            // In a real application, you'd fetch the question details via AJAX
            // For now, we'll simulate this with a delay
            setTimeout(() => {
                // Find the question in the page
                const questionElement = document.querySelector(`input[name="answer_${id}"]`).closest('.question-container');
                const question = questionElement.querySelector('h3').textContent;
                const optionImages = questionElement.querySelectorAll('img');
                
                let html = `
                    <h4 class="text-xl font-semibold text-gray-800 mb-6">${question}</h4>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                `;
                
                optionImages.forEach((img, index) => {
                    const optionLetter = String.fromCharCode(65 + index); // A, B, C, D
                    html += `
                        <div class="relative border-4 border-gray-200 rounded-xl overflow-hidden">
                            <div class="absolute top-3 left-3 z-10 bg-white/90 text-primary-600 font-bold w-8 h-8 rounded-full flex items-center justify-center shadow-sm">${optionLetter}</div>
                            <img src="${img.src}" alt="Option ${optionLetter}" class="w-full h-48 object-cover">
                        </div>
                    `;
                });
                
                html += `</div>`; 
                document.getElementById('question-details').innerHTML = html;
            }, 500);
        }
        
        function closeViewModal() {
            document.getElementById('viewQuestionModal').classList.add('hidden');
        }
        
        function editQuestion(id) {
            // In a real app, this would redirect to an edit page or open an edit modal
            alert(`Edit question ${id} - This would open an edit form in a full implementation`);
        }
        
        function deleteQuestion(id) {
            if (confirm(`Are you sure you want to delete question #${id}? This action cannot be undone.`)) {
                // In a real app, this would send an AJAX request to delete the question
                alert(`Delete question ${id} - This would delete the question in a full implementation`);
            }
        }
        
        // Initialize navigation buttons on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateNavButtons();
            quizResult.classList.remove('hidden');
        });
    </script>
</body>
</html>