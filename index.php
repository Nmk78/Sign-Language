<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// echo '<pre>';
// print_r($_SESSION);
// echo '</pre>';

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

    <script src="utils/toaster.js"></script>

    <script>
        // Function to check URL query parameters and open/close chatbox accordingly
        function checkChatState() {
            const urlParams = new URLSearchParams(window.location.search);
            const chatState = urlParams.get('chat');
            let chatBox = document.getElementById("chat-box");

            if (chatBox) {
                chatBox.classList.toggle("hidden", chatState !== 'open');
            }
        }


        // Run the checkChatState function when the page loads
        window.onload = checkChatState;

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

        function toggleChat() {
            let chatBox = document.getElementById('chat-box');
            chatBox.classList.toggle('hidden');
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
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['logged_in']): ?>
                        <a href="/profile" class="text-white font-bold hover:underline">
                            Hello, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>
                        </a>
                        <img id="profile-avatar" class="size-8 cursor-pointer" src="/assets/userAvatar.svg" alt="" onclick="window.location.href='/profile';">
                    <?php else: ?>
                        <a href="/signup" class="bg-white px-6 py-2 rounded-md text-primary font-medium">Join Now</a>
                        <a href="/signin" class="text-white">Log in</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <?php
    $urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = trim($urlPath, '/');

    switch ($uri) {
        case 'signingup':
            require 'handlers/signup.php';
            break;
        case 'signingin':
            require 'handlers/signin.php';
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
        case 'ai':
            require 'components/ai.php';
            break;
        case 'courseDetails':
            require 'components/courseDetails.php';
            break;
        case '':
            require 'components/home.php';
            break;
        default:
            require 'components/notfound.php';
            break;
    }
    ?>

    <?php include 'components/ai.php' ?>

    <?php
    $hiddenPages = ['dashboard', 'profile', 'settings', 'signin', 'signup','category']; // Pages where the footer should be hidden

    // Extract the last segment of the URI (ignoring query parameters)
    $currentPage = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

    if (!in_array($uri, $hiddenPages)) {
        echo '<footer class="bg-background py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-primary font-bold text-xl">EDUTOCK</div>
        </div>
    </footer>';
    }
    ?>

    <script>
        function openAvatarModal() {
            document.getElementById('avatar-modal').classList.remove('hidden');
        }

        // Close Modal
        function closeAvatarModal() {
            document.getElementById('avatar-modal').classList.add('hidden');
        }

        // Select Avatar
        function selectAvatar(avatarPath) {
            localStorage.setItem('selectedAvatar', avatarPath);
            document.querySelectorAll('#profile-avatar').forEach(function(avatar) {
                avatar.src = avatarPath;
            });
            closeAvatarModal();
        }

        // Load Avatar from Local Storage
        document.addEventListener('DOMContentLoaded', function() {
            const storedAvatar = localStorage.getItem('selectedAvatar');
            if (storedAvatar) {
                document.querySelectorAll('#profile-avatar').forEach(function(avatar) {
                    avatar.src = storedAvatar;
                });
            }
        });
    </script>

</body>

</html>