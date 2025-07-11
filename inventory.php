<?php
require 'config.php';  // your PDO connection setup

session_start();  // start session to manage logged-in state

// Check if user is already logged in (optional)
if (!isset($_SESSION['username'])) {
    // User not logged in, so process login form if submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $inputUsername = $_POST['username'] ?? '';
        $inputPassword = $_POST['password'] ?? '';

        // Prepare statement to check user credentials
        $stmt = $pdo->prepare('SELECT * FROM Users WHERE Username = ? AND Password = ?');
        $stmt->execute([$inputUsername, $inputPassword]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Login success, store username in session
            $_SESSION['username'] = $inputUsername;
        } else {
            // Invalid credentials - show error and exit
            echo "Invalid Username or Password.";
            exit;
        }
    } else {
        // No login data submitted, redirect to login page
        header('Location: login.php');
        exit;
    }
}

// If we're here, user is logged in, so show inventory

$stmt = $pdo->prepare('SELECT ProductID, ProductName, Quantity, Price, Status, SupplierName FROM InventoryTable ORDER BY ProductID ASC');
$stmt->execute();
$inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory - Inventory Management System</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

    <form action="logout.php" method="post" style="display:inline;">
        <button type="submit">Logout</button>
    </form>

    <h2>Inventory Table</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Status</th>
            <th>Supplier Name</th>
        </tr>
        <?php foreach ($inventory as $row): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['ProductID']); ?></td>
            <td><?php echo htmlspecialchars($row['ProductName']); ?></td>
            <td><?php echo htmlspecialchars($row['Quantity']); ?></td>
            <td><?php echo htmlspecialchars($row['Price']); ?></td>
            <td><?php echo htmlspecialchars($row['Status']); ?></td>
            <td><?php echo htmlspecialchars($row['SupplierName']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
