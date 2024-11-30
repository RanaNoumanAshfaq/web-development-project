<?php
// Database connection
$host = 'localhost'; // Your database host
$dbname = 'nouman'; // Database name
$user = 'root'; // Database username
$password = ''; // Database password

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $profilePicture = '';

    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $targetDir = "uploads/";
        $fileName = uniqid() . "_" . basename($_FILES['profile_picture']['name']);
        $targetFile = $targetDir . $fileName;

        // Validate file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
                $profilePicture = $targetFile;
            } else {
                $message = "Failed to upload profile picture.";
            }
        } else {
            $message = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    }

    // Insert or update user details
    $stmt = $conn->prepare("INSERT INTO users (username, email, profile_picture) VALUES (:username, :email, :profile_picture) 
                            ON DUPLICATE KEY UPDATE email = :email, profile_picture = :profile_picture");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':profile_picture', $profilePicture);
    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
    } else {
        $message = "Failed to update profile.";
    }
}

// Fetch user details
$userData = [];
if (isset($_GET['username'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $_GET['username']);
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #4facfe, #00f2fe);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .profile-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
            position: relative;
        }
        .profile-container h2 {
            color: #4facfe;
        }
        input[type="text"], input[type="email"], input[type="file"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            padding: 10px 20px;
            background: #4facfe;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #00b4d8;
        }
        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 10px auto;
            border: 2px solid #4facfe;
            background: url('<?php echo isset($userData["profile_picture"]) ? $userData["profile_picture"] : "https://via.placeholder.com/100"; ?>') no-repeat center center/cover;
        }
        .home-btn {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 8px 16px;
            background: #4facfe;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }
        .home-btn:hover {
            background: #00b4d8;
        }
        .message {
            color: green;
            margin-bottom: 10px;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <a href="homepage.php" class="home-btn">Home</a>
        <h2>User Profile</h2>
        <div class="profile-picture"></div>
        <form action="userprofile.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="username" placeholder="Username" value="<?php echo $userData['username'] ?? ''; ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?php echo $userData['email'] ?? ''; ?>" required>
            <input type="file" name="profile_picture">
            <button type="submit">Update Profile</button>
        </form>
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
