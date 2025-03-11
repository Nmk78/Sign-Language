<?php
// Initialize variables
$nameErr = $emailErr = $subjectErr = $messageErr = "";
$name = $email = $subject = $message = "";
$formSubmitted = false;

// Form processing
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = test_input($_POST["name"]);
        // Check if name only contains letters and whitespace
        if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
            $nameErr = "Only letters and white space allowed";
        }
    }
    
    // Validate email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        // Check if email address is well-formed
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }
    
    // Validate subject
    if (empty($_POST["subject"])) {
        $subjectErr = "Subject is required";
    } else {
        $subject = test_input($_POST["subject"]);
    }
    
    // Validate message
    if (empty($_POST["message"])) {
        $messageErr = "Message is required";
    } else {
        $message = test_input($_POST["message"]);
    }
    
    // If no errors, process the form
    if (empty($nameErr) && empty($emailErr) && empty($subjectErr) && empty($messageErr)) {
        // Email recipient - change this to your email
        $to = "your-email@example.com";
        
        // Email headers
        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        // Email content
        $email_content = "Name: $name\n";
        $email_content .= "Email: $email\n\n";
        $email_content .= "Message:\n$message\n";
        
        // Send email
        if (mail($to, $subject, $email_content, $headers)) {
            $formSubmitted = true;
            // Clear form fields after successful submission
            $name = $email = $subject = $message = "";
        } else {
            echo "<p class='error'>Sorry, there was an error sending your message. Please try again later.</p>";
        }
    }
}

// Function to sanitize form data
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Sign Language Resources</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 1px solid #ddd;
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .intro {
            margin-bottom: 30px;
            text-align: center;
        }
        
        .contact-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        textarea {
            height: 150px;
            resize: vertical;
        }
        
        .error {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
        }
        
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #2980b9;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .contact-info {
            margin-top: 40px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        
        .contact-item {
            flex-basis: 48%;
            margin-bottom: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .contact-item h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        
        footer {
            text-align: center;
            margin-top: 50px;
            padding: 20px 0;
            border-top: 1px solid #ddd;
            color: #7f8c8d;
        }
        
        @media (max-width: 768px) {
            .contact-item {
                flex-basis: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Contact Us</h1>
            <p>We're here to help with your sign language journey</p>
        </header>
        
        <div class="intro">
            <p>Have questions about sign language or need assistance with our resources? Fill out the form below and we'll get back to you as soon as possible.</p>
        </div>
        
        <?php if ($formSubmitted): ?>
            <div class="success-message">
                <p>Thank you for your message! We'll get back to you shortly.</p>
            </div>
        <?php endif; ?>
        
        <div class="contact-form">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo $name; ?>">
                    <span class="error"><?php echo $nameErr; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>">
                    <span class="error"><?php echo $emailErr; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="subject">Subject:</label>
                    <input type="text" id="subject" name="subject" value="<?php echo $subject; ?>">
                    <span class="error"><?php echo $subjectErr; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="message">Message:</label>
                    <textarea id="message" name="message"><?php echo $message; ?></textarea>
                    <span class="error"><?php echo $messageErr; ?></span>
                </div>
                
                <button type="submit">Send Message</button>
            </form>
        </div>
        
        <div class="contact-info">
            <div class="contact-item">
                <h3>Our Location</h3>
                <p>123 Sign Language Street<br>
                Communication City, SC 12345</p>
            </div>
            
            <div class="contact-item">
                <h3>Contact Details</h3>
                <p>Email: info@signlanguage.example<br>
                Phone: (123) 456-7890</p>
            </div>
            
            <div class="contact-item">
                <h3>Office Hours</h3>
                <p>Monday - Friday: 9am - 5pm<br>
                Saturday: 10am - 2pm<br>
                Sunday: Closed</p>
            </div>
            
            <div class="contact-item">
                <h3>Follow Us</h3>
                <p>Facebook: @SignLanguageResources<br>
                Twitter: @SignLangLearn<br>
                Instagram: @SignLanguageCommunity</p>
            </div>
        </div>
        
        <footer>
            <p>&copy; <?php echo date("Y"); ?> Sign Language Resources. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>