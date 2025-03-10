<?php
// Initialize variables
$success_message = '';
$error_message = '';

// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'root';
$db_name = 'sign_language';

$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if form was submitted
    if (isset($_POST['submit_quiz'])) {
        // Create directory for quiz images if it doesn't exist
        $upload_dir = '../quizzes/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Assuming you already have lesson_id and course_id from your existing implementation
        $lesson_id = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : 0;
        $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
        
        if ($lesson_id <= 0) {
            $error_message = "Error: Valid lesson ID is required.";
        } else {
            // Process each question
            $question_count = count($_POST['question_text']);
            $has_errors = false;
            
            // Start transaction
            $conn->begin_transaction();
            
            try {
                for ($i = 0; $i < $question_count; $i++) {
                    $question_text = htmlspecialchars($_POST['question_text'][$i]);
                    $correct_option_index = intval($_POST['correct_option'][$i]);
                    
                    // Map the correct option index to A, B, or C
                    $correct_option_letter = ['A', 'B', 'C'][$correct_option_index];
                    
                    $option_paths = array('', '', ''); // Initialize with empty strings
                    
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
                                throw new Exception("Error: Only JPG, JPEG, PNG & GIF files are allowed for options.");
                            }
                            
                            // Generate unique filename
                            $new_filename = uniqid('quiz_') . '_q' . $i . '_opt' . $j . '.' . $file_ext;
                            $upload_path = $upload_dir . $new_filename;
                            
                            // Upload file
                            if (move_uploaded_file($file_tmp, $upload_path)) {
                                $option_paths[$j] = $upload_path;
                            } else {
                                throw new Exception("Error uploading option image for question " . ($i + 1) . ", option " . ($j + 1));
                            }
                        } else {
                            // Check if using existing image URL
                            if (!empty($_POST["question_{$i}_option_{$j}_url"])) {
                                $option_paths[$j] = htmlspecialchars($_POST["question_{$i}_option_{$j}_url"]);
                            } else {
                                throw new Exception("Error: Missing image for question " . ($i + 1) . ", option " . ($j + 1));
                            }
                        }
                    }
                    
                    // Insert the quiz question into the database
                    $stmt = $conn->prepare("INSERT INTO quizzes (lesson_id, question, option_a, option_b, option_c, correct_option, course_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("isssssi", $lesson_id, $question_text, $option_paths[0], $option_paths[1], $option_paths[2], $correct_option_letter, $course_id);
                    
                    if (!$stmt->execute()) {
                        throw new Exception("Error saving question to database: " . $stmt->error);
                    }
                    header("Location: /dashboard?courses");
                    $stmt->close();
                }
                
                // If we got here, commit the transaction
                $conn->commit();
                $success_message = "Quiz created successfully! All questions have been saved to the database.";
                
            } catch (Exception $e) {
                // An error occurred, rollback the transaction
                $conn->rollback();
                $error_message = $e->getMessage();
                $has_errors = true;
            }
        }
    }
}

// Close the database connection
$conn->close();
?>