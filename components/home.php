<?php
$navItems = ["Learning", "Instructor", "Enterprise", "Scholarship"];
$features = [
    [
        "title" => "CLASS PROGRAM OPTIONS",
        "description" => "Some theorists focus on a single to overarching purpose of education and see.",
        "bg" => "bg-[#FBF7F4]",
        "text" => "text-primary",
        "icon" => "book-open"
    ],
    [
        "title" => "ACCESS ANYWHERE",
        "description" => "Some theorists focus on a single to overarching purpose of education and see.",
        "bg" => "bg-[#FBF7F4]",
        "text" => "text-primary",
        "icon" => "globe"
    ],
    [
        "title" => "FLEXIBLE TIME",
        "description" => "Some theorists focus on a single to overarching purpose of education and see.",
        "bg" => "bg-[#FBF7F4]",
        "text" => "text-primary",
        "icon" => "clock"
    ]
];

$services = [
    "Research Creation",
    "UX/UI Analysis",
    "Web Development",
    "Market Analysis",
    "Product Design"
];

$courses = [
    [
        "title" => "The Web Design Course",
        "price" => "$80.00",
        "duration" => "2h 40m",
        "rating" => 4.8,
        "students" => 1240,
        "image" => "https://hebbkx1anhila5yf.public.blob.vercel-storage.com/image-5PTvlftctpgPVeHoeVoKmAvWN0Azyx.png"
    ],
    [
        "title" => "UI/UX Design Course",
        "price" => "$120.00",
        "duration" => "1h 30m",
        "rating" => 4.9,
        "students" => 980,
        "image" => "https://hebbkx1anhila5yf.public.blob.vercel-storage.com/image-5PTvlftctpgPVeHoeVoKmAvWN0Azyx.png"
    ],
    [
        "title" => "Product Design",
        "price" => "$90.00",
        "duration" => "2h 15m",
        "rating" => 4.7,
        "students" => 1560,
        "image" => "https://hebbkx1anhila5yf.public.blob.vercel-storage.com/image-5PTvlftctpgPVeHoeVoKmAvWN0Azyx.png"
    ]
];

$testimonials = [
    [
        "name" => "Sarah Johnson",
        "role" => "UX Designer",
        "image" => "https://randomuser.me/api/portraits/women/1.jpg",
        "text" => "EDUTOCK completely transformed my career path. The courses are well-structured and the instructors are industry experts."
    ],
    [
        "name" => "Michael Chen",
        "role" => "Web Developer",
        "image" => "https://randomuser.me/api/portraits/men/2.jpg",
        "text" => "The flexibility of learning at my own pace while getting real-world projects to work on made all the difference in my learning journey."
    ],
    [
        "name" => "Emily Rodriguez",
        "role" => "Product Manager",
        "image" => "https://randomuser.me/api/portraits/women/3.jpg",
        "text" => "I've taken courses from several platforms, but Slient Voice stands out with its practical approach and supportive community."
    ]
];

$faqs = [
    [
        "question" => "How do I enroll in a course?",
        "answer" => "Simply browse our course catalog, select the course you're interested in, and click the 'Enroll Now' button. You can pay using credit card, PayPal, or other available payment methods."
    ],
    [
        "question" => "Are certificates provided upon completion?",
        "answer" => "Yes, all our courses come with a certificate of completion that you can add to your portfolio or LinkedIn profile to showcase your skills."
    ],
    [
        "question" => "Can I access the course content after completion?",
        "answer" => "Once you enroll in a course, you have lifetime access to the content, including any future updates to the course material."
    ],
    [
        "question" => "Do you offer refunds if I'm not satisfied?",
        "answer" => "We offer a 30-day money-back guarantee for all our courses. If you're not satisfied with the content, you can request a full refund within 30 days of purchase."
    ]
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDUTOCK - Online Learning Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        'sans': ['Inter', 'sans-serif'],
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
        
        .hero-gradient {
            background: linear-gradient(to right, #f8fafb 60%, #e8f0f9 40%);
        }
        
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .testimonial-card {
            transition: all 0.3s ease;
        }
        
        .testimonial-card:hover {
            transform: scale(1.03);
        }
        
        .faq-item {
            transition: all 0.3s ease;
        }
        
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .faq-item.active .faq-answer {
            max-height: 500px;
        }
    </style>
</head>

<body class="bg-background text-text">


<!-- Hero Section for Sign Language Website -->
<section class="relative bg-gradient-to-br from-[#154785] to-[#cfe1ff] text-[#ffffff] py-20 md:py-32 overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0 pointer-events-none">
        <!-- Soft Circles -->
        <div class="absolute top-10 left-10 w-20 h-20 bg-white/50 rounded-full blur-lg"></div> 
        <div class="absolute bottom-20 right-20 w-32 h-32 bg-white/40 rounded-full blur-xl"></div>
        <div class="absolute top-1/3 left-1/2 w-16 h-16 bg-white/30 rounded-full blur-lg"></div>

        <!-- Abstract Lines -->
        <div class="absolute bottom-10 left-1/4 w-32 h-1 bg-[#b7c5da]"></div>
        <div class="absolute top-16 right-1/3 w-1 h-40 bg-white/40"></div>

        <!-- Arrows -->
        <svg class="absolute bottom-16 left-12 w-16 h-16 text-[#ffffff] opacity-30" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2L10.59 3.41 18.17 11H2v2h16.17l-7.59 7.59L12 22l10-10L12 2z"/>
        </svg>
        <svg class="absolute top-12 right-20 w-12 h-12 text-white/50 opacity-20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2L14.41 4.41 6.83 12H22v2H6.83l7.58 7.59L12 22 2 12 12 2z"/>
        </svg>

        <!-- Dotted Pattern -->
        <div class="absolute top-1/4 left-1/3 grid grid-cols-3 gap-2">
            <div class="w-2 h-2 bg-[#b7c5da] rounded-full"></div>
            <div class="w-2 h-2 bg-[#b7c5da] rounded-full"></div>
            <div class="w-2 h-2 bg-[#b7c5da] rounded-full"></div>
        </div>
    </div>

    <!-- Content -->
    <div class="relative max-w-7xl mx-auto px-6 lg:px-8 grid md:grid-cols-2 gap-12 items-center">
        <!-- Text Content -->
        <div class="space-y-6">
            <span class="inline-block bg-white/20 text-sm px-4 py-1 rounded-full">Breaking Barriers Through Communication</span>
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">
            "Hands That Speak, <span class="text-accent">Hearts That Connect."</span>
            </h1>
            <p class="text-lg text-white/90 max-w-lg">
                Learn, connect, and communicate effortlessly with our interactive sign language courses. Empower inclusivity with technology.
            </p>
            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="#learn" class="bg-[#ffffff] hover:bg-[#154785] text-black hover:text-white px-8 py-3 rounded-md font-medium transition-all">
                    Start Learning
                </a>
                <a href="#about" class="border border-white/50 hover:bg-white hover:text-primary px-8 py-3 rounded-md font-medium transition-all">
                    Discover More
                </a>
            </div>
        </div>
        
        <!-- Image Section -->
        <div class="relative">
            <img src="/assets/home.jpg" alt="Sign Language Learning" class="w-full max-h-[400px] object-cover rounded-2xl shadow-lg">
            <div class="absolute bottom-4 left-4 bg-surface/90 backdrop-blur-lg p-4 rounded-lg shadow-md flex items-center space-x-3">
                <div class="flex -space-x-2">
                    <img src="https://randomuser.me/api/portraits/men/1.jpg" class="w-10 h-10 rounded-full border-2 border-white" alt="User">
                    <img src="https://randomuser.me/api/portraits/women/2.jpg" class="w-10 h-10 rounded-full border-2 border-white" alt="User">
                    <img src="https://randomuser.me/api/portraits/men/3.jpg" class="w-10 h-10 rounded-full border-2 border-white" alt="User">
                </div>
                <p class="text-sm font-medium text-text">Join 15,000+ learners mastering sign language</p>
            </div>
        </div>
    </div>
</section>



    <!-- Partners Section -->
   <!-- <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-center text-xl font-semibold text-primary mb-12">TRUSTED BY LEADING COMPANIES</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8 items-center justify-items-center opacity-60">
                <img src="https://placehold.co/200x80?text=Sponsor+1" alt="Sponsor 1" class="h-12 grayscale hover:grayscale-0 transition-all duration-300">
                <img src="https://placehold.co/200x80?text=Sponsor+2" alt="Sponsor 2" class="h-12 grayscale hover:grayscale-0 transition-all duration-300">
                <img src="https://placehold.co/200x80?text=Sponsor+3" alt="Sponsor 3" class="h-12 grayscale hover:grayscale-0 transition-all duration-300">
                <img src="https://placehold.co/200x80?text=Sponsor+4" alt="Sponsor 4" class="h-12 grayscale hover:grayscale-0 transition-all duration-300">
                <img src="https://placehold.co/200x80?text=Sponsor+5" alt="Sponsor 5" class="h-12 grayscale hover:grayscale-0 transition-all duration-300">
                <img src="https://placehold.co/200x80?text=Sponsor+6" alt="Sponsor 6" class="h-12 grayscale hover:grayscale-0 transition-all duration-300">
            </div>
        </div>
    </section>-->

    <!-- Features Section -->
<section class="relative py-20 bg-white overflow-hidden" id="features">
    <!-- Top Wave Background -->
    <div class="absolute top-0 left-0 w-full">
        <svg class="w-full h-32 md:h-40 text-blue-50" fill="currentColor" viewBox="0 0 1440 320">
            <path fill-opacity="1" d="M0,128L48,144C96,160,192,192,288,186.7C384,181,480,139,576,122.7C672,107,768,117,864,144C960,171,1056,213,1152,229.3C1248,245,1344,235,1392,229.3L1440,224L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
        </svg>
    </div>

    <!-- Bottom Wave Background -->
    <div class="absolute bottom-0 left-0 w-full">
        <svg class="w-full h-32 md:h-40 text-blue-50" fill="currentColor" viewBox="0 0 1440 320">
            <path fill-opacity="1" d="M0,64L48,96C96,128,192,192,288,186.7C384,181,480,139,576,122.7C672,107,768,117,864,144C960,171,1056,213,1152,229.3C1248,245,1344,235,1392,229.3L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>

    <!-- Floating Elements -->
    <div class="absolute -top-10 left-10 w-24 h-24 bg-blue-100/30 rounded-full blur-3xl"></div>
    <div class="absolute bottom-10 right-20 w-20 h-20 bg-blue-100/30 rounded-full blur-2xl"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <!--<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">-->
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">
                    ONLINE LEARNING FOR REAL LIFE
                </h2>
                <p class="text-text-light text-lg">
                    Discover how our platform adapts to your learning style and schedule
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <?php foreach ($features as $index => $feature): ?>
                    <div class="<?php echo $feature['bg']; ?> p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 group">
                        <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-<?php echo $feature['icon']; ?> text-primary text-xl group-hover:text-white"></i>
                        </div>
                        <h3 class="<?php echo $feature['text']; ?> text-xl font-bold mb-4"><?php echo $feature['title']; ?></h3>
                        <p class="text-text-light"><?php echo $feature['description']; ?></p>
                        <a href="#" class="inline-flex items-center mt-6 text-primary font-medium hover:text-primary-dark transition-colors duration-200">
                            Learn more
                            <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-16 bg-white" id="services">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">EXPERIENCE EXCELLENCE<br>OUR SERVICES</h2>
                    <p class="text-text-light text-lg">
                        We provide comprehensive educational services designed to help you succeed in today's competitive landscape.
                    </p>
                    <div class="space-y-6">
                        <?php foreach ($services as $index => $service): ?>
                            <div class="flex items-center space-x-4 group">
                                <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center group-hover:bg-primary transition-colors duration-300">
                                    <div class="w-3 h-3 bg-primary rounded-full group-hover:bg-white transition-colors duration-300"></div>
                                </div>
                                <span class="text-text group-hover:text-primary transition-colors duration-200 font-medium"><?php echo $service; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="#" class="inline-block bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-md transition-colors duration-200 font-medium">
                        View All Services
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/image-5PTvlftctpgPVeHoeVoKmAvWN0Azyx.png" alt="Interior Design" class="w-full rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300">
                    <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/image-5PTvlftctpgPVeHoeVoKmAvWN0Azyx.png" alt="Product Design" class="w-full rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300 mt-8">
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Section -->
    <section class="py-16 bg-background" id="courses">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">
                    PERFECT ONLINE COURSES<br>FOR YOUR CAREER
                </h2>
                <p class="text-text-light text-lg">
                    Explore our most popular courses designed to help you advance in your career
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <?php foreach ($courses as $index => $course): ?>
                    <div class="bg-white rounded-xl overflow-hidden shadow-lg course-card transition-all duration-300">
                        <div class="relative">
                            <img src="<?php echo $course['image']; ?>" alt="<?php echo $course['title']; ?>" class="w-full aspect-video object-cover">
                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-medium text-primary">
                                <?php echo $course['duration']; ?>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center space-x-2 mb-3">
                                <div class="flex">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <?php if ($i < floor($course['rating'])): ?>
                                            <i class="fas fa-star text-warning text-sm"></i>
                                        <?php elseif ($i < $course['rating']): ?>
                                            <i class="fas fa-star-half-alt text-warning text-sm"></i>
                                        <?php else: ?>
                                            <i class="far fa-star text-warning text-sm"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-text-light text-sm">(<?php echo $course['rating']; ?>)</span>
                            </div>
                            <h3 class="font-semibold text-primary text-xl mb-2"><?php echo $course['title']; ?></h3>
                            <div class="flex items-center text-text-light text-sm mb-4">
                                <i class="fas fa-users mr-2"></i>
                                <span><?php echo number_format($course['students']); ?> students</span>
                            </div>
                            <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                                <span class="text-primary font-bold text-xl"><?php echo $course['price']; ?></span>
                                <button class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-md text-sm transition-colors duration-200">
                                    Enroll Now
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-12">
                <a href="#" class="inline-block border-2 border-primary text-primary hover:bg-primary hover:text-white px-8 py-3 rounded-md transition-colors duration-200 font-medium">
                    View All Courses
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-16 bg-white" id="testimonials">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">
                    WHAT OUR STUDENTS SAY
                </h2>
                <p class="text-text-light text-lg">
                    Hear from our community of learners about their experience with Silent Voice
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <?php foreach ($testimonials as $index => $testimonial): ?>
                    <div class="bg-background p-8 rounded-xl shadow-sm testimonial-card">
                        <div class="flex items-center space-x-4 mb-6">
                            <img src="<?php echo $testimonial['image']; ?>" alt="<?php echo $testimonial['name']; ?>" class="w-14 h-14 rounded-full object-cover">
                            <div>
                                <h4 class="font-semibold text-primary"><?php echo $testimonial['name']; ?></h4>
                                <p class="text-text-light text-sm"><?php echo $testimonial['role']; ?></p>
                            </div>
                        </div>
                        <p class="text-text-light italic">
                            "<?php echo $testimonial['text']; ?>"
                        </p>
                        <div class="flex mt-6">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fas fa-star text-warning text-sm"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 bg-background" id="faq">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">
                    FREQUENTLY ASKED QUESTIONS
                </h2>
                <p class="text-text-light text-lg">
                    Find answers to common questions about our platform and courses
                </p>
            </div>
            <div class="space-y-4">
                <?php foreach ($faqs as $index => $faq): ?>
                    <div class="bg-white p-6 rounded-xl shadow-sm faq-item">
                        <div class="flex justify-between items-center cursor-pointer faq-question">
                            <h3 class="font-semibold text-primary text-lg"><?php echo $faq['question']; ?></h3>
                            <i class="fas fa-chevron-down text-primary transition-transform duration-300"></i>
                        </div>
                        <div class="faq-answer mt-4 text-text-light">
                            <p><?php echo $faq['answer']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Registration Section -->
    <!-- <section class="py-16 bg-primary" id="register">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="text-white">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">REGISTER YOUR ACCOUNT</h2>
                    <p class="text-white/80 text-lg mb-6">Get free access to 50,000+ online courses and join our community of lifelong learners.</p>
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <span>Access to all basic courses</span>
                    </div>
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <span>Free learning materials</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <span>Certification after completion</span>
                    </div>
                </div>
                <div>
                    <form class="bg-white p-8 rounded-xl shadow-lg">
                        <h3 class="text-xl font-semibold text-primary mb-6">Create Your Free Account</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="fullname" class="block text-text-light mb-2 text-sm">Full Name</label>
                                <input type="text" id="fullname" placeholder="John Doe" class="w-full px-4 py-3 rounded-md border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
                            </div>
                            <div>
                                <label for="email" class="block text-text-light mb-2 text-sm">Email Address</label>
                                <input type="email" id="email" placeholder="john@example.com" class="w-full px-4 py-3 rounded-md border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
                            </div>
                            <div>
                                <label for="password" class="block text-text-light mb-2 text-sm">Password</label>
                                <input type="password" id="password" placeholder="••••••••" class="w-full px-4 py-3 rounded-md border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
                            </div>
                            <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-3 rounded-md transition-colors duration-200">
                                Sign Up
                            </button>
                            <p class="text-center text-text-light text-sm">
                                Already have an account? <a href="#" class="text-primary hover:underline">Log in</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section> -->

    <!-- Footer -->
    <footer class="bg-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-12">
                <div>
                    <a href="#" class="text-2xl font-bold text-primary mb-4 inline-block">EDU<span class="text-primary-dark">TOCK</span></a>
                    <p class="text-text-light mb-6">
                        Empowering individuals through quality education and accessible learning opportunities.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-text-light hover:text-primary transition-colors duration-200">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-text-light hover:text-primary transition-colors duration-200">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-text-light hover:text-primary transition-colors duration-200">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-text-light hover:text-primary transition-colors duration-200">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-primary mb-4">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-text-light hover:text-primary transition-colors duration-200">About Us</a></li>
                        <li><a href="#" class="text-text-light hover:text-primary transition-colors duration-200">Our Courses</a></li>
                        <li><a href="#" class="text-text-light hover:text-primary transition-colors duration-200">Instructors</a></li>
                        <li><a href="#" class="text-text-light hover:text-primary transition-colors duration-200">Testimonials</a></li>
                        <li><a href="#" class="text-text-light hover:text-primary transition-colors duration-200">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-primary mb-4">Support</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-text-light hover:text-primary transition-colors duration-200">Help Center</a></li>
                        <li><a href="#" class="text-text-light hover:text-primary transition-colors duration-200">FAQs</a></li>
                        <li><a href="#" class="text-text-light hover:text-primary transition-colors duration-200">Privacy Policy</a></li>
                        <li><a href="#" class="text-text-light hover:text-primary transition-colors duration-200">Terms of Service</a></li>
                        <li><a href="#" class="text-text-light hover:text-primary transition-colors duration-200">Cookie Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-primary mb-4">Contact Info</h4>
                    <ul class="space-y-3">
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-map-marker-alt text-primary mt-1"></i>
                            <span class="text-text-light">123 Education Street, Learning City, 10001</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-phone-alt text-primary mt-1"></i>
                            <span class="text-text-light">+1 (555) 123-4567</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-envelope text-primary mt-1"></i>
                            <span class="text-text-light">info@edutock.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-200 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-text-light text-sm">
                        &copy; <?php echo date('Y'); ?> Silent Voice. All rights reserved.
                    </p>
                    <div class="flex space-x-4 mt-4 md:mt-0">
                        <a href="#" class="text-text-light hover:text-primary transition-colors duration-200 text-sm">Privacy Policy</a>
                        <a href="#" class="text-text-light hover:text-primary transition-colors duration-200 text-sm">Terms of Service</a>
                        <a href="#" class="text-text-light hover:text-primary transition-colors duration-200 text-sm">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // FAQ accordion
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const faqItem = question.parentElement;
                const icon = question.querySelector('i');
                
                // Toggle active class
                faqItem.classList.toggle('active');
                
                // Toggle icon rotation
                if (faqItem.classList.contains('active')) {
                    icon.style.transform = 'rotate(180deg)';
                } else {
                    icon.style.transform = 'rotate(0)';
                }
            });
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    const mobileMenu = document.getElementById('mobile-menu');
                    if (!mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                    }
                }
            });
        });
    </script>
</body>

</html>