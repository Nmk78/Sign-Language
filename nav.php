<?php
function renderHeader($activePage = 'home', $role = "instructor")
{
    $navItems = [
        'home' => '/',
        'category' => '/category',
        'About' => '/about',
        'Content' => '/content',
        'dashboard' => '/dashboard?tab=statistics',
        'profile' => '/profile',
        'courseDetails' => '/courseDetails',
    ];

    echo '<header class="bg-primary">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <nav class="hidden md:flex space-x-8">';
    foreach ($navItems as $name => $url) {
        $activeClass = $activePage === $name ? 'text-white underline' : 'text-white';
        echo "<a href='{$url}' class='{$activeClass} hover:text-accent transition-colors'>" . ucfirst(strtolower($name)) . "</a>";
    }
    echo        '</nav>
                
            </div>
        </div>
    </header>';
}

function getActiveRoute($url)
{
    $path = parse_url($url, PHP_URL_PATH);
    $segments = explode('/', rtrim($path, '/'));
    return end($segments);
}

$currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$activePage = getActiveRoute($currentUrl);

renderHeader($activePage, role: "instructor");
