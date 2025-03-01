<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Language LMS - Sign Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white mt-12 flex rounded-lg shadow-lg w-full max-w-4xl mx-auto overflow-hidden">
        
        <!-- Left Side (Signup Form) -->
        <div class="w-full md:w-1/2 p-8">
            <div class="text-center mb-6">
                <p class="text-gray-600">Signup to your account</p>
            </div>
            <form action="signingup" method="POST" class="space-y-6">
                <div>
                    <label for="fullname" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" id="fullname" name="username" required
                        class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" required
                        class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" required
                        class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" required
                        class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Sign Up
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a href="/signin" class="font-medium text-indigo-600 hover:text-indigo-500">Log in</a>
                </p>
            </div>
        </div>

        <!-- Right Side -->
        <div class="hidden md:flex flex-col justify-center items-center w-1/2 bg-indigo-600 text-white p-10">
            <h2 class="text-3xl font-bold">Join Sign Language LMS</h2>
            <p class="mt-4 text-center text-lg">Sign up and start your journey in learning sign language with ease.</p>
        </div>

    </div>
</body>

</html>
