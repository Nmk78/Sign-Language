<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Sign Language Project</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex justify-center items-center min-h-screen bg-gray-100">

    <div class="bg-white p-6 rounded-2xl shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-4">Sign In (Staff)</h2>
        
        <form class="space-y-4">
            <!-- Email -->
            <div>
                <label class="block text-gray-600 font-semibold">Email</label>
                <input type="email" placeholder="Enter your email"
                    class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring focus:ring-blue-300"
                    required>
            </div>

            <!-- Password -->
            <div>
                <label class="block text-gray-600 font-semibold">Password</label>
                <input type="password" placeholder="Enter your password"
                    class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring focus:ring-blue-300"
                    required>
            </div>

            <!-- Video Authentication (Optional) -->
            <div>
                <label class="block text-gray-600 font-semibold">Sign-In with Video (Optional)</label>
                <input type="file" accept="video/*"
                    class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring focus:ring-blue-300">
                <p class="text-xs text-gray-500 mt-1">Upload a short video signing your name for identity verification.</p>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition">
                Sign In
            </button>
        </form>

        <!-- Forgot Password & Sign Up Links -->
        <div class="text-sm text-center text-gray-500 mt-4">
            <a href="#" class="text-blue-500 hover:underline">Forgot password?</a> <br>
            Don't have an account? <a href="#" class="text-blue-500 hover:underline">Sign up</a>
        </div>
    </div>

</body>
</html>
