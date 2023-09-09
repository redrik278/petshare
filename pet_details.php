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

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["pet_id"])) {
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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>পোষা প্রাণীর বিবরণ</title>
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
                    <a class="nav-link" href="logout.php">প্রস্থান</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Pet Details -->
    <div class="container mt-4">
            <h2>পোষা প্রাণীর বিবরণ</h2>
            <div class="card">
                <img src="<?php echo $pet["profile_picture_url"]; ?>" class="card-img-top img-fluid" alt="Pet Image" style="max-height: 200px;">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $pet["name"]; ?></h5>
                    <p class="card-text"><strong>প্রজাতি:</strong> <?php echo $pet["species"]; ?></p>
                    <p class="card-text"><strong>বংশবৃদ্ধি:</strong> <?php echo $pet["breed"]; ?></p>
                    <p class="card-text"><strong>বয়স:</strong> <?php echo $pet["age"]; ?></p>
                    <!-- Add more pet information here -->
                    <!-- Add a link to request.php with the pet_id as a parameter -->
                    <?php if (isset($_SESSION["user_id"]) && $_SESSION["user_type"] !== "Admin") : ?>
                        <div class="card-footer">
                            <a class="btn btn-secondary btn-block" href="request.php?pet_id=<?php echo $pet["pet_id"]; ?>">পোষা প্রাণী অনুরোধ</a>
                            <a href="review.php?pet_id=<?php echo $pet["pet_id"]; ?>" class="btn btn-secondary btn-block">পুনঃমূল্যায়ন</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
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
