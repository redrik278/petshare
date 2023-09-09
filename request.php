<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "petshare";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET["logout"])) {
    // Destroy the session
    session_destroy();
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
} else {
    // User not logged in
    header("Location: login.php");
    exit();
}

if (isset($_GET["pet_id"])) {
    $pet_id = $_GET["pet_id"];

    // Check if the user has already requested this pet
    $checkRequestQuery = "SELECT * FROM PetRequests WHERE user_id = $user_id AND pet_id = $pet_id";
    $checkRequestResult = $conn->query($checkRequestQuery);

    if ($checkRequestResult && $checkRequestResult->num_rows > 0) {
        // User has already requested this pet
        header("Location: pet_details.php?pet_id=$pet_id&message=already_requested");
        exit();
    } else {
        // User has not requested this pet, proceed with the request
        $insertRequestQuery = "INSERT INTO PetRequests (user_id, pet_id, status) VALUES ($user_id, $pet_id, 'pending')";

        if ($conn->query($insertRequestQuery) === TRUE) {
            header("Location: pet_details.php?pet_id=$pet_id&message=request_submitted");
            exit();
        } else {
            echo "Error submitting request: " . $conn->error;
        }
    }
}

$conn->close();
?>
