<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Sign Language Project</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex justify-center items-center min-h-screen bg-gray-100">

    <div class="bg-white p-6 rounded-2xl shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-4">Sign Up (Staff)</h2>
        
        <form class="space-y-4">
            <!-- Full Name -->
            <div>
                <label class="block text-gray-600 font-semibold">Full Name</label>
                <input type="text" placeholder="Enter your full name"
                    class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring focus:ring-blue-300"
                    required>
            </div>

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
                <input type="password" placeholder="Create a password"
                    class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring focus:ring-blue-300"
                    required>
            </div>

            <!-- Role Selection -->
            <div>
                <label class="block text-gray-600 font-semibold">Role</label>
                <select class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring focus:ring-blue-300" required>
                    <option value="">Select your role</option>
                    <option value="teacher">Sign Language Teacher</option>
                    <option value="interpreter">Interpreter</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>

            <!-- Video Upload (Optional) -->
            <div>
                <label class="block text-gray-600 font-semibold">Introduce Yourself (Video)</label>
                <input type="file" accept="video/*"
                    class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring focus:ring-blue-300">
                <p class="text-xs text-gray-500 mt-1">Upload a short video introducing yourself in sign language.</p>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition">
                Sign Up
            </button>
        </form>

        <!-- Already have an account -->
        <p class="text-sm text-center text-gray-500 mt-4">
            Already have an account? <a href="#" class="text-blue-500 hover:underline">Log in</a>
        </p>
    </div>

</body>
</html>
