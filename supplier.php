<?php
// Database connection info
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventorydb";

// Connect to MySQL server
$conn = mysqli_connect($servername, $username, $password);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if (!mysqli_query($conn, $sql)) {
    die("Error creating database: " . mysqli_error($conn));
}

// Select database
mysqli_select_db($conn, $dbname);

// Create suppliers table if not exists
$table_sql = "CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(100) NOT NULL,
    provided_items TEXT NOT NULL
)";
if (!mysqli_query($conn, $table_sql)) {
    die("Error creating table: " . mysqli_error($conn));
}

// Handle adding new supplier
if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $provided_items = mysqli_real_escape_string($conn, $_POST['provided_items']);

    $insert_sql = "INSERT INTO suppliers (name, contact, provided_items) VALUES ('$name', '$contact', '$provided_items')";
    mysqli_query($conn, $insert_sql);
}

// Handle deleting a supplier
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete_sql = "DELETE FROM suppliers WHERE id = $id";
    mysqli_query($conn, $delete_sql);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Supplier Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f3;
            padding: 20px;
        }
        h2 {
            color: #333;
            margin-bottom: 10px;
        }
        form {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px #aaa;
            width: 400px;
            margin-bottom: 30px;
        }
        input[type="text"], textarea {
            width: 90%;
            padding: 8px;
            margin: 5px 0 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }
        input[type="submit"] {
            background-color: #2e86de;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        table {
            width: 90%;
            background: white;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 5px #aaa;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #2e86de;
            color: white;
        }
        a.delete-btn {
            background-color: #e74c3c;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
            float: right;
        }
        a.delete-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<h2>Add Supplier</h2>
<form method="POST" action="">
    <input type="text" name="name" placeholder="Supplier Name" required><br>
    <input type="text" name="contact" placeholder="Contact Details" required><br>
    <textarea name="provided_items" placeholder="Items Provided" rows="3" required></textarea><br>
    <input type="submit" name="add" value="Add Supplier">
</form>

<h2>Supplier List</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Supplier Name</th>
        <th>Contact</th>
        <th>Items Provided</th>
        <th>Action</th>
    </tr>
    <?php
    $result = mysqli_query($conn, "SELECT * FROM suppliers");
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['contact']) . "</td>";
        echo "<td>" . htmlspecialchars($row['provided_items']) . "</td>";
        echo "<td><a class='delete-btn' href='?delete=" . $row['id'] . "' onclick='return confirm(\"Delete this supplier?\")'>Delete</a></td>";
        echo "</tr>";
    }
    ?>
</table>

</body>
</html>