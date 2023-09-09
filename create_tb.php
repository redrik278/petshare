<?php
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

// SQL queries to create tables for the "petshare" database
$queries = [
    "CREATE TABLE IF NOT EXISTS Users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        user_type ENUM('Admin', 'User') DEFAULT 'User'
    )",
    
    "CREATE TABLE IF NOT EXISTS Pets (
        pet_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50),
        species VARCHAR(50),
        breed VARCHAR(50),
        age INT,
        gender ENUM('Male', 'Female', 'Other'),
        size ENUM('Small', 'Medium', 'Large'),
        description TEXT,
        profile_picture_url VARCHAR(255)
    )",
    
    "CREATE TABLE IF NOT EXISTS PetListings (
        listing_id INT AUTO_INCREMENT PRIMARY KEY,
        pet_id INT,
        listing_title VARCHAR(255) NOT NULL,
        listing_description TEXT,
        availability_start_date DATE,
        availability_end_date DATE,
        location VARCHAR(255),
        FOREIGN KEY (pet_id) REFERENCES Pets(pet_id)
    )",
    
    "CREATE TABLE IF NOT EXISTS BookingRequests (
        request_id INT AUTO_INCREMENT PRIMARY KEY,
        listing_id INT,
        requester_id INT,
        status ENUM('Pending', 'Accepted', 'Declined') DEFAULT 'Pending',
        request_message TEXT,
        request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (listing_id) REFERENCES PetListings(listing_id),
        FOREIGN KEY (requester_id) REFERENCES Users(user_id)
    )",
    
    "CREATE TABLE IF NOT EXISTS ReviewsAndRatings (
        review_id INT AUTO_INCREMENT PRIMARY KEY,
        listing_id INT,
        reviewer_id INT,
        rating INT,
        review_text TEXT,
        review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (reviewer_id) REFERENCES Users(user_id)
    )",
    
    "CREATE TABLE IF NOT EXISTS Conversations (
        conversation_id INT AUTO_INCREMENT PRIMARY KEY,
        user1_id INT,
        user2_id INT,
        FOREIGN KEY (user1_id) REFERENCES Users(user_id),
        FOREIGN KEY (user2_id) REFERENCES Users(user_id)
    )",
    "CREATE TABLE IF NOT EXISTS PetRequests (
        request_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        pet_id INT,
        status ENUM('Pending', 'Approved', 'Denied') DEFAULT 'Pending',
        request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES Users(user_id),
        FOREIGN KEY (pet_id) REFERENCES Pets(pet_id)
    )",
    
    "CREATE TABLE IF NOT EXISTS Messages (
        message_id INT AUTO_INCREMENT PRIMARY KEY,
        conversation_id INT,
        sender_id INT,
        message_text TEXT,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (conversation_id) REFERENCES Conversations(conversation_id),
        FOREIGN KEY (sender_id) REFERENCES Users(user_id)
    )"
];

// Execute each query
foreach ($queries as $query) {
    if ($conn->query($query) !== TRUE) {
        echo "Error creating table: " . $conn->error;
    }
}

echo "Tables created successfully";

// Close the connection
$conn->close();
?>
