<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "sign_language";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from session (assuming you have user authentication)
// $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
// $lesson_id = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : 0;
// $answers = isset($_POST['answers']) ? $_POST['answers'] : [];

// $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

$data = json_decode(file_get_contents("php://input"), true);
$lesson_id = isset($data['lesson_id']) ? intval($data['lesson_id']) : 0;
$user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
$answers = isset($data['answers']) ? $data['answers'] : [];


if ($lesson_id === 0 || empty($answers)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data provided', 'asnwers' => $_POST['answers'], 'lesson_id' => $lesson_id , 'user_id' => $user_id]);
    exit;
}

// Initialize results
$results = [
    'status' => 'success',
    'total' => count($answers),
    'correct' => 0,
    'questions' => []
];

// Process each answer
foreach ($answers as $quiz_id => $selected_option) {
    // Get correct answer from database
    $sql = "SELECT question, correct_option FROM quizzes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $is_correct = ($selected_option === $row['correct_option']);
        
        // Add to results
        $results['questions'][] = [
            'id' => $quiz_id,
            'question' => $row['question'],
            'selected' => $selected_option,
            'correct' => $row['correct_option'],
            'is_correct' => $is_correct
        ];
        
        if ($is_correct) {
            $results['correct']++;
        }
    }
}

// Calculate percentage
$results['percentage'] = ($results['total'] > 0) ? 
    round(($results['correct'] / $results['total']) * 100) : 0;

// Save progress to database if user is logged in
if ($user_id > 0) {
    $sql = "INSERT INTO user_progress (user_id, lesson_id, score, completed_at) 
            VALUES (?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE score = ?, completed_at = NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iidi", $user_id, $lesson_id, $results['percentage'], $results['percentage']);
    $stmt->execute();
}

echo json_encode($results);
?>

