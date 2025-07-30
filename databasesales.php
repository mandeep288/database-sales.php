<?php
$conn = mysqli_connect("localhost", "root", "", "inventorydb");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = "";
if (isset($_POST['sell'])) {
    $product_id = intval($_POST['product']);
    $sell_qty = intval($_POST['quantity']);

    // Get product info
    $query = "SELECT quantity, price FROM products WHERE id = $product_id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $current_qty = $row['quantity'];
        $price = $row['price'];

        if ($sell_qty > 0 && $sell_qty <= $current_qty) {
            $new_qty = $current_qty - $sell_qty;
            $total_price = $price * $sell_qty;

            // Update quantity
            $update_query = "UPDATE products SET quantity = $new_qty WHERE id = $product_id";
            mysqli_query($conn, $update_query);

            // Insert into sales table
            $insert_query = "INSERT INTO sales (product_id, quantity, total_price, status) VALUES ($product_id, $sell_qty, $total_price, 'to_ship')";
            mysqli_query($conn, $insert_query);

            $message = "✅ Product sold and recorded successfully!";
        } else {
            $message = "❌ Not enough stock!";
        }
    } else {
        $message = "❌ Product not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sell Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            padding: 20px;
        }
        h2 {
            color: #2c3e50;
        }
        form {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 2px 5px #aaa;
        }
        select, input[type="number"] {
            width: 95%;
            padding: 8px;
            margin: 8px 0 12px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #2ecc71;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .message {
            margin-top: 15px;
            color: #e74c3c;
            font-weight: bold;
        }
        .success {
            color: #27ae60;
        }
         a {
            text-decoration: none;
            margin-bottom: 15px;
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<h2>Sell Product</h2>
<a href="homepagehtml.html">🏠 Back to Home Page</a>

<form method="POST">
    <label>Select Product:</label><br>
    <select name="product" required>
        <option value="">--Choose--</option>
        <?php
        $products = mysqli_query($conn, "SELECT id, name, quantity FROM products");
        while ($p = mysqli_fetch_assoc($products)) {
            echo "<option value='" . $p['id'] . "'>" . htmlspecialchars($p['name']) . " (Available: " . $p['quantity'] . ")</option>";
        }
        ?>
    </select><br>

    <label>Quantity to Sell:</label><br>
    <input type="number" name="quantity" min="1" required><br>

    <input type="submit" name="sell" value="Sell">
</form>

<div class="message <?php echo ($message == "✅ Product sold and recorded successfully!") ? 'success' : ''; ?>">
    <?php echo $message; ?>
</div>

</body>
</html>