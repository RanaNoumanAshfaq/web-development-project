<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Fetch user details from session
$username = htmlspecialchars($_SESSION['username']);
$profilePicture = !empty($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default-profile.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
</head>
<body>
    <h1>Welcome, <?php echo $username; ?></h1>
    <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" class="profile-pic">
    <p>Your profile details will be shown here...</p>
    <!-- Add more sections like email, order history, etc. -->
</body>
</html>
