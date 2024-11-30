<?php
session_start();

// Restrict access to logged-in owners
if (!isset($_SESSION['owner_logged_in'])) {
    header("Location: owner_login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'nouman');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create uploads directory if it doesn't exist
if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}

// Handle book addition
if (isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Handle image upload
    $image = $_FILES['image'];
    $image_path = 'uploads/' . basename($image['name']);
    move_uploaded_file($image['tmp_name'], $image_path);

    // Use prepared statement to avoid SQL injection
   // Use prepared statement to avoid SQL injection
$stmt = $conn->prepare("INSERT INTO books (title, author, price, description, image_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $title, $author, $price, $description, $image_path);

if ($stmt->execute()) {
    $success = "Book added successfully!";
} else {
    $error = "Error: " . $stmt->error;
}


   
}

// Handle book deletion
if (isset($_POST['delete_book'])) {
    $book_id = $_POST['book_id'];
    $sql = "DELETE FROM books WHERE id = $book_id";
    if ($conn->query($sql) === TRUE) {
        $success = "Book deleted successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Handle book editing
if (isset($_POST['edit_book'])) {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Handle optional image upload
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image'];
        $image_path = 'uploads/' . basename($image['name']);
        move_uploaded_file($image['tmp_name'], $image_path);
        $sql = "UPDATE books SET title='$title', author='$author', price='$price', description='$description', image_url='$image_path' WHERE id=$book_id";
    } else {
        $sql = "UPDATE books SET title='$title', author='$author', price='$price', description='$description' WHERE id=$book_id";
    }

    if ($conn->query($sql) === TRUE) {
        $success = "Book updated successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Search for book by ID or Name
$book = null; // Default value to null
if (isset($_POST['search_book'])) {
    $search_term = $_POST['search_term'];
    $sql = "SELECT * FROM books WHERE id = '$search_term' OR title LIKE '%$search_term%'";
    $search_result = $conn->query($sql);
    if ($search_result->num_rows > 0) {
        $book = $search_result->fetch_assoc(); // If book is found, assign it to $book
    } else {
        $error = "No book found with that ID or name.";
    }
}
?>
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-bottom: 20px;
        }

        form input,
        form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        form button {
            background: #8b0000;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Owner Dashboard</h1>

    <?php if (isset($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- Add Book Form -->
    <h2>Add a Book</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Book Title" required>
        <input type="text" name="author" placeholder="Author Name" required>
        <input type="number" step="0.01" name="price" placeholder="Price" required>
        <textarea name="description" placeholder="Book Description" rows="4" required></textarea>
        <input type="file" name="image" required>
        <button type="submit" name="add_book">Add Book</button>
    </form>

    <!-- Update Book Form -->
    <h2>Search for a Book</h2>
    <form method="POST">
        <input type="text" name="search_term" placeholder="Search by ID or Name" required>
        <button type="submit" name="search_book">Search</button>
    </form>

    <?php if (isset($book)): ?>
        <h2>Update Book Details</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
            <input type="text" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
            <input type="text" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>
            <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($book['price']); ?>" required>
            <textarea name="description" rows="2"><?php echo htmlspecialchars($book['description']); ?></textarea>
            <input type="file" name="image">
            <button type="submit" name="edit_book">Update Book</button>
        </form>

        <h2>Delete Book</h2>
        <form method="POST">
            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
            <button type="submit" name="delete_book">Delete Book</button>
        </form>
    <?php endif; ?>

    <!-- Manage Books -->
    <h2>Manage Books</h2>
    <table>
        <tr>
            <th>Image</th>
            <th>Title</th>
            <th>Author</th>
            <th>Price</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM books");
        while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><img src="<?php echo $row['image_url']; ?>" alt="Book Image" style="width: 50px;"></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['author']); ?></td>
                <td><?php echo htmlspecialchars($row['price']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="book_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete_book">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</
