<?php
// order.php — saves the order and shows the receipt
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Who is ordering?
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $customer_name = $_SESSION['username'];
} else {
    $user_id = null;
    $customer_name = trim($_POST['customer_name'] ?? '');
}

if ($customer_name === '') {
    $_SESSION['msg'] = "Please enter your name.";
    header("Location: index.php");
    exit;
}

// Collect the items. The price ALWAYS comes from the database, never from the form.
$items = [];
$subtotal = 0;
$stmt = mysqli_prepare($conn, "SELECT id, name, price FROM products WHERE id = ?");

foreach (($_POST['qty'] ?? []) as $product_id => $qty) {
    $product_id = (int)$product_id;
    $qty = (int)$qty;
    if ($qty < 1) continue;
    if ($qty > 20) $qty = 20;

    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$product) continue;

    $product['qty'] = $qty;
    $subtotal += $product['price'] * $qty;
    $items[] = $product;
}

if (count($items) === 0) {
    $_SESSION['msg'] = "Choose at least one drink before placing the order.";
    header("Location: index.php");
    exit;
}

// 10% discount ONLY for logged-in customers. Guests pay the regular price.
$discount = 0;
if (is_customer()) {
    $discount = round($subtotal * 10 / 100);
}
$total = $subtotal - $discount;

// Save the order
$stmt = mysqli_prepare($conn, "INSERT INTO orders (user_id, customer_name, subtotal, discount, total) VALUES (?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "isiii", $user_id, $customer_name, $subtotal, $discount, $total);
mysqli_stmt_execute($stmt);
$order_id = mysqli_insert_id($conn);

// Save each item
$stmt = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, product_name, price, qty) VALUES (?, ?, ?, ?, ?)");
foreach ($items as $item) {
    mysqli_stmt_bind_param($stmt, "iisii", $order_id, $item['id'], $item['name'], $item['price'], $item['qty']);
    mysqli_stmt_execute($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Received</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <div class="card shadow-sm mx-auto" style="max-width: 560px;">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Order #<?= $order_id ?> received</h5>
            </div>
            <div class="card-body">
                <p>Thank you, <strong><?= e($customer_name) ?></strong>. Tell the barista your order number at the counter.</p>
                <table class="table table-sm">
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= $item['qty'] ?> x <?= e($item['name']) ?></td>
                            <td class="text-end"><?= rupiah($item['price'] * $item['qty']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr><td>Subtotal</td><td class="text-end"><?= rupiah($subtotal) ?></td></tr>
                    <?php if ($discount > 0): ?>
                        <tr class="text-success"><td>Member discount (10%)</td><td class="text-end">-<?= rupiah($discount) ?></td></tr>
                    <?php endif; ?>
                    <tr class="fw-bold"><td>Total</td><td class="text-end"><?= rupiah($total) ?></td></tr>
                </table>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="alert alert-info py-2">Next time, <a href="register.php">register</a> to get 10% off.</div>
                <?php endif; ?>
                <a href="index.php" class="btn btn-primary">Back to menu</a>
            </div>
        </div>
    </div>
</body>
</html>
