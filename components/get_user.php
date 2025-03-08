<?php
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

if (isset($_GET['username'])) {  // Get username from AJAX request
    $username = $_GET['username'];

    // Query the database for this username
    $stmt = $conn->prepare("SELECT username, profile, email FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {  // If a match is found in the database
        // Return user data as JSON, including username and profile image path
        echo json_encode([
            'username' => $row['username'],
            'profile' => $row['profile'],
            'email' => $row['email']
        ]);
    } else {
        echo json_encode(["error" => "User not found"]);  // No match found
    }
}
$stmt->close();
$conn->close();
?>
