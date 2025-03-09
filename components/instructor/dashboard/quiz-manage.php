<?php
// Start session for user data persistence
// session_start();

// Database connection
$conn = new mysqli("localhost", "root", "root", "sign_language");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$lessonId = isset($_GET['lesson_id']) ? intval($_GET['lesson_id']) : 17;

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
<body class="bg-background min-h-screen">
    <!-- Header Section -->
    <header class="bg-surface shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-text flex items-center">
                <i class="fas fa-hands text-primary mr-3"></i>
                Sign Language Quiz Manager
            </h1>
            <div class="flex space-x-4">
                <a href="components\instructor\dashboard\quiz.php?lesson_id=<?php echo $lessonId; ?>" class="bg-secondary hover:bg-opacity-90 text-white px-4 py-2 rounded-lg shadow transition duration-200 flex items-center">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Create Quiz
                </a>
                <button onclick="openQuizModal()" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg shadow transition duration-200 flex items-center">
                    <i class="fas fa-play-circle mr-2"></i>
                    Take Quiz
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Existing Quiz Questions Section -->
        <div class="bg-surface rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-accent to-primary py-5 px-6">
                <h2 class="text-2xl font-bold text-white">Existing Quiz Questions</h2>
                <p class="text-white text-opacity-80 mt-1">Manage your existing sign language quiz questions for <?php echo htmlspecialchars($lessonInfo['title'] ?? 'this lesson'); ?></p>
            </div>
            
            <div class="p-6">
                <?php if (empty($quizQuestions)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-question-circle text-text-light text-6xl mb-4"></i>
                        <h3 class="text-xl font-medium text-text">No quiz questions found</h3>
                        <p class="text-text-light mt-1">Start by creating your first question</p>
                        <a href="components/instructor/dashboard/quiz.php?lesson_id=<?php echo $lessonId; ?>" class="mt-4 inline-block bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded-lg transition duration-200">
                            Create First Question
                        </a>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-light uppercase tracking-wider">Question</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-light uppercase tracking-wider">Correct Answer</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-text-light uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-surface divide-y divide-gray-200">
                                <?php foreach ($quizQuestions as $index => $question): ?>
                                <tr class="<?php echo $index % 2 === 0 ? 'bg-surface' : 'bg-gray-50'; ?> hover:bg-gray-100 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text"><?php echo htmlspecialchars($question['question']); ?></td>                                
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-text-light">
                                        Option <?php echo htmlspecialchars($question['correct_option']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="viewQuestion(<?php echo $question['id']; ?>)" class="text-primary hover:text-primary-dark mr-3">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editQuestion(<?php echo $question['id']; ?>)" class="text-warning hover:text-opacity-80 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteQuestion(<?php echo $question['id']; ?>)" class="text-error hover:text-opacity-80">
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
        <div class="bg-surface rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-accent py-5 px-6 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-white">Sign Language Quiz</h2>
                <button onclick="closeQuizModal()" class="text-white hover:text-gray-200 focus:outline-none">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]" id="quiz-content">
                <!-- Quiz Form - All questions visible for scrolling -->
                <div id="quiz-form-container">
                    <div id="quiz-progress" class="mb-8 sticky top-0 bg-surface pt-2 pb-4 z-10">
                        <div class="flex justify-between text-sm text-text-light mb-1">
                            <span>Progress</span>
                            <span id="progress-text">0%</span>
                        </div>
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div id="progress-value" class="h-full bg-gradient-to-r from-primary to-secondary rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>
                    
                    <form id="quizForm">
                        <?php foreach ($quizQuestions as $index => $question): ?>
                        <div class="mb-10 pb-10 border-b border-gray-200 last:border-b-0 question-container" data-question-id="<?php echo $question['id']; ?>">
                            <div class="flex items-center mb-4">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3">
                                    <?php echo $index + 1; ?>
                                </div>
                                <h3 class="text-xl font-semibold text-text"><?php echo htmlspecialchars($question['question']); ?></h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Option A -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="answer_<?php echo $question['id']; ?>" value="A" class="hidden answer-input" 
                                           data-correct-option="<?php echo $question['correct_option']; ?>" 
                                           data-question-id="<?php echo $question['id']; ?>"
                                           onclick="updateSelection(this)">
                                    <div class="relative border-4 border-gray-200 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1 option-card">
                                        <div class="absolute top-3 left-3 z-10 bg-white/90 text-primary font-bold w-8 h-8 rounded-full flex items-center justify-center shadow-sm">A</div>
                                        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/10 pointer-events-none"></div>
                                        <img src="<?php echo $question['option_a']; ?>" alt="Option A" class="w-full h-48 object-cover">
                                        <div class="p-3 bg-gray-50">
                                            <div class="flex justify-between items-center">
                                                <span class="font-medium text-text">Option A</span>
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
                                        <div class="absolute top-3 left-3 z-10 bg-white/90 text-primary font-bold w-8 h-8 rounded-full flex items-center justify-center shadow-sm">B</div>
                                        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/10 pointer-events-none"></div>
                                        <img src="<?php echo $question['option_b']; ?>" alt="Option B" class="w-full h-48 object-cover">
                                        <div class="p-3 bg-gray-50">
                                            <div class="flex justify-between items-center">
                                                <span class="font-medium text-text">Option B</span>
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
                                        <div class="absolute top-3 left-3 z-10 bg-white/90 text-primary font-bold w-8 h-8 rounded-full flex items-center justify-center shadow-sm">C</div>
                                        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/10 pointer-events-none"></div>
                                        <img src="<?php echo $question['option_c']; ?>" alt="Option C" class="w-full h-48 object-cover">
                                        <div class="p-3 bg-gray-50">
                                            <div class="flex justify-between items-center">
                                                <span class="font-medium text-text">Option C</span>
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
                                        <div class="absolute top-3 left-3 z-10 bg-white/90 text-primary font-bold w-8 h-8 rounded-full flex items-center justify-center shadow-sm">D</div>
                                        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/10 pointer-events-none"></div>
                                        <img src="<?php echo $question['option_d']; ?>" alt="Option D" class="w-full h-48 object-cover">
                                        <div class="p-3 bg-gray-50">
                                            <div class="flex justify-between items-center">
                                                <span class="font-medium text-text">Option D</span>
                                                <span class="result-icon hidden"></span>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <!-- Submit Button - Fixed at the bottom -->
                        <div class="mt-8 flex justify-end sticky bottom-0 pt-4 pb-2 bg-surface">
                            <button type="button" onclick="submitQuiz()" class="bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded-xl font-medium transition shadow-lg hover:shadow-xl flex items-center gap-2">
                                <span>Submit Quiz</span>
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Quiz Results - Initially Hidden -->
                <div id="quizResult" class="mt-10 hidden">
                    <div class="bg-gradient-to-r from-primary/10 to-secondary/10 rounded-xl p-8 text-center transform transition-all duration-500">
                        <div id="result-icon" class="text-5xl mb-4 mx-auto w-20 h-20 rounded-full flex items-center justify-center"></div>
                        <h3 class="text-2xl font-bold text-text mb-2">Your Results</h3>
                        <p id="resultMessage" class="text-lg text-text-light mb-4"></p>
                        
                        <div class="w-full max-w-md mx-auto mb-6">
                            <div class="bg-white rounded-full h-6 shadow-inner overflow-hidden">
                                <div id="score-bar" class="h-full rounded-full transition-all duration-1000 ease-out" style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-6">
                            <button onclick="resetQuiz()" class="bg-white text-primary border border-primary/20 px-6 py-2 rounded-lg transition-colors duration-300 hover:bg-primary/5 flex items-center justify-center gap-2">
                                <i class="fas fa-redo"></i>
                                <span>Try Again</span>
                            </button>
                            <button onclick="closeQuizModal()" class="bg-primary text-white px-6 py-2 rounded-lg transition-colors duration-300 hover:bg-primary-dark flex items-center justify-center gap-2">
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
        <div class="bg-surface rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden">
            <div class="bg-gradient-to-r from-accent to-primary py-4 px-6 flex justify-between items-center">
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
                card.classList.remove('border-success', 'border-error', 'border-primary', 'shadow-success/20', 'shadow-error/20');
                card.classList.add('border-gray-200');
            });
            
            // Remove highlighting from unanswered questions
            document.querySelectorAll('.question-container').forEach(container => {
                container.classList.remove('border-l-4', 'border-warning', 'pl-3');
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
                    card.classList.add('border-primary');
                    card.classList.remove('border-gray-200');
                } else {
                    card.classList.remove('border-primary');
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
                        selectedCard.classList.remove('border-primary', 'border-gray-200');
                        selectedCard.classList.add('border-success', 'shadow-success/20');
                        resultIcon.innerHTML = '<i class="fas fa-check-circle text-success"></i>';
                        correctCount++;
                    } else {
                        selectedCard.classList.remove('border-primary', 'border-gray-200');
                        selectedCard.classList.add('border-error', 'shadow-error/20');
                        resultIcon.innerHTML = '<i class="fas fa-times-circle text-error"></i>';
                        
                        // Highlight the correct answer
                        const correctInput = document.querySelector(`input[name="answer_${questionId}"][value="${correctOption}"]`);
                        const correctCard = correctInput.nextElementSibling;
                        const correctIcon = correctCard.querySelector('.result-icon');
                        
                        correctCard.classList.add('border-success', 'shadow-success/20');
                        correctCard.classList.remove('border-gray-200');
                        correctIcon.classList.remove('hidden');
                        correctIcon.innerHTML = '<i class="fas fa-check-circle text-success"></i>';
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
                    questionContainer.classList.add('border-l-4', 'border-warning', 'pl-3');
                    
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
                resultIcon.className = 'text-5xl mb-4 mx-auto w-20 h-20 rounded-full flex items-center justify-center bg-success/10 text-success';
                resultIcon.innerHTML = '<i class="fas fa-trophy"></i>';
                resultMessage.innerHTML = `<span class="font-bold">Excellent!</span> You got <span class="text-success font-bold">${correctCount}</span> out of <span class="font-bold">${totalQuestions}</span> correct!<br>Your score: <span class="text-success font-bold">${score.toFixed(0)}%</span>`;
                scoreBar.className = 'h-full rounded-full transition-all duration-1000 ease-out bg-gradient-to-r from-success to-secondary';
                createConfetti();
            } else if (score >= 60) {
                resultIcon.className = 'text-5xl mb-4 mx-auto w-20 h-20 rounded-full flex items-center justify-center bg-primary/10 text-primary';
                resultIcon.innerHTML = '<i class="fas fa-medal"></i>';
                resultMessage.innerHTML = `<span class="font-bold">Good job!</span> You got <span class="text-primary font-bold">${correctCount}</span> out of <span class="font-bold">${totalQuestions}</span> correct!<br>Your score: <span class="text-primary font-bold">${score.toFixed(0)}%</span>`;
                scoreBar.className = 'h-full rounded-full transition-all duration-1000 ease-out bg-gradient-to-r from-primary to-primary-dark';
            } else {
                resultIcon.className = 'text-5xl mb-4 mx-auto w-20 h-20 rounded-full flex items-center justify-center bg-accent/10 text-accent';
                resultIcon.innerHTML = '<i class="fas fa-star-half-alt"></i>';
                resultMessage.innerHTML = `<span class="font-bold">Keep practicing!</span> You got <span class="text-accent font-bold">${correctCount}</span> out of <span class="font-bold">${totalQuestions}</span> correct!<br>Your score: <span class="text-accent font-bold">${score.toFixed(0)}%</span>`;
                scoreBar.className = 'h-full rounded-full transition-all duration-1000 ease-out bg-gradient-to-r from-accent to-warning';
            }
            
            // Animate score bar
            setTimeout(() => {
                scoreBar.style.width = `${score}%`;
            }, 100);
        }
        
        // Function to create confetti effect
        function createConfetti() {
            const colors = ['#4A90E2', '#7ED321', '#F5A623', '#10B981', '#F1C40F'];
            
            for (let i = 0; i < 
