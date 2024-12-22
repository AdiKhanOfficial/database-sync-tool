
# Database Sync Tool

This PHP script is designed to synchronize database structures between two MySQL databases. It compares tables and columns in an old database and a new/updated database, then generates SQL statements to align the old database with the updated structure.

## Features
- Identifies missing tables in the old database and generates SQL to create them.
- Detects extra tables in the old database and generates SQL to drop them.
- Compares columns within matching tables to:
  - Add missing columns.
  - Drop extra columns.
- Outputs SQL statements for manual review and execution.

## Prerequisites
- PHP 7.4 or higher.
- MySQL database access for both databases.
- Basic understanding of SQL for manual execution of the generated queries.

## Installation
1. Clone this repository:
   ```bash
   git clone https://github.com/adikhanofficial/database-sync-tool.git
   ```
2. Navigate to the project directory:
   ```bash
   cd DatabaseSyncTool
   ```
3. Place the `dbsync.php` script on your web server or local PHP environment.

## Usage
1. Edit the `dbsync.php` file to include your database credentials:
   ```php
   // Old Database details
   define('DB1_NAME', 'your_old_database_name');
   define('DB1_USER', 'your_old_database_user');
   define('DB1_PASSWORD', 'your_old_database_password');

   // New/Updated Database details
   define('DB2_NAME', 'your_new_database_name');
   define('DB2_USER', 'your_new_database_user');
   define('DB2_PASSWORD', 'your_new_database_password');
   ```

2. Run the script in your PHP environment:
   - If using a web server, access it via your browser:
     ```
     http://localhost/dbsync.php
     ```
   - If using CLI, run:
     ```bash
     php dbsync.php
     ```

3. Review the generated SQL statements in the output and execute them as needed.

## Example Output
The script generates SQL queries like:
```sql
ALTER TABLE `xgadgets_fonesurgeons`.`products` ADD COLUMN `wholesale_price` DECIMAL(10, 2) NULL;
DROP TABLE `xgadgets_fonesurgeons`.`obsolete_table`;
```

## Contributing
If you'd like to contribute:
1. Fork the repository.
2. Create a feature branch:
   ```bash
   git checkout -b feature-name
   ```
3. Commit your changes:
   ```bash
   git commit -m "Description of changes"
   ```
4. Push to your branch:
   ```bash
   git push origin feature-name
   ```
5. Open a pull request.

## License
This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Disclaimer
Use this tool at your own risk. Always back up your databases before making structural changes.
