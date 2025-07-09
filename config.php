<?php
// Database configuration
$host = 'localhost';
$db   = 'inventory_db';
$user = 'root';
$pass = '';     
// change user and pass if needed 

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
} 