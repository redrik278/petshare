<?php
session_start(); // Start the session

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "petshare";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check if user exists in Users table
    $checkUserQuery = "SELECT * FROM Users WHERE username='$username' AND password_hash='$password'";
    $userResult = $conn->query($checkUserQuery);

    if ($userResult->num_rows > 0) {
        // User login successful
        $user = $userResult->fetch_assoc();
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["username"] = $username;
        $_SESSION["user_type"] = $user["user_type"];

        // Redirect to the user's dashboard or home page based on user type
        if ($user["user_type"] == "Admin") {
            header("Location: homepage.php");
        } else {
            header("Location: homepage.php");
        }
        exit();
    } else {
        $loginError = true;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>প্রবেশ করুন</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- Custom CSS for button hover effect -->
    <style>
        /* Button hover effect */
        .btn-primary:hover {
            background-color: #007bff;
        }
    </style>
</head>
<body>
    <!-- Bootstrap Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">পেটশেয়ার</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="signup.php">নিবন্ধন করুন</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">প্রবেশ করুন</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="container mt-4">
        <h2>প্রবেশ করুন</h2>
        <?php if (isset($loginError) && $loginError) : ?>
            <div class="alert alert-danger">অবৈধ লগইন শংসাপত্রের</div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="form-group">
                <label for="username">ব্যবহারকারীর নাম:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">পাসওয়ার্ড:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary">প্রবেশ করুন</button>
        </form>
    </div>
    <!-- Footer -->
    <footer class="footer mt-4">
        <div class="container fixed-bottom text-center">
            <!-- Social Media Links -->
            <a href="https://www.facebook.com/your-facebook-page" target="_blank" class="btn btn-outline-primary btn-social mx-1">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="https://www.linkedin.com/in/your-linkedin-profile" target="_blank" class="btn btn-outline-primary btn-social mx-1">
                <i class="fab fa-linkedin-in"></i>
            </a>
            <a href="https://github.com/your-github-profile" target="_blank" class="btn btn-outline-primary btn-social mx-1">
                <i class="fab fa-github"></i>
            </a>
        </div>
    </footer>

    <!-- Include Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
