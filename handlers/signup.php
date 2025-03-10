<?php
// Loading overlay
echo '
    <div id="loading" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-surface opacity-80 p-8 rounded-lg w-96">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-text">Creating your account</h2>
                <p class="text-text-light mt-1">Please wait...</p>
            </div>
            <img class="size-10 mx-auto mt-4" src="assets/loading.svg" alt="Loading animation" />
        </div>  
    </div>';

include 'components/signup.php';

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

// Get data from POST request with basic sanitization
$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];
$role = filter_var($_POST['role'], FILTER_SANITIZE_STRING);

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>
            alert('Invalid email format');
            window.location.href = '/signup';
          </script>";
    exit();
}

// Check if email already exists
$checkStmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    // Email already exists
    echo "<script>
            document.getElementById('loading').style.display = 'none';
            alert('Email already exists. Please use a different email.');
            window.location.href = '/signup';
          </script>";
    $checkStmt->close();
    $conn->close();
    exit();
}
$checkStmt->close();

// Hash the password
$password_hash = password_hash($password, PASSWORD_BCRYPT);

// Prepare and bind insert statement
$stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $password_hash, $role);

// Execute the statement
if ($stmt->execute()) {
    echo "<script>
            document.getElementById('loading').style.display = 'none';
            alert('Account created successfully!');
            window.location.href = '/signin';
          </script>";
} else {
    echo "<script>
            document.getElementById('loading').style.display = 'none';
            alert('Failed to create account: " . $stmt->error . "');
            window.location.href = '/signup';
          </script>";
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>