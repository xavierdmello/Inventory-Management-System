# Inventory Management System (CP476B Group Project)

## Getting Started

the proper way:

1. **Install Apache, PHP, and MySQL** (no XAMPP, phpMyAdmin, or similar tools allowed).
2. Place the project files in your Apache web root (google/ask gpt how to do this). U can clone this repo to that dir and just keep working on it from there.
3. Import the database (only do this once or if you wanna reset the db):
   - Create a new MySQL database (assuming root is username and pw is blank fr mysql, this is the default)
     ```sh
     mysql -u root -e "CREATE DATABASE inventory_db;"
     ```
   - Import the schema and sample data (run from the project root):
     ```sh
     mysql -u root inventory_db < database/schema.sql
     ```
4. Go to http://localhost:8080/Inventory-Management-System/login.php
