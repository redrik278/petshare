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

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    // Redirect to the login page if the user is not logged in
    header("Location: login.php");
    exit();
}

// Retrieve user information from the session
$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];
$user_type = $_SESSION["user_type"];

// Initialize variables
$pet_id = 0; // Initialize to a default value

// Check if pet_id is provided in the URL
if (isset($_GET["pet_id"])) {
    $pet_id = $_GET["pet_id"];

    // Retrieve pet information from the database
    $sql = "SELECT * FROM Pets WHERE pet_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pet_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $pet = $result->fetch_assoc();
    } else {
        echo "Pet not found.";
        exit();
    }

    $stmt->close();
}

// Check if the pet information is available
if (!$pet) {
    echo "Pet not found.";
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the review data from the form
    $rating = $_POST["rating"];
    $review_text = $_POST["review_text"];

    // Insert the review into the database without listing_id
    $insert_sql = "INSERT INTO ReviewsAndRatings (reviewer_id, rating, review_text) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("iis", $user_id, $rating, $review_text);

    if ($insert_stmt->execute()) {
        echo "Review submitted successfully.";
    } else {
        echo "Error submitting review: " . $insert_stmt->error;
    }

    $insert_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Review - Petshare</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</head>
<body>
    <!-- Bootstrap Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="homepage.php">পেটশেয়ার</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if (isset($_SESSION["user_id"])) : ?>
                    <!-- Display request and review buttons for logged-in users -->
                    <li class="nav-item">
                        <a class="nav-link" href="request_pet.php?pet_id=<?php echo $pet["pet_id"]; ?>">অনুরোধ পোষা</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">প্রস্থান</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Review Form -->
    <div class="container mt-4">
        <h2><?php echo $pet["name"]; ?> এর জন্য পর্যালোচনা জমা দিন</h2>
        <form method="post">
            <div class="form-group">
                <label for="rating">রেটিং:</label>
                <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" required>
            </div>

            <div class="form-group">
                <label for="review_text">পুনঃমূল্যায়ন:</label>
                <textarea class="form-control" id="review_text" name="review_text" rows="4" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">পর্যালোচনা জমা দিন</button>
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
