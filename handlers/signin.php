<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "sign_language";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from POST request
$email = $_POST['email'];
$password = $_POST['password'];

// Prepare SQL to check user credentials
$stmt = $conn->prepare("SELECT id, username, password_hash, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

// Include toaster.js


// Loading screen code
echo '
<div id="loading" class="fixed w-screen h-screen inset-0 top-5 z-40 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-surface opacity-80 p-8 rounded-lg w-96">
        <div class="text-center">
            <h2 class="text-2xl font-bold">Creating your account</h2>
            <p class="text-gray-600 mt-1">Please wait...</p>
        </div>
            <img class="size-10 mx-auto" src="assets/loading.svg" />
    </div>  
</div>';

include 'components/signin.php';


// Check if user exists
if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $username, $password_hash, $role);
    $stmt->fetch();

    // Verify the entered password with the hashed password
    if (password_verify($password, $password_hash)) {
        // Store user info in session
        $_SESSION['user'] = [
            'user_id' => $id,
            'username' => $username,
            'role' => $role,
            'logged_in' => true
        ];

        // Display success toast and redirect after a delay
        echo "<script>
                showToast('success', 'Login Successful', 'Welcome back, $username!');
                setTimeout(function() {
                    window.location.href = '/'; // Redirect after the toast
                }, 2000); // Delay to allow toast to show
              </script>";
        exit();
    } else {
        // Show error toast for incorrect password
        echo "<script>
                showToast('error', 'Incorrect Password', 'Please check your password and try again');
                setTimeout(() => {
                                document.getElementById('loading').classList.add('hidden'); // Hide loading screen
                    window.location.href = '/signin'; 
                }, 1000); // Redirect after 3 seconds
              </script>";
    }
} else {
    // Show error toast for email not found
    echo "<script>
            showToast('error', 'No Account Found', 'We couldn\'t find an account with this email');
            setTimeout(() => {
            document.getElementById('loading').classList.add('hidden'); // Hide loading screen

                window.location.href = '/signin'; // Redirect to signin page
            }, 1000); // Redirect after 3 seconds
          </script>";
}

// Close statement and connection
$stmt->close();
$conn->close();
