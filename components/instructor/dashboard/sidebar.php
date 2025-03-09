<?php
if (!function_exists(function: 'renderSideBar')) {
    function renderSideBar($activeTab = 'students')
    {
        // Define the tab names and icons
        $tabs = [
            'statistics' => ['label' => 'Statistics', 'icon' => 'statistics'],
            'courses' => [
                'label' => 'Courses', 
                'icon' => 'book',
                'dropdown' => [
                    'Enrollments' => 'dashboard?tab=enrollements',
                    'Quiz' => '/dashboard?tab=quiz',
                ]
            ],
            'students' => [
                'label' => 'Students', 
                'icon' => 'users',
                'dropdown' => [
                    'All Students' => '#',
                    'Active Students' => '#',
                    'Inactive Students' => '#',
                    // 'Prospective Students' => '#'
                ]
            ],
        ];

        // Define active tab class
        $activeClass = 'bg-[#EEF2FF] text-[#1d4ed8]';
        $activeBg = 'bg-[#EEF2FF]';

        echo '<div class="flex h-full static top-20 min-h-[500px] max-h-100vh z-30 flex-col justify-between bg-[#f8fafb] p-4">
            <div class="flex flex-col gap-4">';

        // Loop through the tabs and render each one
        foreach ($tabs as $tabKey => $tab) {
            $isActive = $activeTab === $tabKey ? $activeClass : '';
            $tabUrl = "?tab=$tabKey"; // Generate the URL with tab query

            // Select the icon for the tab
            $icon = $isActive ? '<div class="text-[#4A90E2]">' . getTabIcon($tab['icon'] . 'Active') . '</div>' : getTabIcon($tab['icon']);

            ?>
            <div class="flex flex-col z-30">
                <div class="flex items-center gap-3 px-3 py-2 rounded-t-xl <?php echo $isActive; ?>">
                    <div class="text-[#0e161b]">
                        <?php echo $icon; ?>
                    </div>
                    <a href="<?php echo $tabUrl; ?>" class="text-[#0e161b] text-sm font-medium leading-normal flex items-center justify-between">
                        <?php echo $tab['label']; ?>
                        <?php if (isset($tab['dropdown'])): ?>
                            <span class="ml-2 transform transition-transform duration-200 <?php echo $activeTab === $tabKey ? 'rotate-90' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M6.646 4.646a.5.5 0 0 1 .708 0L10.207 8l-2.853 2.854a.5.5 0 0 1-.708-.708L8.793 8 6.646 5.854a.5.5 0 0 1 0-.708z"/>
                                </svg>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>
            <?php

            // If this tab has a dropdown and is active, display it
            if (isset($tab['dropdown']) && $isActive) {
                echo '<div class="w-full rounded-b-xl pl-6 ' . $activeBg . '">';
                if ($tabKey === 'courses') {
                    echo '<div class="flex flex-col px-2">';
                    foreach ($tab['dropdown'] as $item => $link) {
                        $itemClass = $item === 'All' ? ' text-[#1d8cd7]' : ' text-[#507a95]';
                        echo '<a class="flex flex-col items-start justify-center  ' . $itemClass . ' py-2" href="' . $link . '">
                            <p class="text-sm font-bold leading-normal tracking-[0.015em]">' . $item . '</p>
                        </a>';
                    }
                    echo '</div>';
                } elseif ($tabKey === 'students') {
                    echo '<div class="flex flex-col px-4 justify-between">';
                    foreach ($tab['dropdown'] as $item => $link) {
                        $itemClass = $item === 'All Students' ? ' text-[#1d8cd7]' : 'text-[#507a95]';
                        echo '<a class="flex flex-col justify-center' . $itemClass . ' pb-[13px] pt-4 flex-1" href="' . $link . '">
                            <p class="text-sm font-bold leading-normal tracking-[0.015em]">' . $item . '</p>
                        </a>';
                    }
                    echo '</div>';
                }
                echo '</div>';
            }

            echo '</div>';
        }

        echo '</div>
                        <button onclick="logout()" class=" mb-10 hover:bg-error text-error border-error border-2 hover:text-white px-4 py-2 rounded-lg">Logout</button>
            
        <script>
            function logout() {
    localStorage.removeItem("username");
    localStorage.removeItem("role");

    fetch("components/logout.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        }
    }).then(() => {
        window.location.href = "/";
    }).catch(error => console.error("Error:", error));
}
        </script>
        </div>';
    }
}


if (!function_exists('getTabIcon')) {
    function getTabIcon($iconName)
    {
        // Return the corresponding SVG icon for each tab
        switch ($iconName) {
            case 'statistics':
                return '<svg viewBox="0 0 24 24" width="24px" height="24px" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><defs><style>.a,.b{fill:none;stroke:#000000;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.5px;}.a{fill-rule:evenodd;}</style></defs><path class="a" d="M2,2V20a2,2,0,0,0,2,2H22"></path><rect class="b" height="6" rx="1.5" width="3" x="6" y="12"></rect><rect class="b" height="6" rx="1.5" width="3" x="12" y="7"></rect><rect class="b" height="6" rx="1.5" width="3" x="18" y="3"></rect></g></svg>';
            case 'book':
                return '<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                        <path d="M208,24H72A32,32,0,0,0,40,56V224a8,8,0,0,0,8,8H192a8,8,0,0,0,0-16H56a16,16,0,0,1,16-16H208a8,8,0,0,0,8-8V32A8,8,0,0,0,208,24Zm-8,160H72a31.82,31.82,0,0,0-16,4.29V56A16,16,0,0,1,72,40H200Z"></path>
                    </svg>';
            case 'list':
                return '<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                        <path d="M224,128a8,8,0,0,1-8,8H104a8,8,0,0,1,0-16H216A8,8,0,0,1,224,128ZM104,72H216a8,8,0,0,0,0-16H104a8,8,0,0,0,0,16ZM216,184H104a8,8,0,0,0,0,16H216a8,8,0,0,0,0-16ZM43.58,55.16,48,52.94V104a8,8,0,0,0,16,0V40a8,8,0,0,0-11.58-7.16l-16,8a8,8,0,0,0,7.16,14.32ZM79.77,156.72a23.73,23.73,0,0,0-9.6-15.95,24.86,24.86,0,0,0-34.11,4.7,23.63,23.63,0,0,0-3.57,6.46,8,8,0,1,0,15,5.47,7.84,7.84,0,0,1,1.18-2.13,8.76,8.76,0,0,1,12-1.59A7.91,7.91,0,0,1,63.93,159a7.64,7.64,0,0,1-1.57,5.78,1,1,0,0,0-.08.11L33.59,203.21A8,8,0,0,0,40,216H72a8,8,0,0,0,0-16H56l19.08-25.53A23.47,23.47,0,0,0,79.77,156.72Z"></path>
                    </svg>';
            case 'pencil':
                return '<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                        <path d="M227.31,73.37,182.63,28.68a16,16,0,0,0-22.63,0L36.69,152A15.86,15.86,0,0,0,32,163.31V208a16,16,0,0,0,16,16H92.69A15.86,15.86,0,0,0,104,219.31L227.31,96a16,16,0,0,0,0-22.63ZM51.31,160,136,75.31,152.69,92,68,176.68ZM48,179.31,76.69,208H48Zm48,25.38L79.31,188,164,103.31,180.69,120Zm96-96L147.31,64l24-24L216,84.68Z"></path>
                    </svg>';
            case 'users':
                return '<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                        <path d="M164.47,195.63a8,8,0,0,1-6.7,12.37H10.23a8,8,0,0,1-6.7-12.37,95.83,95.83,0,0,1,47.22-37.71,60,60,0,1,1,66.5,0A95.83,95.83,0,0,1,164.47,195.63Zm87.91-.15a95.87,95.87,0,0,0-47.13-37.56A60,60,0,0,0,144.7,54.59a4,4,0,0,0-1.33,6A75.83,75.83,0,0,1,147,150.53a4,4,0,0,0,1.07,5.53,112.32,112.32,0,0,1,4.7,6.62Z"></path>
                    </svg>';
            case 'chat':
                return '<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                        <path d="M0,116V196a16,16,0,0,0,16,16h42.15l25.92,25.91a8,8,0,0,0,13.73-5.66L87.81,188H192a16,16,0,0,0,16-16V60a16,16,0,0,0-16-16H64a16,16,0,0,0-16,16v60H16A16,16,0,0,0,0,116ZM64,60h128v104H64Z"></path>
                    </svg>';

            case 'statisticsActive':
                return '<svg viewBox="0 0 24 24" width="24px" height="24px" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><defs><style>.a,.b{fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.5px;}.a{fill-rule:evenodd;}</style></defs><path class="a" d="M2,2V20a2,2,0,0,0,2,2H22"></path><rect class="b" height="6" rx="1.5" width="3" x="6" y="12"></rect><rect class="b" height="6" rx="1.5" width="3" x="12" y="7"></rect><rect class="b" height="6" rx="1.5" width="3" x="18" y="3"></rect></g></svg>';

            case 'bookActive':
                return '<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M208,24H72A32,32,0,0,0,40,56V224a8,8,0,0,0,8,8H192a8,8,0,0,0,0-16H56a16,16,0,0,1,16-16H208a8,8,0,0,0,8-8V32A8,8,0,0,0,208,24Zm-8,160H72a31.82,31.82,0,0,0-16,4.29V56A16,16,0,0,1,72,40H200Z"></path>
                                </svg>';
            case 'listActive':
                return '<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M224,128a8,8,0,0,1-8,8H104a8,8,0,0,1,0-16H216A8,8,0,0,1,224,128ZM104,72H216a8,8,0,0,0,0-16H104a8,8,0,0,0,0,16ZM216,184H104a8,8,0,0,0,0,16H216a8,8,0,0,0,0-16ZM43.58,55.16,48,52.94V104a8,8,0,0,0,16,0V40a8,8,0,0,0-11.58-7.16l-16,8a8,8,0,0,0,7.16,14.32ZM79.77,156.72a23.73,23.73,0,0,0-9.6-15.95,24.86,24.86,0,0,0-34.11,4.7,23.63,23.63,0,0,0-3.57,6.46,8,8,0,1,0,15,5.47,7.84,7.84,0,0,1,1.18-2.13,8.76,8.76,0,0,1,12-1.59A7.91,7.91,0,0,1,63.93,159a7.64,7.64,0,0,1-1.57,5.78,1,1,0,0,0-.08.11L33.59,203.21A8,8,0,0,0,40,216H72a8,8,0,0,0,0-16H56l19.08-25.53A23.47,23.47,0,0,0,79.77,156.72Z"></path>
                                </svg>';
            case 'pencilActive':
                return '<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M227.31,73.37,182.63,28.68a16,16,0,0,0-22.63,0L36.69,152A15.86,15.86,0,0,0,32,163.31V208a16,16,0,0,0,16,16H92.69A15.86,15.86,0,0,0,104,219.31L227.31,96a16,16,0,0,0,0-22.63ZM51.31,160,136,75.31,152.69,92,68,176.68ZM48,179.31,76.69,208H48Zm48,25.38L79.31,188,164,103.31,180.69,120Zm96-96L147.31,64l24-24L216,84.68Z"></path>
                                </svg>';
            case 'usersActive':
                return '<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M164.47,195.63a8,8,0,0,1-6.7,12.37H10.23a8,8,0,0,1-6.7-12.37,95.83,95.83,0,0,1,47.22-37.71,60,60,0,1,1,66.5,0A95.83,95.83,0,0,1,164.47,195.63Zm87.91-.15a95.87,95.87,0,0,0-47.13-37.56A60,60,0,0,0,144.7,54.59a4,4,0,0,0-1.33,6A75.83,75.83,0,0,1,147,150.53a4,4,0,0,0,1.07,5.53,112.32,112.32,0,0,1,4.7,6.62Z"></path>
                                </svg>';
            case 'chatActive':
                return '<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                    <path d="M0,116V196a16,16,0,0,0,16,16h42.15l25.92,25.91a8,8,0,0,0,13.73-5.66L87.81,188H192a16,16,0,0,0,16-16V60a16,16,0,0,0-16-16H64a16,16,0,0,0-16,16v60H16A16,16,0,0,0,0,116ZM64,60h128v104H64Z"></path>
                                </svg>';
            default:
                return '';
        }
    }
}


// Get the tab parameter from the query, default to 'students' if not set
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'students';

renderSideBar($activeTab);
?>