<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_form";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect and sanitize inputs
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $password = isset($_POST['password']) ? $conn->real_escape_string($_POST['password']) : '';
    $gender = isset($_POST['gender']) ? $conn->real_escape_string($_POST['gender']) : '';
    $city = isset($_POST['city']) ? $conn->real_escape_string($_POST['city']) : '';
    $address = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : '';
    $agreed_to_terms = isset($_POST['agreed_to_terms']) ? 1 : 0;

    // File upload handling
    $file_path = "";
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        $file_path = "uploads/" . basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $file_path);
    }

    // SQL query
    $sql = "INSERT INTO contacts (name, email, password, gender, city, file_path, address, agreed_to_terms) 
            VALUES ('$name', '$email', '$password', '$gender', '$city', '$file_path', '$address', '$agreed_to_terms')";

if ($conn->query($sql) === TRUE) {
    // Form submit hone ke baad seedha home page pe redirect karega
    header("Location: Travel.html");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
}

// Close Connection
$conn->close();
?>
