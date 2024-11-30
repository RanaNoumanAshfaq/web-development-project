<?php
// Start the session at the top of the page
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nouman";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize search term
$searchTerm = "";

// Check if the form is submitted
if (isset($_POST['search'])) {
    $searchTerm = $_POST['search_term'];
    // Sanitize the search term to prevent SQL injection
    $searchTerm = $conn->real_escape_string($searchTerm);
    // Query to search for books with the term in title or author
    $sql = "SELECT * FROM books WHERE title LIKE '%$searchTerm%' OR author LIKE '%$searchTerm%'";
} else {
    // If no search term is provided, initialize an empty result
    $sql = "";
}

// Execute the query only if a search term is provided
if (!empty($sql)) {
    $result = $conn->query($sql);
} else {
    $result = null; // No search results if no query is made
}

// Handle logout
if (isset($_POST['logout'])) {
    // Destroy the session to log out
    session_destroy();
    header("Location: login.php"); // Redirect to login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Book Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Font Awesome -->
    <style>
        /* Your existing CSS */.user-profile {
    padding: 20px;
    text-align: center;
    color: white;
}

.profile-info {
    display: inline-block;
    text-align: center;
    margin-top: 20px;
}

.profile-pic {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
}

.user-profile p {
    font-size: 1.2rem;
}
body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            background: url('bg.jfif') no-repeat center center fixed;
            background-size: cover;
        }
        header {
            background-color: #3498db;
            color: white;
            padding: 20px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }
        header img {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            max-width: 100px;
        }
        header .nav-links {
            margin-right: auto;
            display: flex;
            gap: 20px;
            align-items: center;
        }
        header .nav-links a {
            text-decoration: none;
            color: white;
            font-size: 1.5rem;
            transition: color 0.3s;
        }
        header .nav-links a:hover {
            color: #3498db;
        }
        .hero-section {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 100px 20px;
            text-align: center;
            color: white;
            position: relative;
            z-index: 1;
        }
        .hero-section h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        .hero-section p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        .cta-button {
            background-color: #3498db;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            font-size: 1.2rem;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .cta-button:hover {
            background-color: #2980b9;
        }
        .featured-books {
            padding: 50px 20px;
            text-align: center;
        }
        .featured-books h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }
        .featured-books .books-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .book {
            background-color: white;
            width: 250px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .book img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }
        .book h3 {
            font-size: 1.2rem;
            margin-top: 10px;
        }
        .book p {
            font-size: 1rem;
            color: #888;
        }
        .footer {
            background-color: #333;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .footer a {
            color: white;
            text-decoration: none;
            padding: 0 10px;
            transition: color 0.3s;
        }
        .footer a:hover {
            color: #3498db;
        }
    </style>
</head>
<body>

<header>
    <img src="logo.png" alt="Online Book Store Logo">
    <div class="nav-links">
        <a href="homepage.php"><i class="fas fa-home"></i></a>
        <a href="contact.php"><i class="fas fa-envelope"></i></a>
        <a href="aboutus.php"><i class="fas fa-info-circle"></i></a>
        <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
    </div>
</header>

<!-- Hero Section -->
<div class="hero-section">
    <h1>Welcome to Our Online Book Store</h1>
    <p>Find your next great read with just a few clicks.</p>
    <a href="books.php" class="cta-button">Browse books</a>
</div>

<!-- Profile Section: Show user profile picture and name if logged in -->
<div class="user-profile">
    <?php
    if (isset($_SESSION['username'])) {
        // If user is logged in, show their profile picture and username
        $username = htmlspecialchars($_SESSION['username']);
        $profilePicture = !empty($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default-profile.jpg'; // Default profile image if none

        echo "<div class='profile-info'>";
        echo "<img src='$profilePicture' alt='Profile Picture' class='profile-pic'>";
        echo "<p>Hello, $username!</p>";
        echo "</div>";

        // Logout Button Form
        echo "<form method='POST' action=''>";
        echo "<button type='submit' name='logout' class='cta-button'>Logout</button>";
        echo "</form>";
    } else {
        // If user is not logged in, show a guest message
        echo "<p>Welcome, Guest! Please <a href='login.php'>log in</a> to view your profile.</p>";
    }
    ?>
</div>

<!-- Featured Books -->
<div class="featured-books">
    <h2>Featured Books</h2>
    <div class="books-container">
        <!-- Repeat this block for each featured book -->
        <div class="book">
            <img src="book1.jpg" alt="Book Image">
            <h3>Book Title</h3>
            <p>by Author</p>
        </div>
        <div class="book">
            <img src="book2.jpg" alt="Book Image">
            <h3>Book Title</h3>
            <p>by Author</p>
        </div>
        <div class="book">
            <img src="book3.jpg" alt="Book Image">
            <h3>Book Title</h3>
            <p>by Author</p>
        </div>
    </div>
</div>

<!-- Books Search Results -->
<?php
// Only show the book list if the search term is submitted
if (!empty($searchTerm)) {
    echo '<div class="book-list">';
    echo '<h2>Books Found</h2>';
    echo '<div class="books-container">';
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<div class='book'>";
            // Check if the image path exists and display it
            if (!empty($row['image'])) {
                echo "<img src='uploads/{$row['image']}' alt='Book Image'>";
            } else {
                echo "<img src='default-book.jpg' alt='Book Image'>";
            }
            echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
            echo "<p>by " . htmlspecialchars($row['author']) . "</p>";
            echo "<p><strong>Price:</strong> $" . htmlspecialchars($row['price']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No books found for '$searchTerm'.</p>";
    }
    echo '</div>';
    echo '</div>';
}
?>

<!-- Footer -->
<div class="footer">
    <p>&copy; 2024 Online Book Store. All rights reserved.</p>
    <p>Follow us on <a href="#">Facebook</a>, <a href="#">Instagram</a>, <a href="#">Twitter</a></p>
</div>

</body>
</html>
