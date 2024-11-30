<?php
// Start the session
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'nouman');

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle "Add to Cart" functionality
if (isset($_GET['add_to_cart'])) {
    $book_id = $_GET['add_to_cart'];

    // Check if the cart session already exists, if not, initialize it
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if the book is already in the cart, and increase the quantity if it is
    if (isset($_SESSION['cart'][$book_id])) {
        $_SESSION['cart'][$book_id]++;
    } else {
        // If not, add the book with a quantity of 1
        $_SESSION['cart'][$book_id] = 1;
    }

    // Redirect to the cart page after adding the book
    header("Location: cart.php");
    exit();
}

// Get the book ID from the URL
if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];

    // Query to fetch the book details from the database
    $sql = "SELECT * FROM books WHERE id = $book_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
    } else {
        echo "Book not found.";
        exit();
    }
} else {
    echo "No book selected.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details</title>
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

        .back-btn {
            background-color: #3498db;
            color: white;
            padding: 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-btn:hover {
            background-color: #2980b9;
        }

        .book-details {
            padding: 20px;
            display: flex;
            justify-content: center;
            gap: 30px;
        }

        .book-details img {
            max-width: 300px;
            border-radius: 10px;
        }

        .book-details .info {
            max-width: 600px;
        }

        .book-details h2 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .book-details p {
            font-size: 1.2rem;
            margin: 10px 0;
        }

        .price {
            font-size: 1.5rem;
            color: #27ae60;
            font-weight: bold;
        }

        .button {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<header>
    <h1>Book Details</h1>
</header>

<!-- Book Details Section -->
<div class="book-details">
    <div>
        <!-- Display the book image -->
        <img src="<?php echo htmlspecialchars($book['image_url']); ?>" alt="Book Image">
    </div>
    <div class="info">
        <h2><?php echo htmlspecialchars($book['title']); ?></h2>
        <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
        <p class="price">$<?php echo number_format($book['price'], 2); ?></p>
        <a href="bookdetails.php?add_to_cart=<?php echo $book['id']; ?>" class="button">Add to Cart</a>
    </div>
</div>

<!-- Back Button -->
<div style="text-align: center; margin: 20px;">
    <a href="books.php" class="back-btn">Back to Books List</a>
</div>

</body>
</html>
