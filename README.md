# Inventory Management System (CP476B Group Project)

## Getting Started

the proper way:

1. **Install Apache, PHP, and MySQL** (no XAMPP, phpMyAdmin, or similar tools allowed).
2. Place the project files in your Apache web root (google/ask gpt how to do this). U can clone this repo to that dir and just keep working on it from there.
3. Import the database (only do this once or if you wanna reset the db):
   - Create a new MySQL database (replace `your_mysql_username` as needed):
     ```sh
     mysql -u root -e "CREATE DATABASE inventory_db;"
     ```
   - Import the schema and sample data (run from the project root):
     ```sh
     mysql -u root inventory_db < database/schema.sql
     ```
4. Update `config.php` with your MySQL credentials.
5. Go to http://localhost:8080/Inventory-Management-System/login.php

## Next Steps

- Implement real authentication
- Add update and delete functionality
- Add search functionality
- Improve UI/UX

```

```
