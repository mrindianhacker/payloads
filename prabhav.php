<?php
// DEBUG: show errors (remove or tone down in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Try to include your connection file from several likely locations
$includeCandidates = [
    __DIR__ . "/home1/prabhfwl/public_html/include/z_db2.php",                     // same dir/include
    __DIR__ . "/../include/z_db2.php",                  // one level up
    $_SERVER['DOCUMENT_ROOT'] . "/include/z_db2.php",   // web root include/
    "/var/www/html/include/z_db2.php"                   // common apache path (adjust if needed)
];

$included = false;
foreach ($includeCandidates as $candidate) {
    if (file_exists($candidate)) {
        include_once $candidate;
        $included = true;
        break;
    }
}

if (!$included) {
    // helpful debug message
    die("<strong>Include error:</strong> Could not find include/z_db2.php. Tried: <br>"
        . implode("<br>", array_map('htmlspecialchars', $includeCandidates)));
}

// Expect $link (mysqli) to be provided by z_db2.php
if (!isset($link) || !($link instanceof mysqli)) {
    die("<strong>Connection error:</strong> \$link is not set or not a mysqli instance. Check include/z_db2.php");
}

// simple validator for DB/table identifiers (letters, numbers, underscore)
function valid_identifier($s) {
    return is_string($s) && preg_match('/^[A-Za-z0-9_]+$/', $s);
}

$dbName    = $_GET['db']    ?? '';
$tableName = $_GET['table'] ?? '';
$error     = '';
$result    = null;

if ($dbName !== '' && $tableName !== '') {
    if (!valid_identifier($dbName) || !valid_identifier($tableName)) {
        $error = "Invalid database or table name. Only A-Z, a-z, 0-9 and underscore allowed.";
    } else {
        // Try selecting database
        if (!$link->select_db($dbName)) {
            $error = "Failed to select database <strong>" . htmlspecialchars($dbName) . "</strong>: " . htmlspecialchars($link->error);
        } else {
            // Use backticks for identifiers (we validated them already)
            $sql = "SELECT * FROM `" . $tableName . "` LIMIT 1000"; // LIMIT to avoid huge dumps
            $result = $link->query($sql);
            if ($result === false) {
                $error = "Query failed: " . htmlspecialchars($link->error) . " — SQL: " . htmlspecialchars($sql);
            }
        }
    }
}

// Helper: list DBs for quick selection (optional)
$databases = [];
if ($link) {
    $dbRes = $link->query("SHOW DATABASES");
    if ($dbRes) {
        while ($r = $dbRes->fetch_assoc()) {
            $databases[] = $r[array_keys($r)[0]];
        }
        $dbRes->free();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>DB Viewer</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin:20px; }
        table { border-collapse: collapse; margin-top:10px; }
        th, td { border:1px solid #ccc; padding:6px 8px; }
        th { background:#f2f2f2; }
        .error { color:darkred; font-weight:bold; }
        .note { color:#555; font-size:0.9em; }
    </style>
</head>
<body>

<h2>Database Table Viewer</h2>

<form method="get">
    <label><strong>Database:</strong></label><br>
    <?php if (!empty($databases)): ?>
        <select name="db">
            <option value="">-- select db --</option>
            <?php foreach ($databases as $d): ?>
                <option value="<?php echo htmlspecialchars($d); ?>" <?php echo ($d === $dbName ? 'selected' : ''); ?>>
                    <?php echo htmlspecialchars($d); ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php else: ?>
        <input type="text" name="db" required value="<?php echo htmlspecialchars($dbName); ?>">
    <?php endif; ?>
    <br><br>

    <label><strong>Table:</strong></label><br>
    <input type="text" name="table" required value="<?php echo htmlspecialchars($tableName); ?>">
    <br><br>

    <button type="submit">View</button>
    <span class="note">Identifiers must contain only letters, numbers, and underscores. Results capped at 1000 rows.</span>
</form>

<hr>

<?php if ($error): ?>
    <p class="error"><?php echo $error; ?></p>
<?php endif; ?>

<?php
if ($result instanceof mysqli_result) {
    echo "<h3>Showing: " . htmlspecialchars($dbName) . "." . htmlspecialchars($tableName) . " (rows: " . $result->num_rows . ")</h3>";

    // get fields
    $fields = $result->fetch_fields();
    echo "<table><thead><tr>";
    foreach ($fields as $f) {
        echo "<th>" . htmlspecialchars($f->name) . "</th>";
    }
    echo "</tr></thead><tbody>";

    // rows
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($fields as $f) {
            $col = $f->name;
            echo "<td>" . htmlspecialchars((string)($row[$col] ?? '')) . "</td>";
        }
        echo "</tr>";
    }

    echo "</tbody></table>";
    $result->free();
} elseif ($dbName !== '' && $tableName !== '' && !$error) {
    echo "<p>No rows returned (table empty or inaccessible).</p>";
}
?>

</body>
</html>
