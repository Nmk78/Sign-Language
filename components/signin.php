<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Language LMS - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center w-full min-h-screen">
    <div class="bg-white mt-32 flex rounded-lg shadow-lg w-full max-w-4xl mx-auto overflow-hidden">
        <!-- Left Side -->
        <div class="hidden md:flex flex-col justify-center items-center w-1/2 bg-indigo-600 text-white p-10">
            <h2 class="text-3xl font-bold">Welcome to Sign Language LMS</h2>
            <p class="mt-4 text-center text-lg">Learn sign language effortlessly with our interactive lessons and expert guidance.</p>
        </div>

        <!-- Right Side (Login Form) -->
        <div class="w-full md:w-1/2 p-8">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Welcome Back!</h1>
                <p class="text-gray-600 mt-1">Login to continue learning</p>
            </div>

            <form action="signingin" id="signinForm" method="POST" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" required 
                        class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" required 
                        class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember-me" name="remember-me" 
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 text-sm text-gray-900">Remember me</label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">Forgot password?</a>
                    </div>
                </div> -->

                <div>
                    <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Sign in
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="/signup" class="font-medium text-indigo-600 hover:text-indigo-500">Sign up</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
<script>
        document.getElementById("signinForm").addEventListener("submit", function(event) {
            // Get the username from the input field
            let username = document.getElementById("fullname").value;

            // Store in local storage
            localStorage.setItem("username", username);
        });
</script>
