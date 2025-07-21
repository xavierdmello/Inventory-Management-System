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
            die('<div class="container"><div class="alert alert-error">Invalid Username or Password. <a href="login.php">Try again</a></div></div>');
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
    // Fully-qualified SELECT for filtered search
    $stmt = $pdo->prepare(
        'SELECT ProductID,
                ProductName,
                Quantity,
                Price,
                Status,
                SupplierName
         FROM InventoryTable
         WHERE ProductName LIKE ?
         ORDER BY ProductID ASC'
    );
    // bind the wildcarded search term
    $stmt->execute([ "%{$search}%" ]);
} else {
    // Unfiltered listing
    $stmt = $pdo->prepare(
        'SELECT ProductID,
                ProductName,
                Quantity,
                Price,
                Status,
                SupplierName
         FROM InventoryTable
         ORDER BY ProductID ASC'
    );
    $stmt->execute();
}

$inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - Inventory Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container header-content">
            <h1>Inventory Management System</h1>
            <div>
                <span style="margin-right: 15px;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <form action="logout.php" method="post" style="display:inline;">
                    <button type="submit" class="btn">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="container">
        <h2>Inventory Management</h2>
        
        <form method="get" action="inventory.php" class="search-form">
            <input type="text" name="search" placeholder="Search by Product Name" 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button type="submit">Search</button>
            <?php if (!empty($_GET['search'])): ?>
                <a href="inventory.php" class="btn" style="margin-left: 10px;">Clear Search</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Supplier Name</th>
                        <th colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inventory as $row): ?>
                    <tr>
                        <form method="post" action="inventory.php">
                            <td><?php echo htmlspecialchars($row['ProductID']); ?></td>
                            <td><?php echo htmlspecialchars($row['ProductName']); ?></td>
                            <td>
                                <input type="number" name="new_quantity" 
                                       value="<?php echo htmlspecialchars($row['Quantity']); ?>" 
                                       min="0" required>
                            </td>
                            <td>$<?php echo number_format($row['Price'], 2); ?></td>
                            <td>
                                <select name="new_status" required>
                                    <option value="A" <?php echo $row['Status'] === 'A' ? 'selected' : ''; ?>>Active</option>
                                    <option value="B" <?php echo $row['Status'] === 'B' ? 'selected' : ''; ?>>Backordered</option>
                                    <option value="C" <?php echo $row['Status'] === 'C' ? 'selected' : ''; ?>>Discontinued</option>
                                </select>
                            </td>
                            <td><?php echo htmlspecialchars($row['SupplierName']); ?></td>
                            <!-- Hidden fields for unique identification -->
                            <input type="hidden" name="product_id" value="<?php echo $row['ProductID']; ?>">
                            <input type="hidden" name="supplier_name" value="<?php echo htmlspecialchars($row['SupplierName']); ?>">
                            <input type="hidden" name="price" value="<?php echo $row['Price']; ?>">
                            <td>
                                <button type="submit" name="update_row" value="1" class="btn">Update</button>
                            </td>
                        </form>
                        <td>
                            <form method="post" action="inventory.php" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                <input type="hidden" name="product_id" value="<?php echo $row['ProductID']; ?>">
                                <input type="hidden" name="supplier_name" value="<?php echo htmlspecialchars($row['SupplierName']); ?>">
                                <input type="hidden" name="price" value="<?php echo $row['Price']; ?>">
                                <button type="submit" name="delete_row" value="1" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>