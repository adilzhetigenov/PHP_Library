<?php

include("Storage.php");

session_start();


if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit();
}


$books_storage = new Storage(new JsonIO('books.json'));

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];
    $book = $books_storage->findById($book_id);
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $description = trim($_POST['description']);
    $year = trim($_POST['year']);
    $image = trim($_POST['image']);
    $planet = trim($_POST['planet']);
    $genre = trim($_POST['genre']);

    if (empty($title)) {
        $errors[] = "Title is required.";
    }
    if (empty($author)) {
        $errors[] = "Author is required.";
    }
    if (empty($year) || !is_numeric($year)) {
        $errors[] = "Valid year is required.";
    }

    if (empty($errors)) {
        $updated_book = [
            "id" => $book_id,
            "title" => $title,
            "author" => $author,
            "description" => $description,
            "year" => (int) $year,
            "image" => $image,
            "planet" => $planet,
            "genre" => $genre,
            "ratings" => $book['ratings'], 
            "evaluations" => $book['evaluations'] 
        ];
        $books_storage->update($book_id, $updated_book);
        $success = true;

        $book = $books_storage->findById($book_id);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IK-Library | Edit Book</title>
    <link rel="stylesheet" href="styles/main.css">
</head>

<body>
    <header>
        <h1><a href="index.php">IK-Library</a> > Edit Book</h1>
    </header>
    <div id="content">
        <?php if ($success): ?>
            <p class="success">Book updated successfully!</p>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if (isset($book)): ?>
            <form action="edit_book.php?id=<?= htmlspecialchars($book_id) ?>" method="POST">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($book['title']) ?>">
                
                <label for="author">Author:</label>
                <input type="text" id="author" name="author" value="<?= htmlspecialchars($book['author']) ?>">
                
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="5"><?= htmlspecialchars($book['description']) ?></textarea>
                
                <label for="year">Year:</label>
                <input type="text" id="year" name="year" value="<?= htmlspecialchars($book['year']) ?>">
                
                <label for="image">Image URL:</label>
                <input type="text" id="image" name="image" value="<?= htmlspecialchars($book['image']) ?>">
                
                <label for="planet">Planet:</label>
                <input type="text" id="planet" name="planet" value="<?= htmlspecialchars($book['planet']) ?>">
                
                <label for="genre">Genre:</label>
                <input type="text" id="genre" name="genre" value="<?= htmlspecialchars($book['genre']) ?>">
                
                <button type="submit">Update Book</button>
            </form>
        <?php else: ?>
            <p>Book not found.</p>
        <?php endif; ?>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>

</html>
