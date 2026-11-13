<?php
require_once 'config.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $uname = trim($_POST['uname'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($uname === '' || $password === '') {

        $errors[] = "Please enter your username and password.";

    } else {

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$uname]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {

            if ($user['status'] !== "approved") {

                $errors[] = "Your account has not been approved by the admin.";

            } else {

                session_regenerate_id(true);

                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: dashboard.php");
                exit;
            }

        } else {

            $errors[] = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Inventory System</title>
</head>
<body>
    <h1>Inventory System</h1>
    <h2>Sign In To Your Account</h2>
    <br>

    <?php foreach ($errors as $err): ?>
        <p style="color: red;"><?= e($err) ?></p>
    <?php endforeach; ?>

    <form action="" method="POST">

        <label for="uname">Username:</label>
        <input type="text" name="uname" id="uname" placeholder="Enter username"
               value="<?= e($_POST['uname'] ?? '') ?>" required autofocus>

        <br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" placeholder="Enter password" required>

        <br><br>

        <input type="submit" value="Sign In">
    </form>

    <br><br>
    <p>No Account? <a href="register.php">Register</a></p>
</body>
</html>