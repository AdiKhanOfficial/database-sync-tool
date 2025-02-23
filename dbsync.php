<?php
// Database connection details

//Old Database details
define('DB1_NAME', 'ENTER_DB1_NAME');
define('DB1_USER', 'ENTER_DB1_USERNAME');
define('DB1_PASSWORD', 'ENTER_DB1_PASSWORD'); 

// New/Updated Database details
define('DB2_NAME', 'ENTER_DB2_NAME');
define('DB2_USER', 'ENTER_DB2_USERNAME');
define('DB2_PASSWORD', 'ENTER_DB2_PASSWORD'); 

// Establish connections
$conn1 = connectToDatabase(DB1_NAME, DB1_USER, DB1_PASSWORD);
$conn2 = connectToDatabase(DB2_NAME, DB2_USER, DB2_PASSWORD);

echo "Please run the following queries manually!";
echo "<br><br><code style='padding: 10px;background: #202020;border-radius: 5px;color: white;'>";
// Get the list of tables from both databases
$tableListDb1 = fetchTableList($conn1);
$tableListDb2 = fetchTableList($conn2);

// Synchronize table structures and columns
synchronizeTables($tableListDb1, $tableListDb2, $conn1, $conn2);

echo "</code>";

/**
 * Establishes a connection to a MySQL database.
 */
function connectToDatabase($dbName, $dbUser, $dbPassword) {
    $connection = mysqli_connect('localhost', $dbUser, $dbPassword, $dbName);
    if (!$connection) {
        die("Database connection failed: " . mysqli_connect_error());
    }
    return $connection;
}

/**
 * Fetches the list of tables in a database.
 */
function fetchTableList($connection) {
    $tables = [];
    $query = "SHOW TABLES";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        die("Error fetching table list: " . mysqli_error($connection));
    }

    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }
    return $tables;
}

/**
 * Synchronizes tables between two databases.
 */
function synchronizeTables($tablesDb1, $tablesDb2, $conn1, $conn2) {
    foreach ($tablesDb1 as $table) {
        if (!in_array($table, $tablesDb2)) {
            echo generateDropTableQuery(DB1_NAME, $table);
        }
    }

    foreach ($tablesDb2 as $table) {
        if (!in_array($table, $tablesDb1)) {
            echo generateCreateTableQuery(DB1_NAME, DB2_NAME, $table);
        } else {
            $columnsDb1 = fetchColumnList($conn1, $table);
            $columnsDb2 = fetchColumnList($conn2, $table);
            synchronizeColumns($columnsDb1, $columnsDb2, $table);
        }
    }
}

/**
 * Fetches the list of columns in a table.
 */
function fetchColumnList($connection, $tableName) {
    $columns = [];
    $query = "DESC $tableName";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        die("Error describing table $tableName: " . mysqli_error($connection));
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $columns[] = $row['Field'];
    }
    return $columns;
}

/**
 * Synchronizes columns in a table between two databases.
 */
function synchronizeColumns($columnsDb1, $columnsDb2, $tableName) {
    foreach ($columnsDb1 as $column) {
        if (!in_array($column, $columnsDb2)) {
            echo generateDropColumnQuery(DB1_NAME, $tableName, $column);
        }
    }

    foreach ($columnsDb2 as $column) {
        if (!in_array($column, $columnsDb1)) {
            $columnDetails = fetchColumnDetails(DB2_NAME, $tableName, $column);
            echo generateAddColumnQuery(DB1_NAME, $tableName, $columnDetails);
        }
    }
}

/**
 * Fetches detailed column definition for a specific column.
 */
function fetchColumnDetails($dbName, $tableName, $columnName) {
    $connection = connectToDatabase($dbName, DB2_USER, DB2_PASSWORD);
    $query = "DESC `$tableName`";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        die("Error fetching column details for $tableName.$columnName: " . mysqli_error($connection));
    }

    $columnDetails = "";
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['Field'] === $columnName) {
            $null = ($row['Null'] === 'NO') ? 'NOT NULL' : 'NULL';
            $default = ($row['Default'] !== null) 
                ? "DEFAULT " . ($row['Default'] === 'current_timestamp()' ? $row['Default'] : "'{$row['Default']}'") 
                : '';
            $columnDetails = "`{$row['Field']}` {$row['Type']} $null $default {$row['Extra']}";
            break;
        }
    }
    mysqli_close($connection);
    return $columnDetails;
}

/**
 * Generates a SQL query to drop a table.
 */
function generateDropTableQuery($dbName, $tableName) {
    return "DROP TABLE `$dbName`.`$tableName`;<br>";
}

/**
 * Generates a SQL query to create a table.
 */
function generateCreateTableQuery($db1, $db2, $tableName) {
    return "CREATE TABLE `$db1`.`$tableName` LIKE `$db2`.`$tableName`;<br>";
}

/**
 * Generates a SQL query to drop a column.
 */
function generateDropColumnQuery($dbName, $tableName, $columnName) {
    return "ALTER TABLE `$dbName`.`$tableName` DROP COLUMN `$columnName`;<br>";
}

/**
 * Generates a SQL query to add a column.
 */
function generateAddColumnQuery($dbName, $tableName, $columnDetails) {
    return "ALTER TABLE `$dbName`.`$tableName` ADD $columnDetails;<br>";
}
?>
