<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "petshare";

// Create a connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the database exists, create if not
$createDbQuery = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($createDbQuery) === TRUE) {
    echo "ডাটাবেস ইতিমধ্যে বিদ্যমান<br>";
} else {
    echo "ডাটাবেস তৈরিতে ত্রুটি: " . $conn->error . "<br>";
}

$conn->select_db($dbname);

// Check if the tables exist, create if not
if (!tableExists($conn, 'Users') || !tableExists($conn, 'Pets') || !tableExists($conn, 'PetListings') || !tableExists($conn, 'BookingRequests') || !tableExists($conn, 'ReviewsAndRatings') || !tableExists($conn, 'Conversations') || !tableExists($conn, 'PetRequests')|| !tableExists($conn, 'Messages')) {
    include 'create_tb.php';
}

if ($_
