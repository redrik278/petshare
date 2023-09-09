<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</head>
<body>
    <!-- Bootstrap Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">পেটশেয়ার</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="register.php">নিবন্ধন করুন</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">প্রবেশ করুন</a>
                </li>
            </ul>
        </div>
    </nav>
    
    <!-- Registration Form -->
    <div class="container mt-4">
        <h2>নিবন্ধন</h2>
        <form method="post" action="signup.php" id="registrationForm">
            <label for="user_type">ব্যবহারকারীর ধরন:</label>
            <select class="form-control" id="user_type" name="user_type">
                <option value="Admin">Admin</option>
                <option value="User">User</option>
            </select><br>
    
            <label for="username">ব্যবহারকারীর নাম:</label>
            <input type="text" class="form-control" id="username" name="username" required pattern="^[A-Za-z0-9_]{3,15}$" title="সময়ের সাথে অক্ষর, সংখ্যা এবং আন্ডারস্কোর এর মধ্যে 3-15 টি অক্ষরের মধ্যে একটি নাম নির্ধারণ করুন"><br>
            
            <label for="email">ইমেইল:</label>
            <input type="email" class="form-control" id="email" name="email" required pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$"><br>
            
            <label for="password">পাসওয়ার্ড:</label>
            <input type="password" class="form-control" id="password" name="password" required pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$" title="সম্প্রীত পাসওয়ার্ড নির্মাণ করুন: কমপক্ষে 8 টি অক্ষর এবং একটি সংখ্যা"><br>
            
            <button type="submit" class="btn btn-primary">নিবন্ধন করুন</button>
        </form>
        
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
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user_type = $_POST["user_type"];
            $username = $_POST["username"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            
            // Use preg_match to validate using regex
            if (!preg_match("/^[A-Za-z0-9_]{3,15}$/", $username)) {
                echo "<div class='alert alert-danger'>সঠিক ব্যবহারকারীর নাম প্রদান করুন (3-15 অক্ষর, সংখ্যা এবং আন্ডারস্কোর)</div>";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "<div class='alert alert-danger'>সঠিক ইমেইল ঠিকানা প্রদান করুন</div>";
            } elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
                echo "<div class='alert alert-danger'>সঠিক পাসওয়ার্ড প্রদান করুন (কমপক্ষে 8 অক্ষর এবং একটি সংখ্যা)</div>";
            } else {
                // Check if the email already exists in the database
                $checkEmailQuery = "SELECT * FROM Users WHERE email = '$email'";
                $result = $conn->query($checkEmailQuery);
            
                if ($result && $result->num_rows > 0) {
                    // Email already exists, show a warning
                    echo "<div class='alert alert-danger'>ই - মেইল ​​টি আগে থেকেই আছে. একটি ভিন্ন ইমেল ব্যবহার করুন.</div>";
                } else {
                    // Email is unique, proceed with registration
                    $insertUserQuery = "INSERT INTO Users (username, email, password_hash, user_type) VALUES ('$username', '$email', '$password', '$user_type')";
                    if ($conn->query($insertUserQuery) === TRUE) {
                        echo "<div class='alert alert-success'>নিবন্ধন সফলভাবে</div>";
                        
                        // Retrieve the newly inserted user's ID
                        $userId = $conn->insert_id;
                    } else {
                        echo "<div class='alert alert-danger'>ব্যবহারকারীর অ্যাকাউন্ট তৈরিতে ত্রুটি৷</div>" . $conn->error . "<br>";
                    }
                }
            }
        }
        
        function tableExists($conn, $tableName) {
            $result = $conn->query("SHOW TABLES LIKE '$tableName'");
            return $result && $result->num_rows > 0;
        }
        // Close the connection
        $conn->close();
        ?>
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
