<?php 
include("Storage.php");

session_start();


$books_storage = new Storage(new JsonIO('books.json'));
$books = $books_storage->findAll();


$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];


function calculate_average_rating($ratings) {
    if (empty($ratings)) {
        return 0;
    }
    return array_sum($ratings) / count($ratings);
}


foreach ($books as $id => $book) {
    $books[$id]['average_rating'] = calculate_average_rating($book['ratings']);
}


usort($books, function($a, $b) {
    return $b['average_rating'] <=> $a['average_rating'];
});
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IK-Library | Home</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/cards.css">
</head>

<body>
    <header>
        <h1><a href="index.php">IK-Library</a> > Home</h1>
        <nav>
            <ul>
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="user_details.php"><?= htmlspecialchars($_SESSION['username']) ?></a></li>
                    <li><a href="logout.php" class="nav-link">Logout</a></li>
                <?php else: ?>
                    <li><a href="register.php" class="nav-link">Register</a></li>
                    <li><a href="login.php" class="nav-link">Login</a></li>
                <?php endif; ?>
                <?php if ($is_admin): ?>
                    <li><a href="add_book.php" class="admin-link">Add Book</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <div id="content">
        <div id="card-list">
            <?php foreach ($books as $book): ?>
                <div class="book-card">
                    <div class="image">
                        <img src="<?= htmlspecialchars($book["image"]) ?>" alt="">
                    </div>
                    <div class="details">
                        <h2><a href="details.php?id=<?= htmlspecialchars($book["id"]) ?>"><?= htmlspecialchars($book["author"]) ?> - <?= htmlspecialchars($book["title"]) ?></a></h2>
                        <p>Average Rating: <?= number_format($book['average_rating'], 2) ?></p>
                    </div>
                    <?php if ($is_admin): ?>
                        <div class="edit" onclick="window.location.href='edit_book.php?id=<?= htmlspecialchars($book["id"]) ?>'">
                            <h2><a href="">Edit</a></h2>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>

</html>
