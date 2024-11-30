<?php
// Start the session
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'nouman');

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle the search functionality
$searchTerm = '';
if (isset($_POST['search'])) {
    $searchTerm = $_POST['search_term'];
    // Query to fetch books based on the search term (searching title or author)
    $sql = "SELECT * FROM books WHERE title LIKE '%$searchTerm%' OR author LIKE '%$searchTerm%'";
} else {
    // Fetch all books if no search term is provided
    $sql = "SELECT * FROM books";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books List</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            
        }

        header {
            background-color: #2c3e50;
            padding: 20px;
            text-align: center;
            color: white;
        }

        header .nav {
            text-align: center;
            margin-top: 10px;
        }

        header .nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-size: 1.5rem;
        }

        header .nav a:hover {
            color: #3498db;
        }

        .search-container {
            text-align: center;
            margin: 20px;
        }

        .search-bar {
            padding: 10px;
            font-size: 1rem;
            width: 70%;
            max-width: 400px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .search-btn {
            padding: 10px 20px;
            font-size: 1rem;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-btn:hover {
            background-color: #2980b9;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .book {
            background-color: white;
            width: 250px;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .book:hover {
            transform: translateY(-5px);
        }

        .book img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }

        .book h3 {
            font-size: 1.2rem;
            margin: 10px 0;
        }

        .book p {
            font-size: 1rem;
            color: #555;
        }

        .price {
            font-size: 1.1rem;
            color: #27ae60;
            font-weight: bold;
        }

        .button {
            background-color: #3498db;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .button:hover {
            background-color: #2980b9;
        }

        .icons {
            margin: 20px;
        }

        .icons a {
            font-size: 2rem;
            margin: 0 15px;
            text-decoration: none;
            color: #2c3e50;
        }

        .icons a:hover {
            color: #3498db;
        }
    </style>
</head>
<body>

<header>
    
    <!-- Navigation Icons -->
    <div class="nav">
        <a href="homepage.php"><i class="fas fa-home"></i> Home</a>
        <a href="contact.php"><i class="fas fa-phone-alt"></i> Contact Us</a>
        <a href="about.php"><i class="fas fa-info-circle"></i> About Us</a>
        <a href="cart.php"><i class="fas fa-shopping-cart"></i>Cart</a>
    </div>
</header>

<!-- Search Bar -->
<div class="search-container">
    <form method="POST" action="">
        <input type="text" name="search_term" class="search-bar" placeholder="Search for books..." value="<?php echo htmlspecialchars($searchTerm); ?>">
        <button type="submit" name="search" class="search-btn">Search</button>
    </form>
</div>

<!-- Book Collection -->
<div class="container">
    <?php
    if ($result->num_rows > 0) {
        // Display all books from the database or search results
        while ($row = $result->fetch_assoc()) {
            // Check if image_url exists, otherwise use a default image
            $image = !empty($row['image_url']) ? $row['image_url'] : 'default_image.jpg';
            
            echo "<div class='book'>
                    <img src='" . htmlspecialchars($image) . "' alt='" . htmlspecialchars($row['title']) . "'>
                    <h3>" . htmlspecialchars($row['title']) . "</h3>
                    <p>Author: " . htmlspecialchars($row['author']) . "</p>
                    <p class='price'>$" . number_format($row['price'], 2) . "</p>
                    <a href='bookdetails.php?book_id=" . $row['id'] . "' class='button'>View Details</a>
                  </div>";
        }
    } else {
        echo "<p style='text-align:center;'>No books found for your search.</p>";
    }

    // Close the database connection
    $conn->close();
    ?>
</div>

</body>
</html>
