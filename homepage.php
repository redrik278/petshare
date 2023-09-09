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

// Retrieve user information from the database
$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];
$user_type = $_SESSION["user_type"];

// Retrieve a list of pets from the database
$pets = [];
$sql = "SELECT * FROM Pets";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pets[] = $row;
    }
}

// Suggest pets based on average ratings (top 3)
function calculateAverageRating($petId) {
    global $conn;

    $sql = "SELECT AVG(rating) AS avg_rating FROM ReviewsAndRatings WHERE listing_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $petId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return ($row["avg_rating"]) ? $row["avg_rating"] : 0;
}

// Sort the pets by average rating in descending order
usort($pets, function($a, $b) {
    $avgRatingA = calculateAverageRating($a["pet_id"]);
    $avgRatingB = calculateAverageRating($b["pet_id"]);

    return $avgRatingB <=> $avgRatingA;
});

// Limit the number of suggested pets to 3
$suggestedPets = array_slice($pets, 0, 3);


// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petshare - Homepage</title>
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
                <?php if ($user_type == "Admin") : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="add_pet.php">পোষা প্রাণী যোগ করুন</a>
                    </li>
                <?php endif; ?>
                <?php if ($user_type == "User") : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="notification.php">বিজ্ঞপ্তি</a>
                    </li>
                <?php endif; ?>
                <?php if ($user_type == "Admin") : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="all_req.php">অনুরোধ</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">প্রস্থান</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Display suggested pets -->
    <div class="container mt-4">
        <h3>আপনার জন্য প্রস্তাবিত</h3>
        <div class="row">
            <?php foreach ($suggestedPets as $suggestedPet) : ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <a href="pet_details.php?pet_id=<?php echo $suggestedPet["pet_id"]; ?>" style="text-decoration: none; color: inherit;">
                            <img src="<?php echo $suggestedPet["profile_picture_url"]; ?>" class="card-img-top img-fluid" alt="Pet Image" style="max-height: 200px;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $suggestedPet["name"]; ?></h5>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    
    <!-- Display a list of pets in rows of three -->
    <div class="container mt-4">
        <h3>উপলব্ধ পোষা প্রাণী</h3>
        <div class="row">
            <?php foreach ($pets as $pet) : ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <?php if (isset($_SESSION["user_id"])) : ?>
                            <!-- If the user is logged in, provide a link to pet_details.php -->
                            <a href="pet_details.php?pet_id=<?php echo $pet["pet_id"]; ?>" style="text-decoration: none; color: inherit;">
                                <img src="<?php echo $pet["profile_picture_url"]; ?>" class="card-img-top img-fluid" alt="Pet Image" style="max-height: 200px;">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $pet["name"]; ?></h5>
                                </div>
                            </a>
                        <?php else : ?>
                            <!-- If the user is not logged in, provide a link to the login page -->
                            <a href="login.php" style="text-decoration: none; color: inherit;">
                                <img src="<?php echo $pet["profile_picture_url"]; ?>" class="card-img-top img-fluid" alt="Pet Image" style="max-height: 200px;">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $pet["name"]; ?></h5>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>



    <!-- Footer -->
    <footer class="footer mt-4">
        <div class="container text-center">
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
