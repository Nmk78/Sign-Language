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
    $correct_option = $_POST['correct_option'] ?? '';
    
    // Validate form data
    if (empty($question) || empty($correct_option)) {
        $error_message = "Question and correct answer are required.";
    } else {
        // Define upload directory
        $upload_dir = "uploads/quiz_images/";
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Initialize option paths
        $option_paths = ['', '', '', ''];
        $upload_success = true;
        
        // Process each file upload
        for ($i = 0; $i < 4; $i++) {
            $option_letter = chr(65 + $i); // A, B, C, D
            $file_key = "option_" . strtolower($option_letter);
            
            // Check if file was uploaded
            if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] == 0) {
                $file_tmp = $_FILES[$file_key]['tmp_name'];
                $file_name = $_FILES[$file_key]['name'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                // Validate file extension
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($file_ext, $allowed_extensions)) {
                    // Generate unique filename
                    $new_file_name = "option_" . strtolower($option_letter) . "_" . time() . "_" . rand(1000, 9999) . "." . $file_ext;
                    $file_path = $upload_dir . $new_file_name;
                    
                    // Move uploaded file
                    if (move_uploaded_file($file_tmp, $file_path)) {
                        $option_paths[$i] = $file_path;
                    } else {
                        $error_message = "Failed to upload image for Option $option_letter.";
                        $upload_success = false;
                        break;
                    }
                } else {
                    $error_message = "Invalid file format for Option $option_letter. Allowed formats: JPG, JPEG, PNG, GIF.";
                    $upload_success = false;
                    break;
                }
            } else {
                $error_message = "Please upload an image for Option $option_letter.";
                $upload_success = false;
                break;
            }
        }
        
        // If all files uploaded successfully, insert into database
        if ($upload_success) {
            // Insert new quiz question
            $sql = "INSERT INTO quizzes (lesson_id, question, option_a, option_b, option_c, option_d, correct_option) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issssss", $lessonId, $question, $option_paths[0], $option_paths[1], $option_paths[2], $option_paths[3], $correct_option);
            
            if ($stmt->execute()) {
                $success_message = "Quiz question created successfully!";
            } else {
                $error_message = "Error creating quiz question: " . $conn->error;
            }
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
        
        /* Confetti animation */
        @keyframes confetti-fall {
            0% { transform: translateY(-10px) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
        }
        .confetti {
            position: fixed;
            z-index: 1000;
            animation: confetti-fall 5s ease-out forwards;
            border-radius: 2px;
        }
        
        /* File input styling */
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            cursor: pointer;
        }
        
        .file-input-wrapper input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-preview {
            width: 100%;
            height: 150px;
            border-radius: 0.5rem;
            object-fit: cover;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .file-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
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

    <main class="py-8">
        <!-- Status Messages -->
        <?php if (!empty($success_message)): ?>
            <div id="success-message" class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <p class="text-green-700"><?php echo $success_message; ?></p>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div id="error-message" class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
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
                <!-- Modified form to accept file uploads -->
                <form method="POST" action="" enctype="multipart/form-data" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                        <div class="space-y-4">
                            <label for="question" class="block text-sm font-medium text-gray-700 mb-1">Question</label>
                            <input type="text" id="question" name="question" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        
                        <div class="space-y-6">
                            <h3 class="text-lg font-medium text-gray-900">Upload Option Images</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Option A File Upload -->
                                <div>
                                    <label for="option_a" class="block text-sm font-medium text-gray-700 mb-2">Option A Image</label>
                                    <div class="space-y-2">
                                        <div class="file-preview" id="preview_a">
                                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                                        </div>
                                        <div class="file-input-wrapper w-full">
                                            <button type="button" class="w-full bg-gray-50 border border-gray-300 rounded-lg py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 transition flex items-center justify-center">
                                                <i class="fas fa-upload mr-2"></i>
                                                <span>Choose Image</span>
                                            </button>
                                            <input type="file" id="option_a" name="option_a" accept="image/*" required onchange="previewImage(this, 'preview_a')">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Option B File Upload -->
                                <div>
                                    <label for="option_b" class="block text-sm font-medium text-gray-700 mb-2">Option B Image</label>
                                    <div class="space-y-2">
                                        <div class="file-preview" id="preview_b">
                                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                                        </div>
                                        <div class="file-input-wrapper w-full">
                                            <button type="button" class="w-full bg-gray-50 border border-gray-300 rounded-lg py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 transition flex items-center justify-center">
                                                <i class="fas fa-upload mr-2"></i>
                                                <span>Choose Image</span>
                                            </button>
                                            <input type="file" id="option_b" name="option_b" accept="image/*" required onchange="previewImage(this, 'preview_b')">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Option C File Upload -->
                                <div>
                                    <label for="option_c" class="block text-sm font-medium text-gray-700 mb-2">Option C Image</label>
                                    <div class="space-y-2">
                                        <div class="file-preview" id="preview_c">
                                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                                        </div>
                                        <div class="file-input-wrapper w-full">
                                            <button type="button" class="w-full bg-gray-50 border border-gray-300 rounded-lg py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 transition flex items-center justify-center">
                                                <i class="fas fa-upload mr-2"></i>
                                                <span>Choose Image</span>
                                            </button>
                                            <input type="file" id="option_c" name="option_c" accept="image/*" required onchange="previewImage(this, 'preview_c')">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Option D File Upload -->
                                <div>
                                    <label for="option_d" class="block text-sm font-medium text-gray-700 mb-2">Option D Image</label>
                                    <div class="space-y-2">
                                        <div class="file-preview" id="preview_d">
                                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                                        </div>
                                        <div class="file-input-wrapper w-full">
                                            <button type="button" class="w-full bg-gray-50 border border-gray-300 rounded-lg py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 transition flex items-center justify-center">
                                                <i class="fas fa-upload mr-2"></i>
                                                <span>Choose Image</span>
                                            </button>
                                            <input type="file" id="option_d" name="option_d" accept="image/*" required onchange="previewImage(this, 'preview_d')">
                                        </div>
                                    </div>
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
        
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
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
    
    <!-- Quiz Modal (Pop-up) - MODIFIED FOR VERTICAL SCROLLING -->
    <div id="quizModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <div class="bg-gradient-to-r from-primary-600 to-secondary-600 py-5 px-6 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-white">Sign Language Quiz</h2>
                <button onclick="closeQuizModal()" class="text-white hover:text-gray-200 focus:outline-none">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]" id="quiz-content">
                <!-- Quiz Form - All questions visible for scrolling -->
                <div id="quiz-form-container">
                    <div id="quiz-progress" class="mb-8 sticky top-0 bg-white pt-2 pb-4 z-10">
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
                        <div class="mb-10 pb-10 border-b border-gray-200 last:border-b-0 question-container" data-question-id="<?php echo $question['id']; ?>">
                            <div class="flex items-center mb-4">
                                <div class="bg-primary-100 text-primary-700 rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3">
                                    <?php echo $index + 1; ?>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($question['question']); ?></h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Option A -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="answer_<?php echo $question['id']; ?>" value="A" class="hidden answer-input" 
                                           data-correct-option="<?php echo $question['correct_option']; ?>" 
                                           data-question-id="<?php echo $question['id']; ?>"
                                           onclick="updateSelection(this)">
                                    <div class="relative border-4 border-gray-200 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 option-card">
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
                                
                                <!-- Option B -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="answer_<?php echo $question['id']; ?>" value="B" class="hidden answer-input" 
                                           data-correct-option="<?php echo $question['correct_option']; ?>" 
                                           data-question-id="<?php echo $question['id']; ?>"
                                           onclick="updateSelection(this)">
                                    <div class="relative border-4 border-gray-200 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 option-card">
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
                                
                                <!-- Option C -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="answer_<?php echo $question['id']; ?>" value="C" class="hidden answer-input" 
                                           data-correct-option="<?php echo $question['correct_option']; ?>" 
                                           data-question-id="<?php echo $question['id']; ?>"
                                           onclick="updateSelection(this)">
                                    <div class="relative border-4 border-gray-200 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 option-card">
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
                                
                                <!-- Option D -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="answer_<?php echo $question['id']; ?>" value="D" class="hidden answer-input" 
                                           data-correct-option="<?php echo $question['correct_option']; ?>" 
                                           data-question-id="<?php echo $question['id']; ?>"
                                           onclick="updateSelection(this)">
                                    <div class="relative border-4 border-gray-200 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 option-card">
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
                        
                        <!-- Submit Button - Fixed at the bottom -->
                        <div class="mt-8 flex justify-end sticky bottom-0 pt-4 pb-2 bg-white">
                            <button type="button" onclick="submitQuiz()" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-2 rounded-xl font-medium hover:from-primary-700 hover:to-primary-800 transition shadow-lg hover:shadow-xl flex items-center gap-2">
                                <span>Submit Quiz</span>
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Quiz Results - Initially Hidden -->
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
                            <button onclick="resetQuiz()" class="bg-white text-primary-600 border border-primary-200 px-6 py-2 rounded-lg transition-colors duration-300 hover:bg-primary-50 flex items-center justify-center gap-2">
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
                    <div class="grid grid-cols-4 gap-4 mb-4">
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
        
        // Function to reset quiz - FIXED to properly clear all answers
        function resetQuiz() {
            // Hide results, show questions
            document.getElementById('quizResult').classList.add('hidden');
            document.getElementById('quiz-form-container').classList.remove('hidden');
            
            // Reset form
            document.getElementById('quizForm').reset();
            
            // Reset progress
            answeredQuestionsSet.clear();
            updateProgress();
            
            // Reset result icons
            document.querySelectorAll('.result-icon').forEach(icon => {
                icon.classList.add('hidden');
                icon.innerHTML = '';
            });
            
            // Reset card styling
            document.querySelectorAll('.option-card').forEach(card => {
                card.classList.remove('border-green-500', 'border-red-500', 'border-primary-500', 'shadow-green-200', 'shadow-red-200');
                card.classList.add('border-gray-200');
            });
            
            // Reset explanations
            document.querySelectorAll('.explanation-box').forEach(box => {
                box.classList.add('hidden');
            });
            
            // Remove highlighting from unanswered questions
            document.querySelectorAll('.question-container').forEach(container => {
                container.classList.remove('border-l-4', 'border-yellow-400', 'pl-3');
            });
            
            // Scroll back to top
            document.getElementById('quiz-content').scrollTop = 0;
        }
        
        // Function to update selection and progress
        function updateSelection(selectedInput) {
            // Get the question ID
            const questionId = selectedInput.getAttribute('data-question-id');
            
            // Update card styling
            const allOptionsForQuestion = document.querySelectorAll(`input[name="answer_${questionId}"]`);
            allOptionsForQuestion.forEach(input => {
                const card = input.nextElementSibling;
                if (input === selectedInput) {
                    card.classList.add('border-primary-500');
                    card.classList.remove('border-gray-200');
                } else {
                    card.classList.remove('border-primary-500');
                    card.classList.add('border-gray-200');
                }
            });
            
            // Update progress only if this question wasn't answered before
            if (!answeredQuestionsSet.has(questionId)) {
                answeredQuestionsSet.add(questionId);
                updateProgress();
            }
        }
        
        // Update progress bar
        function updateProgress() {
            const progressPercent = Math.round((answeredQuestionsSet.size / totalQuestions) * 100);
            document.getElementById('progress-value').style.width = `${progressPercent}%`;
            document.getElementById('progress-text').textContent = `${progressPercent}%`;
        }
        
        // Function to submit the quiz and display the correct/incorrect answers
        function submitQuiz() {
            const allQuestions = document.querySelectorAll('.question-container');
            let correctCount = 0;
            let totalAnswered = 0;
            let unansweredQuestions = [];
            
            allQuestions.forEach(questionContainer => {
                const questionId = questionContainer.getAttribute('data-question-id');
                const selectedInput = document.querySelector(`input[name="answer_${questionId}"]:checked`);
                
                if (selectedInput) {
                    totalAnswered++;
                    const correctOption = selectedInput.getAttribute('data-correct-option');
                    const selectedCard = selectedInput.nextElementSibling;
                    const resultIcon = selectedCard.querySelector('.result-icon');
                    
                    resultIcon.classList.remove('hidden');
                    
                    if (selectedInput.value === correctOption) {
                        selectedCard.classList.remove('border-primary-500', 'border-gray-200');
                        selectedCard.classList.add('border-green-500', 'shadow-green-200');
                        resultIcon.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
                        correctCount++;
                    } else {
                        selectedCard.classList.remove('border-primary-500', 'border-gray-200');
                        selectedCard.classList.add('border-red-500', 'shadow-red-200');
                        resultIcon.innerHTML = '<i class="fas fa-times-circle text-red-500"></i>';
                        
                        // Highlight the correct answer
                        const correctInput = document.querySelector(`input[name="answer_${questionId}"][value="${correctOption}"]`);
                        const correctCard = correctInput.nextElementSibling;
                        const correctIcon = correctCard.querySelector('.result-icon');
                        
                        correctCard.classList.add('border-green-500', 'shadow-green-200');
                        correctCard.classList.remove('border-gray-200');
                        correctIcon.classList.remove('hidden');
                        correctIcon.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
                    }
                } else {
                    unansweredQuestions.push(questionId);
                }
            });
            
            // Check if all questions are answered
            if (totalAnswered < totalQuestions) {
                // Highlight unanswered questions
                unansweredQuestions.forEach(questionId => {
                    const questionContainer = document.querySelector(`.question-container[data-question-id="${questionId}"]`);
                    questionContainer.classList.add('border-l-4', 'border-yellow-400', 'pl-3');
                    
                    // Scroll to first unanswered question
                    if (questionId === unansweredQuestions[0]) {
                        questionContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
                
                alert(`Please answer all questions. ${totalQuestions - totalAnswered} question(s) remaining.`);
                return;
            }
            
            // Show results
            document.getElementById('quiz-form-container').classList.add('hidden');
            const quizResult = document.getElementById('quizResult');
            quizResult.classList.remove('hidden');
            
            const resultIcon = document.getElementById('result-icon');
            const resultMessage = document.getElementById('resultMessage');
            const scoreBar = document.getElementById('score-bar');
            const score = (correctCount / totalQuestions) * 100;
            
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
        
        // Function to preview uploaded images
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-contain">`;
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.innerHTML = `<i class="fas fa-image text-gray-400 text-4xl"></i>`;
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
        // Function to hide messages after 5 seconds (5000ms)
        setTimeout(() => {
            const successMsg = document.getElementById("success-message");
            const errorMsg = document.getElementById("error-message");

            if (successMsg) successMsg.style.display = "none";
            if (errorMsg) errorMsg.style.display = "none";
        }, 5000);
    </script>
</body>
</html>

