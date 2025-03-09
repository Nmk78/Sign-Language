<?php

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

    include 'components/signup.php';


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
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role'];

// Hash the password
$password_hash = password_hash($password, PASSWORD_BCRYPT);

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $password_hash, $role);

// Execute the statement
if ($stmt->execute()) {
    echo "New record created successfully";
    echo "<script>
            window.location.href = '/signin';
          </script>";
} else {
    // echo "Error: " . $stmt->error;
    echo"Failed to create account";
    echo "<script>
            window.location.href = '/signup';
          </script>";
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
?>