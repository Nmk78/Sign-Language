<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Language LMS - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
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
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .bg-gradient {
            background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);
        }
    </style>
</head>

<body class="flex w-full items-center justify-center min-h-screen bg-gray-50">
    <div class="bg-white w-full mx-auto  overflow-hidden flex items-center animate-fade-in">
        
        <div class="flex  absolute bg-gray-50 overflow-hidden top-24 right-1/2 transform translate-x-1/2 mx-auto flex-col rounded-xl shadow-xl md:flex-row">
            <!-- Left Side (Image and Info) -->
            <div class="hidden md:block w-1/2 bg-gradient text-white p-12 relative">
                <div class="absolute top-8 left-8">
                    <div class="flex items-center">
                        <i class="fas fa-hands text-2xl mr-2"></i>
                        <span class="font-bold text-xl">Slient Voice LMS</span>
                    </div>
                </div>
                
                <div class="h-full flex flex-col justify-center">
                    <div class="mb-8 mt-5">
                        <h2 class="text-xl font-bold mb-4">Welcome to Silence Voice</h2>
                        <p class="text-primary-100">Your platform for learning and teaching sign language effectively.</p>
                    </div>
                    
                    <div class="space-y-6 mt-auto">
                        <div class="flex items-center">
                            <div class="bg-white/20 rounded-full p-2 mr-3">
                                <i class="fas fa-user-graduate text-white"></i>
                            </div>
                            <div>
                                <p class="text-white font-medium">Students</p>
                                <p class="text-primary-100 text-sm">Access courses and track your learning progress</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="bg-white/20 rounded-full p-2 mr-3">
                                <i class="fas fa-chalkboard-teacher text-white"></i>
                            </div>
                            <div>
                                <p class="text-white font-medium">Teachers</p>
                                <p class="text-primary-100 text-sm">Manage courses and monitor student performance</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side (Login Form) -->
            <div class="w-full md:w-1/2 p-8 md:p-10">
                <div class="md:hidden flex items-center justify-center mb-6">
                    <i class="fas fa-hands text-primary-600 text-2xl mr-2"></i>
                    <span class="font-bold text-xl text-gray-800">Silent Voice</span>
                </div>
                
                <div class="text-center mb-5">
                    <h2 class="text-2xl font-bold text-gray-800">Sign In</h2>
                </div>
                
                <?php
                // Display error message if login failed
                if (isset($_GET['error']) && $_GET['error'] == 1) {
                    echo '<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    Invalid email or password. Please try again.
                                </p>
                            </div>
                        </div>
                    </div>';
                }
                ?>
                
                <form action="signingin" id="loginForm" method="POST" class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" id="email" name="email" required
                                class="pl-10 block w-full px-3 py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                placeholder="your@email.com">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="password" name="password" required
                                class="pl-10 block w-full px-3 py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                placeholder="••••••••">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-eye text-gray-400 cursor-pointer hover:text-gray-600 toggle-password"></i>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" 
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                            <i class="fas fa-sign-in-alt mr-2 mt-1"></i> Sign In
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-600 mb-4">Don't have an account?</p>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <a href="signup" 
                           class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-user-graduate text-primary-500 text-xl mb-2"></i>
                            <span class="text-sm font-medium text-gray-800">Student Sign Up</span>
                        </a>
                        
                        <a href="instructor-signup" 
                           class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-chalkboard-teacher text-primary-500 text-xl mb-2"></i>
                            <span class="text-sm font-medium text-gray-800">Teacher Sign Up</span>
                        </a>
                    </div>
                    
                    <div class="mt-6 flex justify-center">
                        <a href="index.php" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
                            <i class="fas fa-arrow-left mr-2 text-xs"></i>
                            Back to main site
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const togglePassword = document.querySelector('.toggle-password');
            const passwordInput = document.getElementById('password');
            
            togglePassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle the icon
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
            
            // Store email in localStorage if "Remember me" is checked
            const loginForm = document.getElementById('loginForm');
            loginForm.addEventListener('submit', function(event) {
                const rememberMe = document.getElementById('remember-me').checked;
                const email = document.getElementById('email').value;
                
                if (rememberMe) {
                    localStorage.setItem('userEmail', email);
                } else {
                    localStorage.removeItem('userEmail');
                }
            });
            
            // Fill email from localStorage if available
            const savedEmail = localStorage.getItem('userEmail');
            if (savedEmail) {
                document.getElementById('email').value = savedEmail;
                document.getElementById('remember-me').checked = true;
            }
        });
    </script>
</body>
</html>