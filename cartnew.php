<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Shopping Cart</h1>
        <table class="table mt-4">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                if (isset($_SESSION["cart"]) && count($_SESSION["cart"]) > 0) {
                    foreach ($_SESSION["cart"] as $key => $item) {
                        $subtotal = $item["price"] * $item["quantity"];
                        $total += $subtotal;
                        echo "<tr>
                                <td><img src='{$item["image"]}' width='50'></td>
                                <td>{$item["name"]}</td>
                                <td>\${$item["price"]}</td>
                                <td>{$item["quantity"]}</td>
                                <td>\${$subtotal}</td>
                                <td>
                                    <a href='remove_from_cart.php?key={$key}' class='btn btn-danger btn-sm'>Remove</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>Your cart is empty.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <h3>Total: $<?php echo number_format($total, 2); ?></h3>
        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
    </div>
</body>
</html>
