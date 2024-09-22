<?php
include("Storage.php");

session_start();

$users_storage = new Storage(new JsonIO('users.json'));

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $existing_user = $users_storage->findOne(['username' => $username]);

        if ($existing_user) {
            $errors[] = "Username already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $new_user_id = uniqid();
            $new_user = [
                "id" => $new_user_id,
                "username" => $username,
                "email" => $email,
                "password" => $hashed_password,
                "is_admin" => false,
                "last_login" => null,
                "read_books" => []
            ];
            $users_storage->add($new_user);
            $_SESSION['username'] = $username;
            $_SESSION['is_admin'] = false;
            $success = true;
            header('Location: index.php');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <header>
        <h1><a href="index.php">IK-Library</a> > Register</h1>
    </header>
    <div id="content">
        <?php if ($success): ?>
            <p class="success">Registration successful! Redirecting to home page...</p>
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
        <form action="register.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($username ?? '') ?>">
            
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password">
            
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password">
            
            <button type="submit">Register</button>
        </form>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>
</html>
