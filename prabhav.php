<?php
    // Include your DB connection file
    include_once "/home1/prabhfwl/public_html/include/z_db2.php";

    // Query to fetch data
    $sql = "SELECT reg_id, email, password FROM registration_user";
    $result = $link->query($sql);

    if ($result === false) {
        die("Query Error: " . $link->error);
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Users List</title>
</head>
<body>

<h2>Registration User Table</h2>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Reg ID</th>
        <th>Email</th>
        <th>Password</th>
    </tr>

    <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['reg_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['password']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No records found</td></tr>";
        }
    ?>
</table>

</body>
</html>
