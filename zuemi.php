<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Language Sign-Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Sign Up </h2>
        <form id="signupForm">
            <label for="name">Name:</label>
            <input type="text" id="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" required>

            <button type="submit">Sign Up</button>
        </form>

        <!-- Sign Language Image/Video -->
        <div class="sign-language-help">
            <p>Learn how to sign "Sign Up":</p>
            <video src="sign-up.mp4" controls></video>
        </div>

        <p id="statusMessage"></p>
    </div>

    <script src="script.js"></script>
</body>
</html>
