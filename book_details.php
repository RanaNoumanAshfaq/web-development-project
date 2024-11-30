<?php
// Start the session
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'nouman');

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the book_id from the URL
$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;

if ($book_id > 0) {
    // Fetch the details of the specific book
    $sql = "SELECT * FROM books WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
    } else {
        die("Book not found.");
    }
} else {
    die("Invalid book ID.");
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .book-details {
            max-width: 600px;
            margin: auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .book-details img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .book-details h1 {
            font-size: 2rem;
            margin: 20px 0;
        }

        .book-details p {
            font-size: 1.2rem;
            color: #555;
        }

        .price {
            font-size: 1.5rem;
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
            display: inline-block;
            margin-top: 20px;
        }

        .button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<div class="book-details">
    <img src="<?php echo htmlspecialchars(!empty($book['image_url']) ? $book['image_url'] : 'default_image.jpg'); ?>" 
         alt="<?php echo htmlspecialchars($book['title']); ?>">
    <h1><?php echo htmlspecialchars($book['title']); ?></h1>
    <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($book['description']); ?></p>
    <p class="price">$<?php echo number_format($book['price'], 2); ?></p>
    <a href="cart.php?book_id=<?php echo $book['id']; ?>" class="button">Add to Cart</a>
</div>

</body>
</html>
