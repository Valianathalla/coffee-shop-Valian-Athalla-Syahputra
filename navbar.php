<?php
// navbar.php — include right after <body>. Set $root = '../' before including from /admin.
$root = $root ?? '';
?>
<nav class="navbar navbar-expand navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= $root ?>index.php">Coffee Shop</a>
        <div class="d-flex align-items-center gap-2">
            <?php if (is_admin()): ?>
                <a href="<?= $root ?>admin/index.php" class="btn btn-outline-light btn-sm">Admin</a>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="text-white small"><?= e($_SESSION['username']) ?>
                    <span class="badge bg-secondary"><?= e($_SESSION['role']) ?></span></span>
                <a href="<?= $root ?>logout.php" class="btn btn-danger btn-sm">Logout</a>
            <?php else: ?>
                <a href="<?= $root ?>login.php" class="btn btn-outline-light btn-sm">Login</a>
                <a href="<?= $root ?>register.php" class="btn btn-success btn-sm">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
