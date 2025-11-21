<?php
// This script simulates a REAL SQL Injection vulnerability
// but DOES NOT connect to any database or execute unsafe queries.

// For display/debug only
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Fake user input
$user_input = $_GET['user'] ?? "";

// ❌ INTENTIONALLY VULNERABLE QUERY — FOR DEMO PURPOSE ONLY
// This is what real insecure code looks like.
$fake_query = "SELECT * FROM users WHERE username = '$user_input'";

// Show vulnerable code behavior
?>
<!DOCTYPE html>
<html>
<head>
    <title>SQL Injection Demo (Simulation Only)</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        pre { background: #f8f8f8; padding: 10px; border: 1px solid #ccc; }
        .warn { color: darkred; font-weight: bold; }
    </style>
</head>
<body>

<h2>SQL Injection Demonstration (Safe Simulation)</h2>

<p>Enter something like:</p>
<ul>
    <li><code>' OR '1'='1</code></li>
    <li><code>anything' UNION SELECT * FROM credit_cards --</code></li>
</ul>

<form method="get">
    <label>Username:</label><br>
    <input type="text" name="user" value="<?php echo htmlspecialchars($user_input); ?>" style="width:300px">
    <br><br>
    <button type="submit">Run</button>
</form>

<hr>

<h3>Constructed SQL Query</h3>
<pre><?php echo htmlspecialchars($fake_query); ?></pre>

<p class="warn">⚠ This is how vulnerable real-world PHP code gets hacked.</p>

<h3>Explanation</h3>
<p>
This query is <strong>not executed</strong>. It is only shown to demonstrate
how SQL Injection works when developers directly insert user input
into SQL queries without validation.
</p>

</body>
</html>
