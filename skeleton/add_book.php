<?php
include("Storage.php");

session_start();


if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit();
}


$books_storage = new Storage(new JsonIO('books.json'));

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $description = trim($_POST['description']);
    $year = trim($_POST['year']);
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
    if (empty($_FILES['image']['name'])) {
        $errors[] = "Image is required.";
    }

    if (empty($errors)) {

        $target_dir = "uploads/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        
        if ($check === false) {
            $errors[] = "File is not an image.";
        } elseif (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $errors[] = "Sorry, there was an error uploading your file.";
        } else {
            $new_book = [
                "id" => $target_file,
                "title" => $title,
                "author" => $author,
                "description" => $description,
                "year" => (int)$year,
                "image" => $target_file,
                "planet" => $planet,
                "genre" => $genre,
                "ratings" => [],
                "evaluations" => []
            ];
            $books_storage->add($new_book);
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IK-Library | Add Book</title>
    <link rel="stylesheet" href="styles/main.css">
</head>

<body>
    <header>
        <h1><a href="index.php">IK-Library</a> > Add Book</h1>
    </header>
    <div id="content">
        <?php if ($success): ?>
            <p class="success">Book added successfully!</p>
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
        <form action="add_book.php" method="POST" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($title ?? '') ?>">
            
            <label for="author">Author:</label>
            <input type="text" id="author" name="author" value="<?= htmlspecialchars($author ?? '') ?>">
            
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="5"><?= htmlspecialchars($description ?? '') ?></textarea>
            
            <label for="year">Year:</label>
            <input type="text" id="year" name="year" value="<?= htmlspecialchars($year ?? '') ?>">
            
            <label for="image">Image:</label>
            <input type="file" id="image" name="image">
            
            <label for="planet">Planet:</label>
            <input type="text" id="planet" name="planet" value="<?= htmlspecialchars($planet ?? '') ?>">
            
            <label for="genre">Genre:</label>
            <input type="text" id="genre" name="genre" value="<?= htmlspecialchars($genre ?? '') ?>">
            
            <button type="submit">Add Book</button>
        </form>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>

</html>
