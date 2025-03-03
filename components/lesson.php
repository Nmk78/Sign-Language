<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Web Development</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .lesson-list::-webkit-scrollbar {
            width: 4px;
        }
        .lesson-list::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .lesson-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-white">
    <!-- Top Navigation -->
    

    <!-- Main Content -->
    <div class="flex h-[calc(100vh-64px)]">
        <!-- Video Player Section -->
        <div class="flex-1 bg-white p-4">
            <div class="aspect-video bg-gray-100 rounded-lg mb-4"></div>
            
            <!-- Course Info -->
            <div class="max-w-3xl mx-auto mt-8">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Advanced Web Development</h1>
                        <p class="text-gray-600">Master modern web technologies with hands-on projects</p>
                    </div>
                    <span class="px-3 py-1 bg-orange-500 text-white rounded-full text-sm">Bestseller</span>
                </div>

                <div class="flex items-center space-x-6 mb-8">
                    <div class="flex items-center space-x-2">
                        <div class="w-12 h-12 bg-[#4A90E2] rounded-full flex items-center justify-center text-white">
                            JD
                        </div>
                        <div>
                            <p class="font-medium">John Doe</p>
                            <p class="text-sm text-gray-500">Instructor</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-6 mb-8">
                    <div>
                        <p class="text-gray-500">Duration:</p>
                        <p class="font-medium">12 weeks</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Lessons:</p>
                        <p class="font-medium">24 videos</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Level:</p>
                        <p class="font-medium">Intermediate</p>
                    </div>
                </div>
            </div>

            <!-- Video Controls -->
            <div class="fixed bottom-0 left-0 right-[400px] p-4 bg-white border-t">
                <div class="flex justify-between gap-4 max-w-3xl mx-auto">
                    <button class="flex-1 py-2 px-4 rounded bg-[#4A90E2] text-white hover:bg-[#357abd] text-center">
                        Complete Lesson
                    </button>
                    <button class="flex-1 py-2 px-4 rounded border border-gray-200 hover:bg-gray-50 text-center">
                        Watch Later
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="w-[400px] bg-gray-50 border-l flex flex-col">
            <!-- Course Progress -->
            <div class="p-6 border-b">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Course Videos</h2>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-2">75% complete</span>
                        <div class="w-32 h-2 bg-gray-200 rounded-full">
                            <div class="w-3/4 h-full bg-green-500 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lesson List -->
            <div class="flex-1 overflow-y-auto lesson-list">
                <div class="p-4 space-y-2">
                    <!-- Lesson 1 -->
                    <div class="flex items-center p-4 rounded-lg bg-white shadow-sm">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-medium">Introduction</h3>
                            <p class="text-sm text-gray-500">Learn the basics and setup</p>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-sm text-gray-500">15:30</span>
                            <span class="text-xs text-green-500">Completed</span>
                        </div>
                    </div>

                    <!-- Add more lessons following the same pattern -->
                    <!-- You can copy and modify the lesson block above for additional lessons -->
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
</body>
</html>