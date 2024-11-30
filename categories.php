<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }
        header {
            background-color: #8b0000;
            color: white;
            padding: 20px;
            text-align: center;
        }
        main {
            padding: 20px;
            max-width: 800px;
            margin: auto;
        }
        main h1 {
            font-size: 2.5rem;
            color: #333;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        ul li {
            background: white;
            margin: 10px 0;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            font-size: 1.2rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        ul li:hover {
            background-color: #f1f1f1;
        }
        .search-container {
            margin-bottom: 20px;
            text-align: center;
        }
        .search-bar {
            padding: 10px;
            font-size: 1rem;
            width: 80%;
            max-width: 400px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .search-btn {
            padding: 10px 20px;
            font-size: 1rem;
            background-color: #8b0000;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .search-btn:hover {
            background-color: #a52a2a;
        }
    </style>
</head>
<body>
    <header>
        <h1>Categories</h1>
    </header>
    <main>
        <h1>Explore Our Categories</h1>

        <!-- Search Form -->
        <div class="search-container">
            <form method="POST" action="categories.php">
                <input type="text" name="search_term" class="search-bar" placeholder="Search for books..." required>
                <button type="submit" name="search" class="search-btn">Search</button>
            </form>
        </div>

        <!-- Categories List with Links -->
        <ul>
            <li><a href="categories.php?category=fiction">📖 Fiction</a></li>
            <li><a href="categories.php?category=non-fiction">📚 Non-Fiction</a></li>
            <li><a href="categories.php?category=fantasy">🧙‍♂️ Fantasy</a></li>
            <li><a href="categories.php?category=science">🔬 Science</a></li>
            <li><a href="categories.php?category=children">👶 Children's Books</a></li>
            <li><a href="categories.php?category=biographies">📘 Biographies</a></li>
        </ul>

        <!-- Search Results or Category Books -->
        <?php
        // Database connection
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "nouman"; // Replace with your actual database name

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if a category is selected
        if (isset($_GET['category'])) {
            $category = $_GET['category'];

            // Sanitize category input to prevent SQL injection
            $category = $conn->real_escape_string($category);

            // Query to get books for the selected category
            $sql = "SELECT * FROM books WHERE category = '$category'";
            $result = $conn->query($sql);

            // Display books in the selected category
            if ($result && $result->num_rows > 0) {
                echo "<h2>Books in " . ucfirst($category) . " Category</h2>";
                echo "<ul>";
                while ($row = $result->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($row['title']) . " by " . htmlspecialchars($row['author']) . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No books found in this category.</p>";
            }
        }

        // Handle search results
        if (isset($_POST['search'])) {
            // Get search term from form
            $searchTerm = $_POST['search_term'];

            // Sanitize input to prevent SQL injection
            $searchTerm = $conn->real_escape_string($searchTerm);

            // Query to search for books based on title or author
            $sql = "SELECT * FROM books WHERE title LIKE '%$searchTerm%' OR author LIKE '%$searchTerm%'";
            $result = $conn->query($sql);

            // Display search results
            if ($result && $result->num_rows > 0) {
                echo '<h2>Search Results</h2>';
                echo '<ul>';
                while ($row = $result->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($row['title']) . " by " . htmlspecialchars($row['author']) . "</li>";
                }
                echo '</ul>';
            } else {
                echo "<p>No books found for your search.</p>";
            }
        }

        // Close the connection
        $conn->close();
        ?>
    </main>
</body>
</html>
