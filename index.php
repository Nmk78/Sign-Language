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
                        'background': '#f8fafb',
                        'surface': '#FFFFFF',
                        'text': '#333333',
                        'text-light': '#7F8C8D',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-background">
    <!-- Header -->
    <header class="bg-primary sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="text-white font-bold text-xl">EDUTOCK</div>
                <?php include 'nav.php'; ?>
                <div class="flex items-center space-x-4">
                    <button class="bg-white px-6 py-2 rounded-md text-primary font-medium">Join Now</button>
                    <a href="/signin" class="text-white">Log in</a>
                </div>
            </div>
        </div>
    </header>

    <?php
    $urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = trim($urlPath, '/');

    switch ($uri) {
        case 'upl':
            require 'components/instructor/dashboard/addCourse.php';
            break;
        case 'signin':
            require 'components/signin.php';
            break;
        case 'signup':
            require 'components/signup.php';
            break;
        case 'dashboard':
            require 'components/dashboard.php';
            break;
        case 'category':
            require 'components/categories.php';
            break;
        case 'profile':
            require 'components/profile.php';
            break;
        case 'creator':
            require 'components/creator-profile.php';
            break;
        default:
            require 'components/home.php';
            break;
    }
    ?>


    <?php
    $hiddenPages = ['dashboard', 'profile', 'settings','signin','signup']; // Pages where the footer should be hidden

    // Extract the last segment of the URI (ignoring query parameters)
    $currentPage = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

    if (!in_array($currentPage, $hiddenPages)) {
        echo '<footer class="bg-background py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-primary font-bold text-xl">EDUTOCK</div>
        </div>
    </footer>';
    }
    ?>


</body>

</html>