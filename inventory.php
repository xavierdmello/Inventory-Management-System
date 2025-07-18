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
// Update Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_row'])) {
    $product_id = $_POST['product_id'];
    $supplier_name = $_POST['supplier_name'];
    $price = $_POST['price'];
    $newQuantity = $_POST['new_quantity'];
    $newStatus = $_POST['new_status'];

    $stmt = $pdo->prepare('UPDATE InventoryTable SET Quantity = ?, Status = ? WHERE ProductID = ? AND SupplierName = ? AND Price = ?');
    $stmt->execute([$newQuantity, $newStatus, $product_id, $supplier_name, $price]);
}

// Delete Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_row'])) {
    $product_id = $_POST['product_id'];
    $supplier_name = $_POST['supplier_name'];
    $price = $_POST['price'];

    $stmt = $pdo->prepare('DELETE FROM InventoryTable WHERE ProductID = ? AND SupplierName = ? AND Price = ?');
    $stmt->execute([$product_id, $supplier_name, $price]);
}

$search = $_GET['search'] ?? '';
if ($search !== '') {
    $stmt = $pdo->prepare('SELECT ProductID, ProductName, Quantity, Price, Status, SupplierName FROM InventoryTable WHERE ProductName LIKE ? ORDER BY ProductID ASC');
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $pdo->prepare('SELECT ProductID, ProductName, Quantity, Price, Status, SupplierName FROM InventoryTable ORDER BY ProductID ASC');
    $stmt->execute();
}
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
    <form method="get" action="inventory.php" style="margin-bottom: 10px;">
        <input type="text" name="search" placeholder="Search by Product Name" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        <button type="submit">Search</button>
    </form>
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
        <form method="post" action="inventory.php">
            <td><?php echo htmlspecialchars($row['ProductID']); ?></td>
            <td><?php echo htmlspecialchars($row['ProductName']); ?></td>
            <td>
                <input type="number" name="new_quantity" value="<?php echo htmlspecialchars($row['Quantity']); ?>" required>
            </td>
            <td><?php echo htmlspecialchars($row['Price']); ?></td>
            <td>
                <input type="text" name="new_status" value="<?php echo htmlspecialchars($row['Status']); ?>" required>
            </td>
            <td><?php echo htmlspecialchars($row['SupplierName']); ?></td>
            <td>
                <!-- Hidden fields for unique identification -->
                <input type="hidden" name="product_id" value="<?php echo $row['ProductID']; ?>">
                <input type="hidden" name="supplier_name" value="<?php echo htmlspecialchars($row['SupplierName']); ?>">
                <input type="hidden" name="price" value="<?php echo $row['Price']; ?>">
                <button type="submit" name="update_row" value="1">Update</button>
            </td>
        </form>
        <form method="post" action="inventory.php" onsubmit="return confirm('Are you sure you want to delete this item?');">
            <td>
                <input type="hidden" name="product_id" value="<?php echo $row['ProductID']; ?>">
                <input type="hidden" name="supplier_name" value="<?php echo htmlspecialchars($row['SupplierName']); ?>">
                <input type="hidden" name="price" value="<?php echo $row['Price']; ?>">
                <button type="submit" name="delete_row" value="1">Delete</button>
            </td>
        </form>

        </tr>
        <?php endforeach; ?>

    </table>
</body>
</html>
