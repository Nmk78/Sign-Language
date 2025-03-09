<?php
// Initialize variables
$success_message = '';
$error_message = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if form was submitted
    if (isset($_POST['submit_quiz'])) {
        // Create directory for quiz images if it doesn't exist
        $upload_dir = 'uploads/quizzes/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Get quiz title and description
        $quiz_title = htmlspecialchars($_POST['quiz_title']);
        $quiz_description = htmlspecialchars($_POST['quiz_description']);
        
        // Generate unique quiz ID
        $quiz_id = uniqid('quiz_');
        
        // Initialize quiz data array
        $quiz_data = [
            'id' => $quiz_id,
            'title' => $quiz_title,
            'description' => $quiz_description,
            'questions' => []
        ];
        
        // Process each question
        $question_count = count($_POST['question_text']);
        $has_errors = false;
        
        for ($i = 0; $i < $question_count; $i++) {
            $question_text = htmlspecialchars($_POST['question_text'][$i]);
            $correct_option = $_POST['correct_option'][$i];
            $options = [];
            
            // Process each option (3 image options per question)
            for ($j = 0; $j < 3; $j++) {
                $option_key = "question_{$i}_option_{$j}";
                
                // Check if new image was uploaded
                if (!empty($_FILES[$option_key]['name'])) {
                    $file_name = $_FILES[$option_key]['name'];
                    $file_tmp = $_FILES[$option_key]['tmp_name'];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    
                    // Validate file extension
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                    if (!in_array($file_ext, $allowed_extensions)) {
                        $error_message = "Error: Only JPG, JPEG, PNG & GIF files are allowed for options.";
                        $has_errors = true;
                        break 2; // Break out of both loops
                    }
                    
                    // Generate unique filename
                    $new_filename = $quiz_id . '_q' . $i . '_opt' . $j . '.' . $file_ext;
                    $upload_path = $upload_dir . $new_filename;
                    
                    // Upload file
                    if (move_uploaded_file($file_tmp, $upload_path)) {
                        $options[$j] = $upload_path;
                    } else {
                        $error_message = "Error uploading option image for question " . ($i + 1) . ", option " . ($j + 1);
                        $has_errors = true;
                        break 2; // Break out of both loops
                    }
                } else {
                    // Check if using existing image URL
                    if (!empty($_POST["question_{$i}_option_{$j}_url"])) {
                        $options[$j] = htmlspecialchars($_POST["question_{$i}_option_{$j}_url"]);
                    } else {
                        $error_message = "Error: Missing image for question " . ($i + 1) . ", option " . ($j + 1);
                        $has_errors = true;
                        break 2; // Break out of both loops
                    }
                }
            }
            
            // Add question data to quiz
            $quiz_data['questions'][] = [
                'text' => $question_text,
                'options' => $options,
                'correct_option' => intval($correct_option)
            ];
        }
        
        // Save quiz data if no errors
        if (!$has_errors) {
            // In a real application, you would save to a database
            // For this example, we'll save to a JSON file
            $json_data = json_encode($quiz_data, JSON_PRETTY_PRINT);
            $quiz_file = $upload_dir . $quiz_id . '.json';
            
            if (file_put_contents($quiz_file, $json_data)) {
                $success_message = "Quiz created successfully! Quiz ID: $quiz_id";
            } else {
                $error_message = "Error saving quiz data.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Sign Language Quiz</title>
    <!-- Tailwind CSS from CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Inter font from Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <script>
        tailwind.config = {
            theme: {
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                },
                extend: {
                    colors: {
                        primary: '#6366F1', // Indigo
                        secondary: '#F0F4F8',
                        dark: '#111827',
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%);
        }
        .image-preview {
            aspect-ratio: 4/3;
            object-fit: cover;
        }
    </style>
</head>
<body class="bg-gray-50 text-dark min-h-screen font-sans">


    <main class="container mx-auto px-4 py-8">
        <?php if (!empty($success_message)): ?>
            <div class="mb-8 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline"><?php echo $success_message; ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="mb-8 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline"><?php echo $error_message; ?></span>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Quiz Information</h2>
            <p class="text-gray-600 mb-6">Create a new quiz with image-based options for sign language learning.</p>
            
           
   
            <!-- Quiz Details -->
                <div class="mb-8 grid gap-6 md:grid-cols-2">
                    <div class="col-span-2 md:col-span-1">
                        <label for="quiz_title" class="block text-sm font-medium text-gray-700 mb-1">Quiz Title</label>
                        <input type="text" id="quiz_title" name="quiz_title" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                    </div>
                    
                    <div class="col-span-2 md:col-span-1">
                        <label for="quiz_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <input type="text" id="quiz_description" name="quiz_description" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                    </div>
                </div>
                
                <!-- Questions Container -->
                <div id="questionsContainer">
                    <!-- First question is added by default -->
                    <div class="question-block mb-12 p-6 border border-gray-200 rounded-lg bg-gray-50">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Question 1</h3>
                            <button type="button" class="remove-question text-red-500 hover:text-red-700" data-question="0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>
                            </button>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Question Text</label>
                            <input type="text" name="question_text[0]" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                placeholder="e.g., What sign represents 'Hello'?">
                        </div>
                        
                        <div class="mb-4">
                            <p class="block text-sm font-medium text-gray-700 mb-3">Options (Select 3 Images)</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Option 1 -->
                                <div class="option-block p-4 border border-gray-200 rounded-lg bg-white">
                                    <div class="mb-2 flex items-center">
                                        <input type="radio" id="correct_0_0" name="correct_option[0]" value="0" required
                                            class="w-4 h-4 text-primary focus:ring-primary">
                                        <label for="correct_0_0" class="ml-2 text-sm font-medium text-gray-700">
                                            Correct Answer
                                        </label>
                                    </div>
                                    
                                    <div class="mb-2 bg-gray-100 rounded-lg overflow-hidden">
                                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 300 200' %3E%3Crect fill='%23cccccc' width='300' height='200'/%3E%3Ctext fill='%23666666' font-family='sans-serif' font-size='24' text-anchor='middle' x='150' y='110'%3EOption 1%3C/text%3E%3C/svg%3E"
                                            class="image-preview w-full h-32 object-cover" id="preview_0_0">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Upload Image</label>
                                        <input type="file" name="question_0_option_0" accept="image/*"
                                            class="option-image block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90"
                                            onchange="previewImage(this, 'preview_0_0')">
                                    </div>
                                    
                                    <div class="mt-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Or Image URL</label>
                                        <input type="url" name="question_0_option_0_url" placeholder="https://example.com/image.jpg"
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                            onchange="previewImageURL(this, 'preview_0_0')">
                                    </div>
                                </div>
                                
                                <!-- Option 2 -->
                                <div class="option-block p-4 border border-gray-200 rounded-lg bg-white">
                                    <div class="mb-2 flex items-center">
                                        <input type="radio" id="correct_0_1" name="correct_option[0]" value="1" 
                                            class="w-4 h-4 text-primary focus:ring-primary">
                                        <label for="correct_0_1" class="ml-2 text-sm font-medium text-gray-700">
                                            Correct Answer
                                        </label>
                                    </div>
                                    
                                    <div class="mb-2 bg-gray-100 rounded-lg overflow-hidden">
                                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 300 200' %3E%3Crect fill='%23cccccc' width='300' height='200'/%3E%3Ctext fill='%23666666' font-family='sans-serif' font-size='24' text-anchor='middle' x='150' y='110'%3EOption 2%3C/text%3E%3C/svg%3E"
                                            class="image-preview w-full h-32 object-cover" id="preview_0_1">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Upload Image</label>
                                        <input type="file" name="question_0_option_1" accept="image/*"
                                            class="option-image block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90"
                                            onchange="previewImage(this, 'preview_0_1')">
                                    </div>
                                    
                                    <div class="mt-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Or Image URL</label>
                                        <input type="url" name="question_0_option_1_url" placeholder="https://example.com/image.jpg"
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                            onchange="previewImageURL(this, 'preview_0_1')">
                                    </div>
                                </div>
                                
                                <!-- Option 3 -->
                                <div class="option-block p-4 border border-gray-200 rounded-lg bg-white">
                                    <div class="mb-2 flex items-center">
                                        <input type="radio" id="correct_0_2" name="correct_option[0]" value="2" 
                                            class="w-4 h-4 text-primary focus:ring-primary">
                                        <label for="correct_0_2" class="ml-2 text-sm font-medium text-gray-700">
                                            Correct Answer
                                        </label>
                                    </div>
                                    
                                    <div class="mb-2 bg-gray-100 rounded-lg overflow-hidden">
                                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 300 200' %3E%3Crect fill='%23cccccc' width='300' height='200'/%3E%3Ctext fill='%23666666' font-family='sans-serif' font-size='24' text-anchor='middle' x='150' y='110'%3EOption 3%3C/text%3E%3C/svg%3E"
                                            class="image-preview w-full h-32 object-cover" id="preview_0_2">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Upload Image</label>
                                        <input type="file" name="question_0_option_2" accept="image/*"
                                            class="option-image block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90"
                                            onchange="previewImage(this, 'preview_0_2')">
                                    </div>
                                    
                                    <div class="mt-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Or Image URL</label>
                                        <input type="url" name="question_0_option_2_url" placeholder="https://example.com/image.jpg"
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                            onchange="previewImageURL(this, 'preview_0_2')">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Add Question Button -->
                <div class="mb-8 flex justify-center">
                    <button type="button" id="addQuestionBtn" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                        Add Another Question
                    </button>
                </div>
                

        </div>
    </main>


    <script>
        // Counter for question IDs
        let questionCounter = 1;
        
        // Function to preview uploaded image
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(file);
                
                // Clear the URL input when file is selected
                const urlInput = input.parentElement.nextElementSibling.querySelector('input[type="url"]');
                if (urlInput) {
                    urlInput.value = '';
                }
            }
        }
        
        // Function to preview image from URL
        function previewImageURL(input, previewId) {
            const preview = document.getElementById(previewId);
            const url = input.value.trim();
            
            if (url) {
                preview.src = url;
                
                // Clear the file input when URL is entered
                const fileInput = input.parentElement.previousElementSibling.querySelector('input[type="file"]');
                if (fileInput) {
                    fileInput.value = '';
                }
            } else {
                // Reset to placeholder if URL is empty
                preview.src = `data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 300 200' %3E%3Crect fill='%23cccccc' width='300' height='200'/%3E%3Ctext fill='%23666666' font-family='sans-serif' font-size='24' text-anchor='middle' x='150' y='110'%3EOption ${preview.id.split('_')[2]}%3C/text%3E%3C/svg%3E`;
            }
        }
        
        // Function to add a new question
        document.getElementById('addQuestionBtn').addEventListener('click', function() {
            const questionsContainer = document.getElementById('questionsContainer');
            const newQuestionIndex = questionCounter;
            questionCounter++;
            
            const questionBlock = document.createElement('div');
            questionBlock.className = 'question-block mb-12 p-6 border border-gray-200 rounded-lg bg-gray-50';
            questionBlock.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Question ${questionCounter}</h3>
                    <button type="button" class="remove-question text-red-500 hover:text-red-700" data-question="${newQuestionIndex}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>
                    </button>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Question Text</label>
                    <input type="text" name="question_text[${newQuestionIndex}]" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                        placeholder="e.g., What sign represents 'Thank you'?">
                </div>
                
                <div class="mb-4">
                    <p class="block text-sm font-medium text-gray-700 mb-3">Options (Select 3 Images)</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Option 1 -->
                        <div class="option-block p-4 border border-gray-200 rounded-lg bg-white">
                            <div class="mb-2 flex items-center">
                                <input type="radio" id="correct_${newQuestionIndex}_0" name="correct_option[${newQuestionIndex}]" value="0" required
                                    class="w-4 h-4 text-primary focus:ring-primary">
                                <label for="correct_${newQuestionIndex}_0" class="ml-2 text-sm font-medium text-gray-700">
                                    Correct Answer
                                </label>
                            </div>
                            
                            <div class="mb-2 bg-gray-100 rounded-lg overflow-hidden">
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 300 200' %3E%3Crect fill='%23cccccc' width='300' height='200'/%3E%3Ctext fill='%23666666' font-family='sans-serif' font-size='24' text-anchor='middle' x='150' y='110'%3EOption 1%3C/text%3E%3C/svg%3E"
                                    class="image-preview w-full h-32 object-cover" id="preview_${newQuestionIndex}_0">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Upload Image</label>
                                <input type="file" name="question_${newQuestionIndex}_option_0" accept="image/*"
                                    class="option-image block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90"
                                    onchange="previewImage(this, 'preview_${newQuestionIndex}_0')">
                            </div>
                            
                            <div class="mt-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Or Image URL</label>
                                <input type="url" name="question_${newQuestionIndex}_option_0_url" placeholder="https://example.com/image.jpg"
                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                    onchange="previewImageURL(this, 'preview_${newQuestionIndex}_0')">
                            </div>
                        </div>
                        
                        <!-- Option 2 -->
                        <div class="option-block p-4 border border-gray-200 rounded-lg bg-white">
                            <div class="mb-2 flex items-center">
                                <input type="radio" id="correct_${newQuestionIndex}_1" name="correct_option[${newQuestionIndex}]" value="1" 
                                    class="w-4 h-4 text-primary focus:ring-primary">
                                <label for="correct_${newQuestionIndex}_1" class="ml-2 text-sm font-medium text-gray-700">
                                    Correct Answer
                                </label>
                            </div>
                            
                            <div class="mb-2 bg-gray-100 rounded-lg overflow-hidden">
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 300 200' %3E%3Crect fill='%23cccccc' width='300' height='200'/%3E%3Ctext fill='%23666666' font-family='sans-serif' font-size='24' text-anchor='middle' x='150' y='110'%3EOption 2%3C/text%3E%3C/svg%3E"
                                    class="image-preview w-full h-32 object-cover" id="preview_${newQuestionIndex}_1">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Upload Image</label>
                                <input type="file" name="question_${newQuestionIndex}_option_1" accept="image/*"
                                    class="option-image block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90"
                                    onchange="previewImage(this, 'preview_${newQuestionIndex}_1')">
                            </div>
                            
                            <div class="mt-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Or Image URL</label>
                                <input type="url" name="question_${newQuestionIndex}_option_1_url" placeholder="https://example.com/image.jpg"
                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                    onchange="previewImageURL(this, 'preview_${newQuestionIndex}_1')">
                            </div>
                        </div>
                        
                        <!-- Option 3 -->
                        <div class="option-block p-4 border border-gray-200 rounded-lg bg-white">
                            <div class="mb-2 flex items-center">
                                <input type="radio" id="correct_${newQuestionIndex}_2" name="correct_option[${newQuestionIndex}]" value="2"   id="correct_${newQuestionIndex}_2" name="correct_option[${newQuestionIndex}]" value="2" 
                                    class="w-4 h-4 text-primary focus:ring-primary">
                                <label for="correct_${newQuestionIndex}_2" class="ml-2 text-sm font-medium text-gray-700">
                                    Correct Answer
                                </label>
                            </div>
                            
                            <div class="mb-2 bg-gray-100 rounded-lg overflow-hidden">
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 300 200' %3E%3Crect fill='%23cccccc' width='300' height='200'/%3E%3Ctext fill='%23666666' font-family='sans-serif' font-size='24' text-anchor='middle' x='150' y='110'%3EOption 3%3C/text%3E%3C/svg%3E"
                                    class="image-preview w-full h-32 object-cover" id="preview_${newQuestionIndex}_2">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Upload Image</label>
                                <input type="file" name="question_${newQuestionIndex}_option_2" accept="image/*"
                                    class="option-image block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90"
                                    onchange="previewImage(this, 'preview_${newQuestionIndex}_2')">
                            </div>
                            
                            <div class="mt-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Or Image URL</label>
                                <input type="url" name="question_${newQuestionIndex}_option_2_url" placeholder="https://example.com/image.jpg"
                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                    onchange="previewImageURL(this, 'preview_${newQuestionIndex}_2')">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            questionsContainer.appendChild(questionBlock);
            
            // Add event listener to the new remove button
            questionBlock.querySelector('.remove-question').addEventListener('click', function() {
                removeQuestion(this);
            });
        });
        
        // Function to remove a question
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-question') || e.target.closest('.remove-question')) {
                const button = e.target.classList.contains('remove-question') ? e.target : e.target.closest('.remove-question');
                removeQuestion(button);
            }
        });
        
        function removeQuestion(button) {
            // Don't remove if it's the only question
            const questionBlocks = document.querySelectorAll('.question-block');
            if (questionBlocks.length <= 1) {
                alert('You must have at least one question in the quiz.');
                return;
            }
            
            // Remove the question block
            const questionBlock = button.closest('.question-block');
            questionBlock.remove();
            
            // Update question numbers
            document.querySelectorAll('.question-block').forEach((block, index) => {
                block.querySelector('h3').textContent = `Question ${index + 1}`;
            });
        }
        
        // Form validation before submission
        document.getElementById('quizForm').addEventListener('submit', function(e) {
            const questionBlocks = document.querySelectorAll('.question-block');
            let isValid = true;
            
            questionBlocks.forEach((block, index) => {
                // Check if question text is filled
                const questionText = block.querySelector(`input[name="question_text[${index}]"]`);
                if (!questionText.value.trim()) {
                    isValid = false;
                    questionText.classList.add('border-red-500');
                } else {
                    questionText.classList.remove('border-red-500');
                }
                
                // Check if a correct option is selected
                const correctOption = block.querySelector(`input[name="correct_option[${index}]"]:checked`);
                if (!correctOption) {
                    isValid = false;
                    alert(`Please select a correct answer for Question ${index + 1}`);
                }
                
                // Check if each option has either a file or URL
                for (let i = 0; i < 3; i++) {
                    const fileInput = block.querySelector(`input[name="question_${index}_option_${i}"]`);
                    const urlInput = block.querySelector(`input[name="question_${index}_option_${i}_url"]`);
                    
                    if ((!fileInput.files || fileInput.files.length === 0) && !urlInput.value.trim()) {
                        isValid = false;
                        alert(`Please provide an image for Question ${index + 1}, Option ${i + 1}`);
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>