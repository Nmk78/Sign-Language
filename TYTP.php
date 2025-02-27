<?php
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
<body class="h-screen w-screen bg-background flex items-center justify-center">
    <div class="bg-surface p-8 rounded-none shadow-lg">
        <h1 class="text-3xl font-bold text-text mb-4">Login</h1>
        <form action="TYTP.php" method="POST">
            <div class="mb-4">
                <label for="email" class="block text-text-light">Email</label>
                <input type="email" name="email" id="email" class="w-full px-4 py-2 rounded-lg bg-surface border border-text-light focus:border-primary focus:ring focus:ring-primary/10" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-text-light">Password</label>
                <input type="password" name="password" id="password" class="w-full px-4 py-2 rounded-lg bg-surface border border-text-light focus:border-primary focus:ring focus:ring-primary/10" required>
            </div>
            <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-primary-dark transition-colors">Login</button>
        </form>
    </div>
    
</body>
</html>