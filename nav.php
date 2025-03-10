<?php
function renderHeader($activePage = 'home', $role = "instructor")
{

    // TODO Test
    //FIXME  FIx this

    //improve this

    $navItems = [
        'home' => '/',
        'category' => '/category',
        'About' => '/about',
        'Contact' => '/contact',
        // 'dashboard' => '/dashboard?tab=statistics',
        // 'profile' => '/profile',
        // 'courseDetails' => '/courseDetails',
    ];

    // TODO

    echo '<header class="bg-primary text-primary-dark shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <nav class="hidden md:flex space-x-8" aria-label="Main navigation">
                ' . implode('', array_map(function($name, $url) use ($activePage) {
                    $activeClass = $activePage === $name 
                        ? 'text-primary-dark underline font-semibold border-b-2 border-primary-dark' 
                        : 'text-primary-dark hover:text-primary-dark/80 hover:border-b-2 hover:border-primary-dark/50';
                    return sprintf(
                        '<a href="%s" class="%s transition-all duration-200 ease-in-out" title="%s">%s</a>',
                        htmlspecialchars($url),
                        $activeClass,
                        ucfirst(strtolower($name)),
                        ucfirst(strtolower($name))
                    );
                }, array_keys($navItems), $navItems)) . '
            </nav>
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
