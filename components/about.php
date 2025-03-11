<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Silent Voice | Sign Language Learning Platform</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#1e5dac',
                        'primary-dark': '#154785',
                        'secondary': '#b7c5da',
                        'accent': '#eae2e4',
                        'success': '#10B981',
                        'warning': '#F1C40F',
                        'error': '#E74C3C',
                        'background': '#f8fafb',
                        'surface': '#FFFFFF',
                        'text': '#333333',
                        'text-light': '#7F8C8D',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-background min-h-screen flex flex-col max-w-6xl mx-auto">
    
            
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-primary text-white py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">About Silent Voice</h1>
                <p class="text-xl opacity-90">
                    <?php
                    // PHP variable for dynamic content
                    $mission = "Hands That Speak, Hearts That Connect.";
                    echo $mission;
                    ?>
                </p>
            </div>
        </div>
    </section>

   <!-- Our Story Section -->
<section class="py-20 bg-gradient-to-b from-white to-gray-100" id="our-story">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <!-- Image Section -->
            <div class="flex justify-center">
                <div class="relative w-64 h-64 md:w-80 md:h-80">
                    <img src="/assets/logo.png" alt="Silent Voice Team" class="w-full h-full object-cover rounded-full shadow-xl border-4 border-primary">
                </div>
            </div>

            <!-- Story Content -->
            <div class="md:w-3/4">
                <h2 class="text-3xl text-[#1e5dac] md:text-4xl font-bold text-text mb-6">
                    Our Beginning, Our Vision
                </h2>
                <div class="space-y-4 text-[#154785] text-text leading-relaxed">
                    <p>Silent Voice was born in December 2025—not just as a project, but as a mission. As part of our PHP development journey, we set out to create something meaningful: a bridge between the hearing and the deaf communities.</p>
                    <p>What started as a coursework initiative soon turned into a vision for the future. We saw beyond the lines of code and recognized the power of accessibility, inclusion, and breaking communication barriers.</p>
                    <p>Our goal is simple yet profound—to make sign language learning accessible, engaging, and impactful. This platform is just the beginning, and we are committed to evolving, growing, and reaching more people with each step forward.</p>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- Our Mission Section -->
    <section class="py-16 bg-secondary ">
        <div class="container mx-auto px-20 text-center max-w-7xl mx-auto">
            <h2 class="text-3xl text-[#154785] font-bold text-text mb-12">Our Mission & Values</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php
                // PHP array for dynamic content
                $values = [
                    [
                        "title" => "Inclusivity",
                        "description" => "We believe in creating a world where everyone can communicate freely, regardless of hearing ability.",
                        "icon" => "users"
                    ],
                    [
                        "title" => "Education",
                        "description" => "We're committed to providing high-quality, accessible sign language education to break down communication barriers.",
                        "icon" => "book-open"
                    ],
                    [
                        "title" => "Community",
                        "description" => "We foster a supportive community where learners can practice, connect, and grow together.",
                        "icon" => "heart"
                    ]
                ];

                foreach ($values as $value) {
                    echo '<div class="bg-surface p-8 rounded-lg shadow-md">';
                    echo '<div class="flex justify-center mb-4">';
                    
                    // Icon system based on the icon name
                    if ($value["icon"] == "users") {
                        echo '<div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center">';
                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>';
                        echo '</div>';
                    } elseif ($value["icon"] == "book-open") {
                        echo '<div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center">';
                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>';
                        echo '</div>';
                    } else {
                        echo '<div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center">';
                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path></svg>';
                        echo '</div>';
                    }
                    
                    echo '</div>';
                    echo '<h3 class="text-xl font-semibold text-text mb-2">' . $value["title"] . '</h3>';
                    echo '<p class="text-text-light">' . $value["description"] . '</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Our Team Section -->
    <section class="py-16 bg-surface">
        <div class="container mx-auto max-w-6xl">
            <h2 class="text-3xl font-bold text-[#154785] mb-2 text-center">Our Team</h2>
            <p class="text-text-light text-center mb-12 max-w-2xl mx-auto">Meet the passionate educators, linguists, and technologists who make Silent Voice possible.</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php
                // PHP array for team members
                $team = [
                    [
                        "name" => "Sarah Chen",
                        "position" => "Founder & CEO",
                        "bio" => "ASL educator with 10+ years of experience and a passion for accessible communication.",
                        "image" => "https://via.placeholder.com/300x300"
                    ],
                    [
                        "name" => "Michael Rodriguez",
                        "position" => "Head of Education",
                        "bio" => "Certified interpreter and curriculum developer specializing in interactive learning methods.",
                        "image" => "https://via.placeholder.com/300x300"
                    ],
                    [
                        "name" => "Aisha Johnson",
                        "position" => "Technology Director",
                        "bio" => "Tech innovator focused on creating accessible digital learning experiences.",
                        "image" => "https://via.placeholder.com/300x300"
                    ],
                    [
                        "name" => "David Kim",
                        "position" => "Community Manager",
                        "bio" => "Deaf advocate and educator building bridges between hearing and deaf communities.",
                        "image" => "https://via.placeholder.com/300x300"
                    ]
                ];

                foreach ($team as $member) {
                    echo '<div class="bg-surface rounded-lg shadow-md overflow-hidden border border-secondary/30 transition-transform hover:-translate-y-1">';
                    echo '<img src="' . $member["image"] . '" alt="' . $member["name"] . '" class="w-full h-64 object-cover">';
                    echo '<div class="p-6">';
                    echo '<h3 class="text-xl font-semibold text-text">' . $member["name"] . '</h3>';
                    echo '<p class="text-primary font-medium mb-2">' . $member["position"] . '</p>';
                    echo '<p class="text-text-light">' . $member["bio"] . '</p>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Impact Section -->
    <section class="py-16 bg-primary text-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold mb-12 text-center">Our Impact</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <?php
                // PHP variables for statistics
                $studentsCount = 15000;
                $countriesCount = 42;
                $coursesCount = 24;
                
                $stats = [
                    [
                        "number" => number_format($studentsCount),
                        "label" => "Students Worldwide",
                        "icon" => "users"
                    ],
                    [
                        "number" => $countriesCount,
                        "label" => "Countries Reached",
                        "icon" => "globe"
                    ],
                    [
                        "number" => $coursesCount,
                        "label" => "Courses Available",
                        "icon" => "book"
                    ]
                ];
                
                foreach ($stats as $stat) {
                    echo '<div class="p-6">';
                    echo '<p class="text-4xl font-bold mb-2">' . $stat["number"] . '+</p>';
                    echo '<p class="text-xl opacity-90">' . $stat["label"] . '</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-16 bg-background">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-text mb-12 text-center">What Our Students Say</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <?php
                // PHP array for testimonials
                $testimonials = [
                    [
                        "quote" => "Silent Voice changed my life. I can now communicate with my deaf cousin, and it's brought us so much closer. The courses are engaging and the community is incredibly supportive.",
                        "name" => "Emily T.",
                        "role" => "Student"
                    ],
                    [
                        "quote" => "As a teacher working with deaf students, I needed to improve my sign language skills quickly. Silent Voice provided exactly what I needed - structured lessons that I could fit around my busy schedule.",
                        "name" => "Marcus L.",
                        "role" => "Elementary School Teacher"
                    ]
                ];
                
                foreach ($testimonials as $testimonial) {
                    echo '<div class="bg-surface p-8 rounded-lg shadow-md border border-secondary/30">';
                    echo '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" class="text-secondary mb-4" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"></path><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"></path></svg>';
                    echo '<p class="text-text mb-6">' . $testimonial["quote"] . '</p>';
                    echo '<div class="flex items-center">';
                    echo '<div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center mr-3">';
                    echo '<span class="text-primary font-bold">' . substr($testimonial["name"], 0, 1) . '</span>';
                    echo '</div>';
                    echo '<div>';
                    echo '<p class="font-semibold text-text">' . $testimonial["name"] . '</p>';
                    echo '<p class="text-text-light text-sm">' . $testimonial["role"] . '</p>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-secondary">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-[#154785] mb-4">Ready to Start Your Sign Language Journey?</h2>
            <p class="text-text-light mb-8 max-w-2xl mx-auto">Join thousands of students learning to communicate in new ways. Our structured courses make learning sign language accessible to everyone.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="signup.php" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-md transition-colors font-medium">Get Started</a>
            </div>
        </div>
    </section>

    <!-- Footer -->


    <?php
    // Additional PHP functionality can be added here
    // For example, tracking page views or handling form submissions
    
    // Track page view
    $page = "About Us";
    $timestamp = date('Y-m-d H:i:s');
    $user_ip = $_SERVER['REMOTE_ADDR'];
    
    // In a real application, you might log this information to a database
    // $query = "INSERT INTO page_views (page, timestamp, ip_address) VALUES ('$page', '$timestamp', '$user_ip')";
    // mysqli_query($connection, $query);
    ?>
</body>
</html>