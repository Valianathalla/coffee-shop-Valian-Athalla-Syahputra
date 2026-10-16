<?php
// index.php — menu + order form (guests and customers)
include 'config.php';

$result = mysqli_query($conn, "SELECT * FROM products ORDER BY name");

$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">

        <?php if ($msg): ?>
            <div class="alert alert-warning"><?= e($msg) ?></div>
        <?php endif; ?>

        <?php if (is_customer()): ?>
            <div class="alert alert-success">You are logged in, so you get <strong>10% off</strong> this order.</div>
        <?php else: ?>
            <div class="alert alert-info">Regular prices apply. <a href="login.php">Log in</a> or <a href="register.php">register</a> to get 10% off.</div>
        <?php endif; ?>

        <form method="POST" action="order.php">
            <div class="row g-3">
                <?php while ($p = mysqli_fetch_assoc($result)):
                    $price = (int)$p['price'];
                    $member_price = $price - round($price * 10 / 100);
                ?>
                <div class="col-6 col-md-4">
                    <div class="card h-100 shadow-sm">
                        <?php if ($p['image']): ?>
                            <img src="uploads/<?= e($p['image']) ?>" class="card-img-top" alt="<?= e($p['name']) ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= e($p['name']) ?></h5>
                            <p class="card-text text-muted small"><?= e($p['description']) ?></p>
                            <?php if (is_customer()): ?>
                                <p class="mb-2"><strong><?= rupiah($member_price) ?></strong>
                                    <s class="text-muted small"><?= rupiah($price) ?></s></p>
                            <?php else: ?>
                                <p class="mb-2"><strong><?= rupiah($price) ?></strong></p>
                            <?php endif; ?>
                            <label class="form-label small mb-1" for="qty<?= $p['id'] ?>">Quantity</label>
                            <input type="number" class="form-control" id="qty<?= $p['id'] ?>"
                                   name="qty[<?= $p['id'] ?>]" value="0" min="0" max="20">
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <div class="card shadow-sm mt-4 mb-5">
                <div class="card-body">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <p class="mb-3">Ordering as <strong><?= e($_SESSION['username']) ?></strong></p>
                    <?php else: ?>
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Your name</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                        </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-success">Place Order</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
