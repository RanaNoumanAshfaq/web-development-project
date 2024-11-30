<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nouman";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $profile_pic = $_FILES['profile_pic'];

    // Check for upload errors
    if ($profile_pic['error'] === 0) {
        $file_tmp = $profile_pic['tmp_name'];
        $file_name = basename($profile_pic['name']);
        $upload_dir = "uploads/";
        $upload_path = $upload_dir . $file_name;

        // Ensure the upload directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Move uploaded file
        if (move_uploaded_file($file_tmp, $upload_path)) {
            // Save file path to database
            $sql = "UPDATE users SET profile_pic = '$upload_path' WHERE id = $user_id";
            if ($conn->query($sql) === TRUE) {
                echo "Profile picture updated successfully!";
                header("Location: homepage.php");
                exit;
            } else {
                echo "Database error: " . $conn->error;
            }
        } else {
            echo "File upload failed.";
        }
    } else {
        echo "Error in file upload.";
    }
} else {
    echo "Invalid request.";
}
$conn->close();
?>
