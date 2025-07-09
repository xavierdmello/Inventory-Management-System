<?php
require 'config.php';

// Fetch inventory data sorted by ProductID
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