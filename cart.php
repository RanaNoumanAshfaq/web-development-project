<?php
// Start the session
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'nouman');

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch cart items from session
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_price = 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }

        header {
            background: #2c3e50;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .cart-item p {
            margin: 0;
        }

        .total {
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }

        .payment-button {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            display: block;
            margin: 20px 0;
            text-align: center;
            text-decoration: none;
        }

        .payment-button:hover {
            background-color: #2980b9;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 5px;
            min-width: 200px;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
</head>
<body>
<header>
    <h1>Your Cart</h1>
</header>
<div class="container">
    <?php
    if (!empty($cart_items)) {
        foreach ($cart_items as $book_id => $quantity) {
            // Query to fetch book details
            $sql = "SELECT * FROM books WHERE id = $book_id";
            $result = $conn->query($sql);

            if ($result && $row = $result->fetch_assoc()) {
                $subtotal = $row['price'] * $quantity;
                $total_price += $subtotal;

                echo "<div class='cart-item'>
                        <p>" . htmlspecialchars($row['title']) . " (x" . $quantity . ")</p>
                        <p>PKR " . number_format($subtotal, 2) . "</p>
                      </div>";
            }
        }

        echo "<p class='total'>Total: PKR " . number_format($total_price, 2) . "</p>";
    } else {
        echo "<p>Your cart is empty. <a href='books.php'>Browse Books</a></p>";
    }
    ?>
    <?php if (!empty($cart_items)): ?>
        <div class="dropdown">
            <a class="payment-button" href="#">Proceed to Checkout</a>
            <div class="dropdown-content">
                <a href="checkout.php">Credit/Debit Card</a>
                <a href="#">Cash on Delivery</a>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>

<?php
$conn->close();
?>
