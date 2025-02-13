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
                        primary: '#0B4D2F',
                        secondary: '#B4FF39',
                        beige: '#FBF7F4',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-beige">
    <!-- Header -->
    <header class="bg-primary">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="text-white font-bold text-xl">EDUTOCK</div>
                <?php include 'layout.php'; ?>
                <div class="flex items-center space-x-4">
                    <button class="bg-secondary px-6 py-2 rounded-md text-primary font-medium">Join Now</button>
                    <a href="#" class="text-white">Log in</a>
                </div>
            </div>
        </div>
    </header>

    <?php
    $uri = trim($_SERVER['REQUEST_URI'], '/');

    if ($uri === 'dashboard') {
        require 'components/dashboard.php';
    } else if ($uri === 'profile') {
        require 'components/profile.php';
    } else if ($uri === 'creator') {
        require 'components/creator-profile.php';
    } else if ($uri === 'dashboard') {
        require 'components/dashboard.php';
    } else {
        require 'components/home.php';
    }
    ?>


    <!-- Footer -->
    <footer class="bg-beige py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-primary font-bold text-xl">EDUTOCK</div>
        </div>
    </footer>
</body>

</html>