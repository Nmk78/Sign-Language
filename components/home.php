<?php
$navItems = ["Learning", "Instructor", "Enterprise", "Scholarship"];
$features = [
    [
        "title" => "CLASS PROGRAM OPTIONS",
        "description" => "Some theorists focus on a single to overarching purpose of education and see.",
        "bg" => "bg-primary"
    ],
    [
        "title" => "ACCESS ANYWHERE",
        "description" => "Some theorists focus on a single to overarching purpose of education and see.",
        "bg" => "bg-[#FBF7F4]"
    ],
    [
        "title" => "FLEXIBLE TIME",
        "description" => "Some theorists focus on a single to overarching purpose of education and see.",
        "bg" => "bg-[#FBF7F4]"
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
        "duration" => "2h 40m"
    ],
    [
        "title" => "UI/UX Design Course",
        "price" => "$120.00",
        "duration" => "1h 30m"
    ],
    [
        "title" => "Product Design",
        "price" => "$90.00",
        "duration" => "2h 15m"
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
                        'background': '#F5F5F5',
                        'surface': '#FFFFFF',
                        'text': '#333333',
                        'text-light': '#7F8C8D',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-beige">
    <!-- Hero Section -->
    <section class="bg-beige">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <div class="space-y-6">
                    <div class="flex items-center space-x-2">
                        <div class="bg-primary text-white text-sm p-1">86%</div>
                        <span class="text-primary">Students in Average</span>
                    </div>
                    <h1 class="text-5xl font-bold text-primary leading-tight">
                        GETWAY TO YOUR<br>KNOWLEDGE<br>UNIVERSE
                    </h1>
                </div>
                <div class="relative">
                    <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/image-jNLOT8D3ifET6hAc7vRlsBqodzVLf8.png" alt="Student" class="w-full">
                </div>
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-center text-xl font-semibold text-primary mb-12">WE ARE SUPPORTED BY</h2>
            <div class="grid grid-cols-6 gap-8 items-center justify-items-center opacity-60">
                <img src="path-to-merck-logo.png" alt="Merck" class="h-8">
                <img src="path-to-nio-logo.png" alt="NIO" class="h-8">
                <img src="path-to-defist-logo.png" alt="DeFist" class="h-8">
                <img src="path-to-merck-logo.png" alt="Merck" class="h-8">
                <img src="path-to-shopify-logo.png" alt="Shopify" class="h-8">
                <img src="path-to-demio-logo.png" alt="Demio" class="h-8">
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-primary mb-12">
                ONLINE LEARNING FOR<br>REAL LIFE, EXPLORE COURSE
            </h2>
            <div class="grid md:grid-cols-3 gap-8">
                <?php foreach ($features as $feature): ?>
                    <div class="<?php echo $feature['bg']; ?> p-8 rounded-lg">
                        <h3 class="text-xl font-bold text-primary mb-4"><?php echo $feature['title']; ?></h3>
                        <p class="text-gray-600"><?php echo $feature['description']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12">
                <div>
                    <h2 class="text-2xl font-bold text-primary mb-8">EXPERIENCE EXCELLENCE<br>OUR SERVICES</h2>
                    <div class="space-y-4">
                        <?php foreach ($services as $service): ?>
                            <div class="flex items-center space-x-4">
                                <div class="w-2 h-2 bg-primary rounded-full"></div>
                                <span class="text-gray-600"><?php echo $service; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/image-5PTvlftctpgPVeHoeVoKmAvWN0Azyx.png" alt="Interior Design" class="w-full rounded-lg">
                    <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/image-5PTvlftctpgPVeHoeVoKmAvWN0Azyx.png" alt="Product Design" class="w-full rounded-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Section -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-primary mb-12">PERFECT ONLINE COURSE<br>FOR YOUR CAREER</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <?php foreach ($courses as $course): ?>
                    <div class="bg-white rounded-lg overflow-hidden shadow-lg">
                        <div class="aspect-video bg-gray-100"></div>
                        <div class="p-6">
                            <h3 class="font-semibold text-primary"><?php echo $course['title']; ?></h3>
                            <div class="flex justify-between items-center mt-4">
                                <span class="text-primary font-bold"><?php echo $course['price']; ?></span>
                                <span class="text-gray-500"><?php echo $course['duration']; ?></span>
                            </div>
                            <div class="flex justify-between items-center mt-4">
                                <div class="flex space-x-2">
                                    <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                                    <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                                    <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                                </div>
                                <button class="bg-primary text-white px-4 py-2 rounded-md text-sm">Enroll Now</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Registration Section -->
    <section class="bg-primary py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12">
                <div class="text-white">
                    <h2 class="text-2xl font-bold mb-4">REGISTER YOUR ACCOUNT</h2>
                    <p>Get free access to 50000+ online course</p>
                </div>
                <form class="space-y-4">
                    <input type="text" placeholder="Full Name" class="w-full px-4 py-3 rounded-md bg-white">
                    <input type="email" placeholder="Email Address" class="w-full px-4 py-3 rounded-md bg-white">
                    <button type="submit" class="w-full bg-secondary text-primary font-bold py-3 rounded-md">
                        Sign Up
                    </button>
                </form>
            </div>
        </div>
    </section>

</body>

</html>