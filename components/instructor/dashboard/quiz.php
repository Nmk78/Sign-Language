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

// Get lesson information
$lessonQuery = "SELECT title, description FROM lesson WHERE id = ?";
$lessonStmt = $conn->prepare($lessonQuery);
$lessonStmt->bind_param("i", $lessonId);
$lessonStmt->execute();
$lessonResult = $lessonStmt->get_result();
$lessonInfo = $lessonResult->fetch_assoc();

// Close connection
$lessonStmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Sign Language Quiz</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#f5f5fd',
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
<body class="bg-background min-h-screen">
    <!-- Header Section -->
    <header class="bg-surface shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-text flex items-center">
                <i class="fas fa-hands text-primary mr-3"></i>
                Create Sign Language Quiz
            </h1>
            <div class="flex space-x-4">
                <a href="components\instructor\dashboard\quiz-manage.php?lesson_id=<?php echo $lessonId; ?>" class="bg-secondary hover:bg-opacity-90 text-white px-4 py-2 rounded-lg shadow transition duration-200 flex items-center">
                    <i class="fas fa-list mr-2"></i>
                    Manage Quizzes
                </a>
            </div>
        </div>
    </header>

    <main class=" py-8">
        <!-- Status Messages -->
        <?php if (!empty($success_message)): ?>
            <div id="success-message" class="bg-success bg-opacity-10 border-l-4 border-success p-4 mb-6 rounded-md">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-success mr-2"></i>
                    <p class="text-success"><?php echo $success_message; ?></p>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div id="error-message" class="bg-error bg-opacity-10 border-l-4 border-error p-4 mb-6 rounded-md">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-error mr-2"></i>
                    <p class="text-error"><?php echo $error_message; ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Create Quiz Section -->
        <div class="bg-surface rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-primary to-primary-dark py-5 px-6">
                <h2 class="text-2xl font-bold text-white">Create New Sign Language Quiz Question</h2>
                <p class="text-white text-opacity-80 mt-1">Add new questions to the <?php echo htmlspecialchars($lessonInfo['title'] ?? 'sign language') ?> quiz</p>
            </div>
            
            <div class="p-6">
                <!-- Form to accept file uploads -->
                <form method="POST" action="" enctype="multipart/form-data" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                        <div class="space-y-4">
                            <label for="question" class="block text-sm font-medium text-text mb-1">Question</label>
                            <input type="text" id="question" name="question" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div class="space-y-6">
                            <h3 class="text-lg font-medium text-text">Upload Option Images</h3>
                            <div class="grid grid-cols-4 gap-6">
                                <!-- Option A File Upload -->
                                <div>
                                    <label for="option_a" class="block text-sm font-medium text-text mb-2">Option A Image</label>
                                    <div class="space-y-2">
                                        <div id="preview_a" class="w-full h-[150px] rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden">
                                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                                        </div>
                                        <div class="relative w-full">
                                            <button type="button" class="w-full bg-gray-50 border border-gray-300 rounded-lg py-2 px-4 text-sm text-text-light hover:bg-gray-100 transition flex items-center justify-center">
                                                <i class="fas fa-upload mr-2"></i>
                                                <span>Choose Image</span>
                                            </button>
                                            <input type="file" id="option_a" name="option_a" accept="image/*" required onchange="previewImage(this, 'preview_a')"
                                                   class="absolute inset-0 opacity-0 w-full h-full cursor-pointer">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Option B File Upload -->
                                <div>
                                    <label for="option_b" class="block text-sm font-medium text-text mb-2">Option B Image</label>
                                    <div class="space-y-2">
                                        <div id="preview_b" class="w-full h-[150px] rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden">
                                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                                        </div>
                                        <div class="relative w-full">
                                            <button type="button" class="w-full bg-gray-50 border border-gray-300 rounded-lg py-2 px-4 text-sm text-text-light hover:bg-gray-100 transition flex items-center justify-center">
                                                <i class="fas fa-upload mr-2"></i>
                                                <span>Choose Image</span>
                                            </button>
                                            <input type="file" id="option_b" name="option_b" accept="image/*" required onchange="previewImage(this, 'preview_b')"
                                                   class="absolute inset-0 opacity-0 w-full h-full cursor-pointer">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Option C File Upload -->
                                <div>
                                    <label for="option_c" class="block text-sm font-medium text-text mb-2">Option C Image</label>
                                    <div class="space-y-2">
                                        <div id="preview_c" class="w-full h-[150px] rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden">
                                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                                        </div>
                                        <div class="relative w-full">
                                            <button type="button" class="w-full bg-gray-50 border border-gray-300 rounded-lg py-2 px-4 text-sm text-text-light hover:bg-gray-100 transition flex items-center justify-center">
                                                <i class="fas fa-upload mr-2"></i>
                                                <span>Choose Image</span>
                                            </button>
                                            <input type="file" id="option_c" name="option_c" accept="image/*" required onchange="previewImage(this, 'preview_c')"
                                                   class="absolute inset-0 opacity-0 w-full h-full cursor-pointer">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Option D File Upload -->
                                <div>
                                    <label for="option_d" class="block text-sm font-medium text-text mb-2">Option D Image</label>
                                    <div class="space-y-2">
                                        <div id="preview_d" class="w-full h-[150px] rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden">
                                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                                        </div>
                                        <div class="relative w-full">
                                            <button type="button" class="w-full bg-gray-50 border border-gray-300 rounded-lg py-2 px-4 text-sm text-text-light hover:bg-gray-100 transition flex items-center justify-center">
                                                <i class="fas fa-upload mr-2"></i>
                                                <span>Choose Image</span>
                                            </button>
                                            <input type="file" id="option_d" name="option_d" accept="image/*" required onchange="previewImage(this, 'preview_d')"
                                                   class="absolute inset-0 opacity-0 w-full h-full cursor-pointer">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-text mb-3">Correct Answer</label>
                                <div class="flex flex-wrap items-center gap-6">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="correct_option" value="A" checked
                                               class="w-5 h-5 text-primary focus:ring-primary">
                                        <span class="ml-2 text-text">Option A</span>
                                    </label>
                                    
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="correct_option" value="B"
                                               class="w-5 h-5 text-primary focus:ring-primary">
                                        <span class="ml-2 text-text">Option B</span>
                                    </label>
                                    
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="correct_option" value="C"
                                               class="w-5 h-5 text-primary focus:ring-primary">
                                        <span class="ml-2 text-text">Option C</span>
                                    </label>
                                    
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="correct_option" value="D"
                                               class="w-5 h-5 text-primary focus:ring-primary">
                                        <span class="ml-2 text-text">Option D</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" name="create_quiz" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl flex items-center gap-2">
                            <i class="fas fa-plus-circle"></i>
                            <span>Create Quiz Question</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
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
