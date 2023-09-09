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
$user_type = $_SESSION["user_type"];

// Check if the user is an admin
if ($user_type !== "Admin") {
    // Redirect to a different page (e.g., homepage.php) if the user is not an admin
    header("Location: homepage.php");
    exit();
}

// Handle request status update
if (isset($_POST["update_status"])) {
    $request_id = $_POST["request_id"];
    $new_status = $_POST["status"];
    
    // Update the status of the pet request
    $updateQuery = "UPDATE PetRequests SET status = '$new_status' WHERE request_id = $request_id";
    if ($conn->query($updateQuery) === TRUE) {
        // Status updated successfully
        // You can add additional logic here, such as sending notifications to users
    } else {
        echo "Error updating status: " . $conn->error;
    }
}

// Retrieve pet requests from the database
$petRequests = [];
$sql = "SELECT * FROM PetRequests";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $petRequests[] = $row;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>পোষা প্রাণী অনুরোধ</title>
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
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Display pet requests in a table with status update options -->
    <div class="container mt-4">
        <h3>পোষা প্রাণী অনুরোধ</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>অনুরোধ আইডি </th>
                        <th>ব্যবহারকারী আইডি</th>
                        <th>পোষা আইডি</th>
                        <th>অবস্থা</th>
                        <th>অনুরোধ তারিখ</th>
                        <th>কর্ম</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($petRequests as $request) : ?>
                        <tr>
                            <td><?php echo $request["request_id"]; ?></td>
                            <td><?php echo $request["user_id"]; ?></td>
                            <td><?php echo $request["pet_id"]; ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="request_id" value="<?php echo $request["request_id"]; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="Pending" <?php if ($request["status"] === "Pending") echo "selected"; ?>>Pending</option>
                                        <option value="Approved" <?php if ($request["status"] === "Approved") echo "selected"; ?>>Approved</option>
                                        <option value="Denied" <?php if ($request["status"] === "Denied") echo "selected"; ?>>Denied</option>
                                    </select>
                                    <input type="hidden" name="update_status">
                                </form>
                            </td>
                            <td><?php echo $request["request_date"]; ?></td>
                            <td>
                                <?php if ($request["status"] === "Approved"): ?>
                                    <button class="btn btn-success" onclick="notifyUser(<?php echo $request["user_id"]; ?>)">অবহিত</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
    <script>
    function notifyUser(userId) {
        // You can add code here to notify the user
        alert("User with ID " + userId + " has been notified.");
    }
    </script>
</body>
</html>
