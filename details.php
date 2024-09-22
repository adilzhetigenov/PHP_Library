<?php
include("Storage.php");

session_start();


$books_storage = new Storage(new JsonIO('books.json'));
$users_storage = new Storage(new JsonIO('users.json'));

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$book_id = $_GET['id'];
$book = $books_storage->findById($book_id);

if (!$book) {
    header('Location: index.php');
    exit();
}


$user = null;
$is_read = false;
if (isset($_SESSION['username'])) {
    $user = $users_storage->findOne(['username' => $_SESSION['username']]);

    if ($user) {
        foreach ($user['read_books'] as $read_book) {
            if ($read_book['book_id'] == $book_id) {
                $is_read = true;
                break;
            }
        }
    }
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_evaluation'])) {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    if ($rating < 1 || $rating > 5) {
        $errors[] = "Rating must be between 1 and 5.";
    }
    if (empty($comment)) {
        $errors[] = "Comment cannot be empty.";
    }

    if (empty($errors)) {
        $new_evaluation = [
            'username' => $user['username'],
            'rating' => $rating,
            'comment' => $comment
        ];
        $book['evaluations'][] = $new_evaluation;
        $book['ratings'][] = $rating;
        $books_storage->update($book_id, $book);
        $success = true;
    }
}

// Handle mark as read form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_as_read'])) {
    if ($user && !$is_read) {
        $user['read_books'][] = ["book_id" => $book_id, "read_at" => date("Y-m-d H:i:s")];
        $users_storage->update($user['id'], $user);
        $is_read = true;
        $success = true;
    }
}

// Function to calculate the average rating of a book
function calculate_average_rating($ratings) {
    if (empty($ratings)) {
        return 0;
    }
    return array_sum($ratings) / count($ratings);
}

$average_rating = calculate_average_rating($book['ratings']);


$readers = [];
foreach ($users_storage->findAll() as $u) {
    if (isset($u['read_books']) && is_array($u['read_books'])) {
        foreach ($u['read_books'] as $read_book) {
            if ($read_book['book_id'] == $book_id) {
                $readers[] = ['username' => $u['username'], 'read_at' => $read_book['read_at']];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IK-Library | Book Details</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/details.css">
</head>

<body>
    <header>
        <h1><a href="index.php">IK-Library</a> > Book Details</h1>
    </header>
    <div id="content">
        <div class="book-details">
            <img src="<?= htmlspecialchars($book['image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
            <h2><?= htmlspecialchars($book['title']) ?></h2>
            <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
            <p><strong>Year:</strong> <?= htmlspecialchars($book['year']) ?></p>
            <p><strong>Planet:</strong> <?= htmlspecialchars($book['planet']) ?></p>
            <p><strong>Genre:</strong> <?= htmlspecialchars($book['genre']) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($book['description']) ?></p>
            <p><strong>Average Rating:</strong> <?= number_format($average_rating, 2) ?></p>

            <?php if ($user): ?>
                <form action="details.php?id=<?= htmlspecialchars($book_id) ?>" method="POST">
                    <?php if ($success): ?>
                        <p class="success">Book marked as read!</p>
                    <?php endif; ?>
                    <?php if (!$is_read): ?>
                        <button type="submit" name="mark_as_read">Mark as Read</button>
                    <?php else: ?>
                        <p>You have read this book.</p>
                    <?php endif; ?>
                </form>
            <?php endif; ?>

            <h3>Evaluations</h3>
            <?php if (!empty($book['evaluations'])): ?>
                <ul class="evaluations">
                    <?php foreach ($book['evaluations'] as $evaluation): ?>
                        <li class="evaluation">
                            <p><strong><?= htmlspecialchars($evaluation['username']) ?>:</strong> <?= htmlspecialchars($evaluation['comment']) ?> (Rating: <?= htmlspecialchars($evaluation['rating']) ?>)</p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No evaluations yet.</p>
            <?php endif; ?>

            <?php if ($user): ?>
                <h3>Write an Evaluation</h3>
                <?php if (!empty($errors)): ?>
                    <div class="errors">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form action="details.php?id=<?= htmlspecialchars($book_id) ?>" method="POST">
                    <label for="rating">Rating (1-5):</label>
                    <input type="number" id="rating" name="rating" min="1" max="5" value="<?= htmlspecialchars($_POST['rating'] ?? '') ?>">
                    
                    <label for="comment">Comment:</label>
                    <textarea id="comment" name="comment"><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea>
                    
                    <button type="submit" name="submit_evaluation">Submit Evaluation</button>
                </form>
            <?php endif; ?>

            <h3>Users who have read this book</h3>
            <?php if (!empty($readers)): ?>
                <ul>
                    <?php foreach ($readers as $reader): ?>
                        <li>
                            <p><strong><?= htmlspecialchars($reader['username']) ?>:</strong> read at <?= htmlspecialchars($reader['read_at']) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No users have read this book yet.</p>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>

</html>
