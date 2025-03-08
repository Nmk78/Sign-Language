<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Language Quiz</title>
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
                    animation: {
                        'bounce-slow': 'bounce 3s infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .option-card {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .option-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        input:checked + .option-card {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.3);
            transform: translateY(-5px);
        }
        
        .option-card.correct {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3) !important;
        }
        
        .option-card.incorrect {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.3) !important;
        }
        
        .option-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(255,255,255,0) 70%, rgba(0,0,0,0.1) 100%);
            z-index: 1;
        }
        
        .option-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 2;
            background-color: rgba(255, 255, 255, 0.9);
            color: #0ea5e9;
            font-weight: bold;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .result-animation {
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .progress-bar {
            height: 8px;
            border-radius: 4px;
            background: #e5e7eb;
            overflow: hidden;
            margin: 1rem 0;
        }
        
        .progress-value {
            height: 100%;
            background: linear-gradient(to right, #0ea5e9, #14b8a6);
            border-radius: 4px;
            transition: width 0.5s ease;
        }
        
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background-color: #0ea5e9;
            animation: confetti 5s ease-in-out infinite;
            z-index: 999;
        }
        
        @keyframes confetti {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            100% { transform: translateY(1000px) rotate(720deg); opacity: 0; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 py-10">
    <div id="confetti-container"></div>
    
    <div class="">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-primary-600 to-secondary-600 py-6 px-8">
                <h2 class="text-3xl font-bold text-white text-center flex items-center justify-center gap-3">
                    <i class="fas fa-hands text-yellow-300 animate-pulse-slow"></i>
                    Sign Language Quiz
                    <i class="fas fa-hands text-yellow-300 animate-pulse-slow"></i>
                </h2>
                <p class="text-primary-100 text-center mt-2">Test your knowledge of sign language gestures</p>
            </div>
            
            <!-- Quiz Content -->
            <div class="p-8">
                <div id="quiz-progress" class="mb-8 hidden">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Progress</span>
                        <span id="progress-text">0%</span>
                    </div>
                    <div class="progress-bar">
                        <div id="progress-value" class="progress-value" style="width: 0%"></div>
                    </div>
                </div>
                
                <form id="quizForm">
                    <?php
                    $conn = new mysqli("localhost", "root", "root", "sign_language");

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "SELECT id, question, option_a, option_b, option_c, option_d, correct_option FROM quizzes WHERE lesson_id = 17";
                    $result = $conn->query($sql);
                    $questionCount = 0;
                    $totalQuestions = $result->num_rows;

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) { 
                            $questionCount++;
                            ?>
                            <div class="mb-10 question-container" data-question="<?php echo $questionCount; ?>">
                                <div class="flex items-center mb-4">
                                    <div class="bg-primary-100 text-primary-700 rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3">
                                        <?php echo $questionCount; ?>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-800"><?php echo $row['question']; ?></h3>
                                </div>
                                
                                <div class="grid grid-cols-4 gap-6">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="answer_<?php echo $row['id']; ?>" value="A" class="hidden" 
                                               data-correct-option="<?php echo $row['correct_option']; ?>" 
                                               data-question-num="<?php echo $questionCount; ?>"
                                               onclick="updateSelection(this)">
                                        <div class="option-card border-4 border-gray-200 rounded-xl overflow-hidden">
                                            <div class="option-badge">A</div>
                                            <img src="<?php echo $row['option_a']; ?>" alt="Option A" class="w-full h-48 object-cover">
                                            <div class="p-3 bg-gray-50">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-medium text-gray-700">Option A</span>
                                                    <span class="result-icon hidden"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <label class="cursor-pointer">
                                        <input type="radio" name="answer_<?php echo $row['id']; ?>" value="B" class="hidden" 
                                               data-correct-option="<?php echo $row['correct_option']; ?>" 
                                               data-question-num="<?php echo $questionCount; ?>"
                                               onclick="updateSelection(this)">
                                        <div class="option-card border-4 border-gray-200 rounded-xl overflow-hidden">
                                            <div class="option-badge">B</div>
                                            <img src="<?php echo $row['option_b']; ?>" alt="Option B" class="w-full h-48 object-cover">
                                            <div class="p-3 bg-gray-50">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-medium text-gray-700">Option B</span>
                                                    <span class="result-icon hidden"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <label class="cursor-pointer">
                                        <input type="radio" name="answer_<?php echo $row['id']; ?>" value="C" class="hidden" 
                                               data-correct-option="<?php echo $row['correct_option']; ?>" 
                                               data-question-num="<?php echo $questionCount; ?>"
                                               onclick="updateSelection(this)">
                                        <div class="option-card border-4 border-gray-200 rounded-xl overflow-hidden">
                                            <div class="option-badge">C</div>
                                            <img src="<?php echo $row['option_c']; ?>" alt="Option C" class="w-full h-48 object-cover">
                                            <div class="p-3 bg-gray-50">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-medium text-gray-700">Option C</span>
                                                    <span class="result-icon hidden"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <label class="cursor-pointer">
                                        <input type="radio" name="answer_<?php echo $row['id']; ?>" value="D" class="hidden" 
                                               data-correct-option="<?php echo $row['correct_option']; ?>" 
                                               data-question-num="<?php echo $questionCount; ?>"
                                               onclick="updateSelection(this)">
                                        <div class="option-card border-4 border-gray-200 rounded-xl overflow-hidden">
                                            <div class="option-badge">D</div>
                                            <img src="<?php echo $row['option_d']; ?>" alt="Option D" class="w-full h-48 object-cover">
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
                        <?php }
                    } else {
                        echo "<div class='p-10 text-center'>";
                        echo "<i class='fas fa-exclamation-triangle text-yellow-500 text-4xl mb-4'></i>";
                        echo "<p class='text-xl text-red-500 font-medium'>No quizzes available for this lesson.</p>";
                        echo "<p class='text-gray-500 mt-2'>Please try another lesson or contact your instructor.</p>";
                        echo "</div>";
                    }
                    $conn->close();
                    ?>
                    
                    <div class="text-center mt-8">
                        <button type="button" onclick="submitQuiz()" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-3 rounded-xl font-medium hover:from-primary-700 hover:to-primary-800 transition shadow-lg hover:shadow-xl flex items-center justify-center mx-auto gap-2">
                            <span>Submit Quiz</span>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>

                <div id="quizResult" class="mt-10 hidden">
                    <div class="result-animation bg-gradient-to-r from-primary-50 to-secondary-50 rounded-xl p-8 text-center">
                        <div id="result-icon" class="text-5xl mb-4 mx-auto w-20 h-20 rounded-full flex items-center justify-center"></div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Your Results</h3>
                        <p id="resultMessage" class="text-lg text-gray-600 mb-4"></p>
                        
                        <div class="w-full max-w-md mx-auto mb-6">
                            <div class="bg-white rounded-full h-6 shadow-inner overflow-hidden">
                                <div id="score-bar" class="h-full rounded-full transition-all duration-1000 ease-out" style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <div class="flex justify-center gap-4 mt-6">
                            <button onclick="window.location.reload()" class="bg-white text-primary-600 border border-primary-200 px-6 py-2 rounded-lg hover:bg-primary-50 transition flex items-center gap-2">
                                <i class="fas fa-redo"></i>
                                <span>Try Again</span>
                            </button>
                            <button onclick="window.location.href='lessons.php'" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition flex items-center gap-2">
                                <i class="fas fa-book"></i>
                                <span>Back to Lessons</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center text-gray-500 text-sm mt-6">
            © <?php echo date('Y'); ?> Sign Language Learning Platform. All rights reserved.
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show progress bar if there are questions
            const questionContainers = document.querySelectorAll('.question-container');
            if (questionContainers.length > 0) {
                document.getElementById('quiz-progress').classList.remove('hidden');
            }
        });
        
        // Track answered questions with a Set to avoid duplicates
        const answeredQuestionsSet = new Set();
        const totalQuestions = document.querySelectorAll('.question-container').length;
        
        // Function to update selection and progress
        function updateSelection(selectedInput) {
            // Update card styling
            const allCards = document.querySelectorAll(`input[name="${selectedInput.name}"] + .option-card`);
            allCards.forEach(card => {
                card.classList.remove('border-primary-500');
            });
            
            const selectedCard = selectedInput.nextElementSibling;
            selectedCard.classList.add('border-primary-500');
            
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
        
        // Function to create confetti effect
        function createConfetti() {
            const confettiContainer = document.getElementById('confetti-container');
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
                
                confettiContainer.appendChild(confetti);
                
                // Remove confetti after animation
                setTimeout(() => {
                    confetti.remove();
                }, 5000);
            }
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
                        selectedCard.classList.add('correct');
                        resultIcon.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
                        correctCount++;
                    } else {
                        selectedCard.classList.add('incorrect');
                        resultIcon.innerHTML = '<i class="fas fa-times-circle text-red-500"></i>';
                        
                        // Highlight the correct answer
                        const questionName = input.name;
                        const correctInput = document.querySelector(`input[name="${questionName}"][value="${correctOption}"]`);
                        const correctCard = correctInput.nextElementSibling;
                        const correctIcon = correctCard.querySelector('.result-icon');
                        
                        correctCard.classList.add('correct');
                        correctIcon.classList.remove('hidden');
                        correctIcon.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
                    }
                    
                    // Add to questions map to track which ones were answered
                    const questionNum = input.getAttribute('data-question-num');
                    questionsMap.set(questionNum, true);
                }
            });
            
            // Highlight unanswered questions
            document.querySelectorAll('.question-container').forEach(container => {
                const questionNum = container.getAttribute('data-question');
                if (!questionsMap.has(questionNum)) {
                    container.classList.add('border-l-4', 'border-yellow-400', 'pl-3');
                    container.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
            
            if (totalAnswered < totalQuestions) {
                alert(`Please answer all questions. You've answered ${totalAnswered} out of ${totalQuestions}.`);
                return;
            }
            
            // Show results
            const resultMessage = document.getElementById('resultMessage');
            const quizResult = document.getElementById('quizResult');
            const scoreBar = document.getElementById('score-bar');
            const resultIcon = document.getElementById('result-icon');
            const score = (correctCount / totalQuestions) * 100;
            
            quizResult.classList.remove('hidden');
            quizResult.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
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
    </script>
</body>
</html>