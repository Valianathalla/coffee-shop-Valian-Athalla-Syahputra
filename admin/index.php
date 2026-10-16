<?php
// admin/index.php — products + orders (admin only)
include '../config.php';

if (!is_admin()) {
    header("Location: ../login.php");
    exit;
}

$products = mysqli_query($conn, "SELECT * FROM products ORDER BY name");
$orders   = mysqli_query($conn, "SELECT * FROM orders ORDER BY id DESC");
$items_stmt = mysqli_prepare($conn, "SELECT product_name, qty FROM order_items WHERE order_id = ?");

$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);
$root = '../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include '../navbar.php'; ?>
    <div class="container mt-4 mb-5">

        <?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

        <!-- Products -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Products</h5>
                <a href="product_form.php" class="btn btn-success btn-sm">+ Add Product</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr><th>Image</th><th>Name</th><th>Price</th><th class="text-center">Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php while ($p = mysqli_fetch_assoc($products)): ?>
                            <tr>
                                <td>
                                    <?php if ($p['image']): ?>
                                        <img src="../uploads/<?= e($p['image']) ?>" width="60" height="60" style="object-fit: cover;" alt="">
                                    <?php endif; ?>
                                </td>
                                <td><?= e($p['name']) ?></td>
                                <td><?= rupiah($p['price']) ?></td>
                                <td class="text-center">
                                    <a href="product_form.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm"
                                       onclick="return confirm('Delete this product?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Orders -->
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white"><h5 class="mb-0">Orders</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr><th>#</th><th>Customer</th><th>Items</th><th>Subtotal</th><th>Discount</th><th>Total</th><th>Time</th></tr>
                        </thead>
                        <tbody>
                        <?php while ($o = mysqli_fetch_assoc($orders)):
                            $oid = $o['id'];
                            mysqli_stmt_bind_param($items_stmt, "i", $oid);
                            mysqli_stmt_execute($items_stmt);
                            $lines = mysqli_stmt_get_result($items_stmt);
                        ?>
                            <tr>
                                <td><?= $oid ?></td>
                                <td><?= e($o['customer_name']) ?>
                                    <?= $o['user_id'] ? '<span class="badge bg-success">member</span>' : '<span class="badge bg-secondary">guest</span>' ?></td>
                                <td>
                                    <?php while ($l = mysqli_fetch_assoc($lines)): ?>
                                        <?= $l['qty'] ?> x <?= e($l['product_name']) ?><br>
                                    <?php endwhile; ?>
                                </td>
                                <td><?= rupiah($o['subtotal']) ?></td>
                                <td><?= $o['discount'] > 0 ? '-' . rupiah($o['discount']) : '-' ?></td>
                                <td><strong><?= rupiah($o['total']) ?></strong></td>
                                <td><?= e($o['created_at']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
