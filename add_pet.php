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

// Check if the user is logged in and is an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "Admin") {
    // Redirect to the login page or unauthorized page
    header("Location: login.php"); // Redirect to login page or another unauthorized page
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pet_name = $_POST["pet_name"];
    $pet_species = $_POST["pet_species"];
    $pet_breed = $_POST["pet_breed"];
    $pet_age = $_POST["pet_age"];

    // Handle image upload
    $target_dir = "uploads/"; // Specify the directory where images will be stored
    $image_name = basename($_FILES["pet_image"]["name"]);
    $target_file = $target_dir . $image_name;
    $image_upload_success = move_uploaded_file($_FILES["pet_image"]["tmp_name"], $target_file);

    if (!$image_upload_success) {
        // Handle image upload error
        echo "Error uploading image.";
        exit();
    }

    // Insert the pet information into the database
    $sql = "INSERT INTO Pets (name, species, breed, age, profile_picture_url) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssis", $pet_name, $pet_species, $pet_breed, $pet_age, $target_file);

    if ($stmt->execute()) {
        // Pet added successfully
        header("Location: homepage.php"); // Redirect back to the homepage
        exit();
    } else {
        // Handle errors
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>পোষা প্রাণী যোগ করুন</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</head>
<body>
    <!-- Bootstrap Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="homepage.php">Petshare</a>
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
                <li class="nav-item">
                    <a class="nav-link" href="homepage.php">বাড়ি</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">প্রস্থান</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Add Pet Form -->
    <div class="container mt-4">
        <h2>পোষা প্রাণী যোগ করুন</h2>
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="pet_name">পোষা প্রাণীর নাম:</label>
                <input type="text" class="form-control" id="pet_name" name="pet_name" required>
            </div>

            <div class="form-group">
                <label for="pet_species">প্রজাতি:</label>
                <input type="text" class="form-control" id="pet_species" name="pet_species" required>
            </div>

            <div class="form-group">
                <label for="pet_breed">বংশবৃদ্ধি:</label>
                <input type="text" class="form-control" id="pet_breed" name="pet_breed" required>
            </div>

            <div class="form-group">
                <label for="pet_age">বয়স:</label>
                <input type="number" class="form-control" id="pet_age" name="pet_age" required>
            </div>

            <div class="form-group">
                <label for="pet_image">পোষা ইমেজ:</label>
                <input type="file" class="form-control-file" id="pet_image" name="pet_image" required>
            </div>

            <button type="submit" class="btn btn-primary">পোষা প্রাণী যোগ করুন</button>
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
