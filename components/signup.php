<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Language LMS - Student Sign Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
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
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(10px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .student-bg {
            background-color: #1e5dac;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%2399f6e4' fill-opacity='0.15'%3E%3Cpath opacity='.5' d='M96 95h4v1h-4v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9zm-1 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9z'/%3E%3Cpath d='M6 5V0H5v5H0v1h5v94h1V6h94V5H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>

<body class="w-full h-full flex items-center justify-center min-h-screen bg-gray-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl mx-4 overflow-hidden animate-fade-in">
        <div class="flex absolute bg-gray-50 rounded-xl shadow-xl overflow-hidden top-24 right-1/2 transform translate-x-1/2  flex-col md:flex-row">
            <!-- Left Side (Image and Info) -->
            <div class="hidden md:block w-1/2 student-bg text-white p-12 relative">
                <div class="absolute top-8 left-8">
                    <div class="flex items-center">
                        <i class="fas fa-hands text-white text-2xl mr-2"></i>
                        <span class="font-bold text-xl">Silent Voice</span>
                    </div>
                </div>
                <div class="h-full flex flex-col justify-center">
                    <div class="mb-8">
                        <span class="inline-block px-3 py-1 bg-white/20 text-white text-sm rounded-full mb-4">STUDENT PORTAL</span>
                        <h2 class="text-3xl font-bold mb-4">Join Us, Student!</h2>
                        <p class="text-primary-100">Start your sign language learning journey and track your progress.</p>
                    </div>
                    <div class="space-y-4 mt-8">
                        <div class="flex items-center">
                            <div class="bg-white/20 rounded-full p-2 mr-3">
                                <i class="fas fa-book-open text-white"></i>
                            </div>
                            <div>
                                <p class="text-white font-medium">Access Your Courses</p>
                                <p class="text-primary-100 text-sm">Start learning new skills</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="bg-white/20 rounded-full p-2 mr-3">
                                <i class="fas fa-chart-line text-white"></i>
                            </div>
                            <div>
                                <p class="text-white font-medium">Track Your Progress</p>
                                <p class="text-primary-100 text-sm">See how far you've come</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="bg-white/20 rounded-full p-2 mr-3">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <div>
                                <p class="text-white font-medium">Join the Community</p>
                                <p class="text-primary-100 text-sm">Connect with fellow learners</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side (Signup Form) -->
            <div class="w-full md:w-1/2 p-8 md:p-10">
                <div class="md:hidden flex items-center justify-center mb-6">
                    <i class="fas fa-hands text-primary-600 text-2xl mr-2"></i>
                    <span class="font-bold text-xl text-gray-800">SignLang LMS</span>
                </div>
                <div class="text-center mb-5">
                    <h2 class="text-2xl font-bold text-[#1e5dac]">Student Sign Up</h2>
                </div>
                <?php
                // Display error message if signup failed
                if (isset($_GET['error']) && $_GET['error'] == 1) {
                    echo '<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    Signup failed. Please try again.
                                </p>
                            </div>
                        </div>
                    </div>';
                }
                ?>
                <form action="signingup" id="studentSignupForm" method="POST" class="space-y-3">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" id="name" name="username" required
                                class="pl-10 block w-full px-3 py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#1e5dac] focus:border-primary-500 transition-colors"
                                placeholder="Your Name">
                        </div>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" id="email" name="email" required
                                class="pl-10 block w-full px-3 py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#1e5dac] focus:border-primary-500 transition-colors"
                                placeholder="your@email.com">
                        </div>
                    </div>
                    <input type="hidden" name="role" value="student">

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="password" name="password" required
                                class="pl-10 block w-full px-3 py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#1e5dac] focus:border-primary-500 transition-colors"
                                placeholder="••••••••">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-eye text-gray-400 cursor-pointer hover:text-gray-600 toggle-password"></i>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="confirm-password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="confirm-password" name="confirm-password" required
                                class="pl-10 block w-full px-3 py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#1e5dac] focus:border-primary-500 transition-colors"
                                placeholder="••••••••">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-eye text-gray-400 cursor-pointer hover:text-gray-600 toggle-password-confirm"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#1e5dac] hover:bg-[#214a7b] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1e5dac] transition-colors">
                            <i class="fas fa-user-plus mr-2"></i> Sign Up as Student
                        </button>
                    </div>
                </form>
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="text-center mb-4">
                        <p class="text-sm text-gray-600">
                            Already have an account?
                            <a href="signup" class="font-medium text-[#1e5dac] hover:[#214a7b]">
                                Sign in as a student
                            </a>
                        </p>
                    </div>
                    <div class="flex justify-center">
                        <a href="instructor-signup" class="text-sm text-[#1e5dac] hover:[#214a7b] flex items-center">
                            <i class="fas fa-chalkboard-teacher mr-2 text-xs"></i>
                            Teacher? Sign up here
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
            const togglePasswordConfirm = document.querySelector('.toggle-password-confirm');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm-password');

            togglePassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);


                // Toggle the icon
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
            togglePasswordConfirm.addEventListener('click', function() {

                const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPasswordInput.setAttribute('type', type);

                // Toggle the icon
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });

        });
    </script>
</body>

</html>