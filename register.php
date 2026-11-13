<?php
require_once 'config.php';
require_once 'includes/functions.php';

$mess = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $uname = trim($_POST['uname'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($uname === '') {
        $errors[] = "Username is required.";
    } elseif (mb_strlen($uname) > 50) {
        $errors[] = "Username must not exceed 50 characters.";
    }

    if ($password === '') {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if ($password !== $password2) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {

        $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$uname]);

        if ($check->fetch()) {

            $errors[] = "Username already exists.";

        } else {

            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO users (username, password, status, role)
                     VALUES (?, ?, 'pending', 'user')"
                );
                $stmt->execute([$uname, password_hash($password, PASSWORD_DEFAULT)]);

                $mess = "Registered successfully! Please wait for admin approval of your account.";

            } catch (PDOException $ex) {
                // 23000 = duplicate (race condition sa UNIQUE username)
                $errors[] = $ex->getCode() === '23000'
                    ? "Username already exists."
                    : "Something went wrong. Please try again later.";
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
    <title>Register | Inventory System</title>
</head>
<body>

    <h1>Inventory System</h1>
    <h2>Create your Account</h2>
    <br>

    <?php foreach ($errors as $err): ?>
        <p style="color: red;"><?= e($err) ?></p>
    <?php endforeach; ?>

    <?php if ($mess !== ''): ?>
        <p style="color: blue;"><?= e($mess) ?></p>
    <?php endif; ?>

    <form action="" method="POST">

        <label for="uname">Username:</label>
        <input type="text" name="uname" id="uname" placeholder="Enter username"
               maxlength="50" value="<?= e($_POST['uname'] ?? '') ?>" required autofocus>

        <br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" placeholder="Enter password" required>

        <br><br>

        <label for="password2">Re-enter Password:</label>
        <input type="password" name="password2" id="password2" placeholder="Re-enter password" required>

        <br><br>

        <input type="submit" value="Register">
    </form>

    <br><br>
    <p>Already have an account? <a href="index.php">Login</a></p>

</body>
</html>