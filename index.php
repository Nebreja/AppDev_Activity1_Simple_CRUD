<?php
include 'connect.php';
session_start();

// Initialize variables
$product_id = $name = $description = $price = $quantity = $barcode = "";
$created_at = $updated_at = date("Y-m-d H:i:s");

// Handle add/edit submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['username'];
    $description = $_POST['descript'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $barcode = $_POST['barcode'];

    if (!empty($_POST['product_id'])) {
        // Update existing product
        $product_id = $_POST['product_id'];
        $stmt = $con->prepare("UPDATE product SET name=?, description=?, price=?, quantity=?, barcode=?, updated_at=? WHERE id=?");
        $stmt->execute([$name, $description, $price, $quantity, $barcode, $updated_at, $product_id]);
    } else {
        // Add new product
        $stmt = $con->prepare("INSERT INTO product (name, description, price, quantity, barcode, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $quantity, $barcode, $created_at, $updated_at]);
    }
    header("Location: index.php");
    exit();
}

// Handle delete and edit
if (isset($_GET['delete_id'])) {
    $stmt = $con->prepare("DELETE FROM product WHERE id=?");
    $stmt->execute([$_GET['delete_id']]);
    header("Location: index.php");
    exit();
} elseif (isset($_GET['edit_id'])) {
    $stmt = $con->prepare("SELECT * FROM product WHERE id=?");
    $stmt->execute([$_GET['edit_id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        $product_id = $product['id'];
        $name = $product['name'];
        $description = $product['description'];
        $price = $product['price'];
        $quantity = $product['quantity'];
        $barcode = $product['barcode'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
</head>
<body>
    <h1>Product Management</h1>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Barcode</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $con->query("SELECT * FROM product");
            foreach ($stmt as $product) {
                echo "<tr>
                    <td>{$product['name']}</td>
                    <td>{$product['description']}</td>
                    <td>{$product['price']}</td>
                    <td>{$product['quantity']}</td>
                    <td>{$product['barcode']}</td>
                    <td>{$product['created_at']}</td>
                    <td>{$product['updated_at']}</td>
                    <td>
                        <a href='index.php?edit_id={$product['id']}'>Edit</a> | 
                        <a href='index.php?delete_id={$product['id']}' onclick='return confirm(\"Delete this product?\")'>Delete</a>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>

    <h2><?php echo $product_id ? 'Edit Product' : 'Add Product'; ?></h2>
    <form method="post">
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
        <input type="text" name="username" value="<?php echo $name; ?>" placeholder="Name" required>
        <input type="text" name="descript" value="<?php echo $description; ?>" placeholder="Description" required>
        <input type="text" name="price" value="<?php echo $price; ?>" placeholder="Price" required>
        <input type="text" name="quantity" value="<?php echo $quantity; ?>" placeholder="Quantity" required>
        <input type="text" name="barcode" value="<?php echo $barcode; ?>" placeholder="Barcode" required>
        <button type="submit"><?php echo $product_id ? 'Update' : 'Submit'; ?></button>
    </form>
</body>
</html>
