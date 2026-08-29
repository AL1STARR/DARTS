<?php
session_start();

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // TEMPORARY LOGIN (for testing only)
    $correct_email = "admin@gmail.com";
    $correct_password = "12345";

    // Validation
    if (empty($email) || empty($password)) {
        echo "Please fill in all fields.";
        exit();
    }

    // Check credentials
    if ($email === $correct_email && $password === $correct_password) {
        
        // Store session
        $_SESSION['user'] = $email;

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();

    } else {
        echo "Invalid email or password.";
    }
}
?>