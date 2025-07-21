<?php
require 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = '<div class="alert alert-error">Passwords do not match.</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="alert alert-error">Invalid email format.</div>';
    } else {
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE Username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $message = '<div class="alert alert-error">Username already taken.</div>';
        } else {
            $stmt = $pdo->prepare("SELECT * FROM Users WHERE Email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $message = '<div class="alert alert-error">Email already in use.</div>';
            } else {
                $stmt = $pdo->prepare("INSERT INTO Users (Username, Password, Email) VALUES (?, ?, ?)");
                if ($stmt->execute([$username, $password, $email])) {
                    $message = '<div class="alert alert-success">Account created successfully! You can now <a href="login.php">login</a>.</div>';
                } else {
                    $message = '<div class="alert alert-error">Error creating account.</div>';
                }
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
    <title>Create Account - Inventory Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Create Account</h2>
        <?php echo $message; ?>
        <form action="register.php" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Register</button>
        </form>
        <p style="text-align: center; margin-top: 20px;"><a href="login.php">Back to Login</a></p>
    </div>
</body>
</html>