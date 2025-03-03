<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Web Development</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column -->
            <div class="space-y-8">

                <!-- Conditional Content -->
                <div id="courseContent"></div>

                <!-- Comments Section -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold mb-4">Comments</h3>
                    <div class="border-b pb-4">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                            <div>
                                <p class="font-medium">User123</p>
                                <p class="text-gray-600 mb-2">Great course! Really informative.</p>
                                <p class="text-sm text-gray-500">2 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Right Column -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b">
                    <h1 class="text-3xl font-bold mb-2">Advanced Web Development</h1>
                    <p class="text-gray-600 mb-6">Master modern web technologies with hands-on projects</p>

                    <!-- Progress Bar -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold">Course Videos</h2>
                        <div class="flex items-center">
                            <span class="text-green-500 mr-2">75% complete</span>
                            <div class="w-32 h-2 bg-gray-200 rounded-full">
                                <div class="w-3/4 h-full bg-green-500 rounded-full"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Video Lessons -->
                    <div class="space-y-4">
                        <!-- Lesson 1 -->
                        <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                            <div class="w-32 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium">Lesson 1: Introduction</h3>
                                <p class="text-sm text-gray-500">Learn the basics of web development and set up your environment.</p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-sm text-gray-500">15:30</span>
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Completed</span>
                                </div>
                            </div>
                        </div>

                        <!-- Lesson 2 -->
                        <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                            <div class="w-32 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium">Lesson 2: Advanced Concepts</h3>
                                <p class="text-sm text-gray-500">Dive deeper into advanced web development concepts and techniques.</p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-sm text-gray-500">22:45</span>
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Completed</span>
                                </div>
                            </div>
                        </div>

                        <!-- Lesson 3 -->
                        <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                            <div class="w-32 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium">Lesson 3: Building Projects</h3>
                                <p class="text-sm text-gray-500">Learn how to build real-world projects from scratch.</p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-sm text-gray-500">28:10</span>
                                    <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">Not started</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Button -->
    <div class="fixed bottom-8 right-8">
        <button class="bg-[#4A90E2] text-white p-4 rounded-full shadow-lg hover:bg-[#357abd]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
            </svg>
        </button>
    </div>

    <script>
    // Simulate enrollment status (true for enrolled, false for not enrolled)
    const isEnrolled = true; // Change this to false to see the non-enrolled view

    const courseContent = document.getElementById('courseContent');

    if (isEnrolled) {
        courseContent.innerHTML = `
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class=" aspect-video aspect-h-9">
                    <iframe src="assets/vd.mp4" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen
                            class="w-full h-full">
                    </iframe>
                </div>
                <div class="p-4">
                    <h2 class="text-xl font-semibold mb-2">Lesson 3: Building Projects</h2>
                    <p class="text-sm text-gray-600 mb-4">Learn how to build real-world projects from scratch.</p>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">28:10</span>
                        <div class="flex space-x-2">
                            <button class="bg-gray-200 text-gray-800 px-3 py-1 rounded-md text-sm hover:bg-gray-300">
                                <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                                Next Lesson
                            </button>
                            <button class="bg-[#4A90E2] text-white px-3 py-1 rounded-md text-sm hover:bg-[#357abd]">
                                Complete Lesson
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } else {
        courseContent.innerHTML = `
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-[#4A90E2] rounded-full flex items-center justify-center text-white">
                            JD
                        </div>
                        <div>
                            <h3 class="font-medium">John Doe</h3>
                            <p class="text-sm text-gray-500">Instructor</p>
                        </div>
                    </div>
                    <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm">Bestseller</span>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div>
                        <p class="text-gray-500 text-sm">Duration:</p>
                        <p class="font-medium">12 weeks</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Lessons:</p>
                        <p class="font-medium">24 videos</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Level:</p>
                        <p class="font-medium">Intermediate</p>
                    </div>
                </div>

                <button class="w-full bg-[#4A90E2] text-white py-2 rounded-md hover:bg-[#357abd]">
                    Enroll Now
                </button>
            </div>
        `;
    }
</script>
</body>

</html>