<?php
    // Include your database connection file
    include_once $_SERVER['DOCUMENT_ROOT'] . "/include/z_db2.php";

    // A simple whitelist function to avoid SQL injection in table/DB names
    function validate_name($name) {
        return preg_match('/^[a-zA-Z0-9_]+$/', $name);
    }

    $dbName     = $_GET['db'] ?? null;
    $tableName  = $_GET['table'] ?? null;
    $error      = "";

    if ($dbName && $tableName) {
        
        // Validate DB and table names
        if (!validate_name($dbName) || !validate_name($tableName)) {
            $error = "Invalid database or table name.";
        } else {
            // Select database
            if (!$link->select_db($dbName)) {
                $error = "Failed to select database: " . $link->error;
            } else {
                // Fetch table data
                $query = "SELECT * FROM `$tableName`";
                $result = $link->query($query);

                if ($result === false) {
                    $error = "Query Error: " . $link->error;
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Viewer</title>
</head>
<body>

<h2>View Database Table Data</h2>

<form method="GET">
    <label>Database Name:</label><br>
    <input type="text" name="db" required value="<?php echo htmlspecialchars($dbName); ?>"><br><br>

    <label>Table Name:</label><br>
    <input type="text" name="table" required value="<?php echo htmlspecialchars($tableName); ?>"><br><br>

    <button type="submit">View Data</button>
</form>

<hr>

<?php if ($error): ?>
    <p style="color:red;"><strong><?php echo $error; ?></strong></p>
<?php endif; ?>

<?php
    // Display data if query succeeded
    if (isset($result) && $result && $result->num_rows > 0) {
        
        echo "<h3>Results from: <b>$dbName.$tableName</b></h3>";
        echo "<table border='1' cellpadding='8' cellspacing='0'><tr>";

        // Print table headers
        while ($field = $result->fetch_field()) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "</tr>";

        // Print table rows
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $col => $val) {
                echo "<td>" . htmlspecialchars($val) . "</td>";
            }
            echo "</tr>";
        }

        echo "</table>";

    } elseif ($dbName && $tableName && !$error) {
        echo "<p>No records found in this table.</p>";
    }
?>

</body>
</html>
