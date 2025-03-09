<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Sign Language Learning</title>
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

        .hand-animation {
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .hand-animation:hover {
            transform: translateY(-5px);
        }

        .blur-bg {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
    </style>
</head>

<body class="bg-white text-dark min-h-screen flex items-center justify-center p-4 font-sans">

    <div class="relative w-full max-w-6xl mx-auto bg-background overflow-hidden">
        <div class="absolute top-0 right-0 w-1/2 h-1/2 bg-secondary rounded-bl-full -z-10 opacity-70"></div>

        <div class="max-w-5xl w-full mx-auto px-4 py-8 md:py-12">
            <div class="flex flex-col md:flex-row items-center gap-12 md:gap-16">
                <!-- Left content -->
                <div class="flex-1 space-y-6">
                    <div class="inline-block gradient-bg text-white px-3 py-1 rounded-full text-sm font-medium mb-2">
                        404 Error
                    </div>

                    <h1 class="text-4xl md:text-5xl font-bold tracking-tight">
                        We couldn't find<br>that page
                    </h1>

                    <p class="text-gray-600 text-lg">
                        The page you're looking for doesn't exist or has been moved.
                    </p>

                    <div class="pt-4 flex flex-col sm:flex-row gap-4">
                        <a href="<?php echo htmlspecialchars('/'); ?>" class="inline-flex items-center justify-center px-6 py-3 bg-primary hover:bg-opacity-90 text-white font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl">
                            Return Home
                        </a>

                        <a href="#" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 hover:border-primary text-primary font-medium rounded-lg transition-all duration-200">
                            Contact Support
                        </a>
                    </div>
                </div>

                <!-- Right content - Modern sign language illustration -->
                <div class="flex-1 flex justify-center">
                    <div class="relative w-64 h-64 md:w-80 md:h-80">
                        <!-- Modern abstract hand shapes -->
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full h-full">
                            <div class="absolute top-0 left-0 w-40 h-40 gradient-bg rounded-3xl opacity-20 animate-pulse" style="animation-duration: 3s;"></div>
                            <div class="absolute bottom-0 right-0 w-40 h-40 gradient-bg rounded-full opacity-10 animate-pulse" style="animation-duration: 4s;"></div>

                            <!-- Hand sign illustrations -->
                            <div class="relative z-10 flex items-center justify-center h-full">
                                <div class="hand-animation">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-48 h-48 text-primary">
                                        <path d="M18 11V6a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v0"></path>
                                        <path d="M14 10V4a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v2"></path>
                                        <path d="M10 10.5V6a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v8"></path>
                                        <path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Subtle entrance animation
            const mainContent = document.querySelector('.max-w-4xl');
            mainContent.style.opacity = '0';
            mainContent.style.transform = 'translateY(20px)';
            mainContent.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';

            setTimeout(() => {
                mainContent.style.opacity = '1';
                mainContent.style.transform = 'translateY(0)';
            }, 100);

            // Log 404 error
            console.log('404 error: Page not found at', window.location.pathname);

            <?php
            // PHP code to log the 404 error
            $current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';
            $ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';

            // In a real application, you would log this to a file or database
            // error_log("404 Error: $current_url | IP: $ip_address | User Agent: $user_agent");
            ?>
        });
    </script>
</body>

</html>