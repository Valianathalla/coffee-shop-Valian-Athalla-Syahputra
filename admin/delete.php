<?php
// admin/delete.php
include '../config.php';

if (!is_admin()) {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Find the image first so the file can be removed too
    $stmt = mysqli_prepare($conn, "SELECT image FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    if ($product && $product['image']) {
        @unlink('../uploads/' . basename($product['image']));
    }
    $_SESSION['msg'] = "Product deleted.";
}
header("Location: index.php");
exit;
?>
