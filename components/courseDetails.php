<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Overview</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
                    },
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-background font-sans">
    <div class="">
        <header class="mb-8 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-text">Advanced Web Development</h1>
            <p class="text-text-light mt-2">Master modern web technologies with hands-on projects</p>
        </header>
        
        <div class="grid md:grid-cols-3 gap-8 bg-surface p-6 md:p-8 rounded-xl shadow-lg">
            <!-- Left Section: Course Image and Comments -->
            <div class="col-span-1 space-y-8">
                <div class="overflow-hidden rounded-xl shadow-md">
                    <img src="photo_2025-02-19_12-50-35.jpg" alt="Course Image" class="w-full h-48 object-cover rounded-t-xl">
                    <div class="p-5 bg-white border-t border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="bg-primary rounded-full w-10 h-10 flex items-center justify-center text-white font-bold">JD</div>
                                <div class="ml-3">
                                    <p class="font-medium">John Doe</p>
                                    <p class="text-sm text-text-light">Instructor</p>
                                </div>
                            </div>
                            <span class="bg-accent text-white text-sm py-1 px-3 rounded-full">Bestseller</span>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-text-light">Duration:</span>
                                <span class="font-medium">12 weeks</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-text-light">Lessons:</span>
                                <span class="font-medium">24 videos</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-text-light">Level:</span>
                                <span class="font-medium">Intermediate</span>
                            </div>
                        </div>
                        <button class="w-full mt-6 bg-primary hover:bg-primary-dark text-white font-medium py-2 rounded-lg transition-colors">
                            Enroll Now
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-xl font-bold text-text mb-4">Comments</h2>
                    <div class="space-y-4 max-h-80 overflow-y-auto mb-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-text">Great course! Really informative.</p>
                            <div class="flex justify-between mt-2">
                                <span class="text-sm text-text-light">- User123</span>
                                <span class="text-xs text-text-light">2 days ago</span>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-text">Loved the content, very detailed.</p>
                            <div class="flex justify-between mt-2">
                                <span class="text-sm text-text-light">- User456</span>
                                <span class="text-xs text-text-light">1 week ago</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <textarea class="w-full p-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary resize-none" placeholder="Add a comment..." rows="3"></textarea>
                        <div class="flex justify-end">
                            <button class="mt-2 bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Section: Course Videos -->
            <div class="col-span-2">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-text">Course Videos</h2>
                        <div class="flex items-center">
                            <span class="text-sm text-success mr-2">75% complete</span>
                            <div class="w-24 h-2 bg-gray-200 rounded-full">
                                <div class="w-3/4 h-full bg-success rounded-full"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Video 1 -->
                        <div class="group bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors overflow-hidden">
                            <div class="p-4 flex flex-col md:flex-row md:items-center">
                                <div class="flex-shrink-0 mr-4 mb-4 md:mb-0">
                                    <div class="relative">
                                        <div class="w-full md:w-32 h-40 md:h-20 bg-gray-200 rounded-lg overflow-hidden">
                                            <img src="assets/lesson1.mp4" alt="Lesson 1 thumbnail" class="w-full h-full object-cover opacity-0">
                                            <div class="absolute inset-0 bg-gray-300 flex items-center justify-center">
                                                <span class="text-xs text-gray-600">Lesson 1</span>
                                            </div>
                                        </div>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div class="w-10 h-10 bg-black bg-opacity-50 rounded-full flex items-center justify-center group-hover:bg-primary transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-text">Lesson 1: Introduction</h3>
                                        <span class="text-sm text-text-light">15:30</span>
                                    </div>
                                    <p class="text-text-light text-sm mt-1">Learn the basics of web development and set up your environment.</p>
                                    <div class="flex items-center mt-2">
                                        <span class="text-xs bg-success text-white px-2 py-1 rounded-full">Completed</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Video 2 -->
                        <div class="group bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors overflow-hidden">
                            <div class="p-4 flex flex-col md:flex-row md:items-center">
                                <div class="flex-shrink-0 mr-4 mb-4 md:mb-0">
                                    <div class="relative">
                                        <div class="w-full md:w-32 h-40 md:h-20 bg-gray-200 rounded-lg overflow-hidden">
                                            <img src="assets/lesson2.mp4" alt="Lesson 2 thumbnail" class="w-full h-full object-cover opacity-0">
                                            <div class="absolute inset-0 bg-gray-300 flex items-center justify-center">
                                                <span class="text-xs text-gray-600">Lesson 2</span>
                                            </div>
                                        </div>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div class="w-10 h-10 bg-black bg-opacity-50 rounded-full flex items-center justify-center group-hover:bg-primary transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-text">Lesson 2: Advanced Concepts</h3>
                                        <span class="text-sm text-text-light">22:45</span>
                                    </div>
                                    <p class="text-text-light text-sm mt-1">Dive deeper into advanced web development concepts and techniques.</p>
                                    <div class="flex items-center mt-2">
                                        <span class="text-xs bg-success text-white px-2 py-1 rounded-full">Completed</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Video 3 -->
                        <div class="group bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors overflow-hidden">
                            <div class="p-4 flex flex-col md:flex-row md:items-center">
                                <div class="flex-shrink-0 mr-4 mb-4 md:mb-0">
                                    <div class="relative">
                                        <div class="w-full md:w-32 h-40 md:h-20 bg-gray-200 rounded-lg overflow-hidden">
                                            <img src="assets/lesson2.mp4" alt="Lesson 3 thumbnail" class="w-full h-full object-cover opacity-0">
                                            <div class="absolute inset-0 bg-gray-300 flex items-center justify-center">
                                                <span class="text-xs text-gray-600">Lesson 3</span>
                                            </div>
                                        </div>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div class="w-10 h-10 bg-black bg-opacity-50 rounded-full flex items-center justify-center group-hover:bg-primary transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-text">Lesson 3: Building Projects</h3>
                                        <span class="text-sm text-text-light">28:10</span>
                                    </div>
                                    <p class="text-text-light text-sm mt-1">Learn how to build real-world projects from scratch.</p>
                                    <div class="flex items-center mt-2">
                                        <span class="text-xs bg-gray-200 text-text-light px-2 py-1 rounded-full">Not started</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-center">
                        <button class="bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded-lg transition-colors flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Load More Lessons
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <footer class="mt-8 text-center text-text-light text-sm">
            <p>© 2025 Advanced Web Development. All rights reserved.</p>
        </footer>
    </div>

    <script>
        // Simple script to handle comment submission
        document.addEventListener('DOMContentLoaded', function() {
            const commentForm = document.querySelector('textarea');
            const submitButton = commentForm.nextElementSibling.querySelector('button');
            const commentsContainer = document.querySelector('.max-h-80');
            
            submitButton.addEventListener('click', function() {
                if (commentForm.value.trim()) {
                    const newComment = document.createElement('div');
                    newComment.className = 'p-4 bg-gray-50 rounded-lg';
                    newComment.innerHTML = `
                        <p class="text-text">${commentForm.value}</p>
                        <div class="flex justify-between mt-2">
                            <span class="text-sm text-text-light">- You</span>
                            <span class="text-xs text-text-light">Just now</span>
                        </div>
                    `;
                    commentsContainer.appendChild(newComment);
                    commentForm.value = '';
                    
                    // Scroll to the bottom of comments
                    commentsContainer.scrollTop = commentsContainer.scrollHeight;
                }
            });
        });
    </script>
</body>
</html>