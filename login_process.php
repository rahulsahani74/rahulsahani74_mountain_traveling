<?php
// Start session
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "travel_form");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error variable
$error = "";

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    session_destroy();
    header("Location: ?");
    exit();
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Query to fetch user data
    $sql = "SELECT * FROM contacts WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password (assuming plain text passwords; use password_verify for hashed passwords)
        if ($password === $user['password']) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['gender'] = $user['gender'];
            $_SESSION['city'] = $user['city'];
            $_SESSION['address'] = $user['address'];
            $_SESSION['file_path'] = $user['file_path'];
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No user found with this email.";
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
    <title>Login and Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .container {
            max-width: 500px;
            margin-top: 50px;
        }
        .profile-container {
            text-align: center;
            margin-top: 50px;
            color: #FFE4E1;
        }
        .profile-container img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
        }
    </style>
    <!--CSS LINK-->
    <link rel="stylesheet" href="login.css">
     <!--GOOGLE FONT LINK-->
     <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
</head>
<body>

<!-- Background Video -->
<video autoplay muted loop class="bg-video">
        <source src="mountain.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

<div class="container">
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Profile Page -->
        <div class="profile-container">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
            <img src="<?php echo htmlspecialchars($_SESSION['file_path']); ?>" alt="Profile Picture">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($_SESSION['gender']); ?></p>
            <p><strong>City:</strong> <?php echo htmlspecialchars($_SESSION['city']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($_SESSION['address']); ?></p>
            <a href="?logout=true" class="btn btn-danger">Logout</a>
        </div>
    <?php else: ?>
        <!-- Login Form -->
        <div class="login-container">
            <div class="login-card">
                <h6 class="text-center mb-4">Welcome to Mountain Traveling</h6>
                <h3 class="text-center mb-4">Login</h3>
                <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <a href="Travel.html" class="back-to-home">Back to Home</a>
            </div>
        </div>
    <?php endif; ?>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
