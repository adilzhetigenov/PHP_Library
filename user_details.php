<?php

include("Storage.php");

session_start();


$books_storage = new Storage(new JsonIO('books.json'));
$users_storage = new Storage(new JsonIO('users.json'));


if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$user = $users_storage->findOne(['username' => $_SESSION['username']]);

if (!$user) {
    header('Location: login.php');
    exit();
}

$read_books = $user['read_books'];
usort($read_books, function($a, $b) {
    return strtotime($b['read_at']) - strtotime($a['read_at']);
});

$books_read = array_map(function($read_book) use ($books_storage) {
    return $books_storage->findById($read_book['book_id']);
}, $read_books);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/details.css">
</head>

<body>
    <header>
        <h1><a href="index.php">IK-Library</a> > User Details</h1>
    </header>
    <div id="content">
        <h2><?= htmlspecialchars($user['username']) ?>'s Profile</h2>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Last Login:</strong> <?= htmlspecialchars($user['last_login']) ?></p>

        <h3>Books Read</h3>
        <?php if (!empty($books_read)): ?>
            <ul class="books-read">
                <?php foreach ($books_read as $book): ?>
                    <li>
                        <p><strong>Title:</strong> <?= htmlspecialchars($book['title']) ?></p>
                        <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
                        <p><strong>Read At:</strong> <?= htmlspecialchars($read_books[array_search($book['id'], array_column($read_books, 'book_id'))]['read_at']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No books read yet.</p>
        <?php endif; ?>

        <h3>Evaluations</h3>
        <?php if (!empty($user['evaluations'])): ?>
            <ul class="evaluations">
                <?php foreach ($user['evaluations'] as $evaluation): ?>
                    <li class="evaluation">
                        <p><strong>Book:</strong> <?= htmlspecialchars($evaluation['book_title']) ?></p>
                        <p><strong>Rating:</strong> <?= htmlspecialchars($evaluation['rating']) ?></p>
                        <p><strong>Comment:</strong> <?= htmlspecialchars($evaluation['comment']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No evaluations written yet.</p>
        <?php endif; ?>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>

</html>
