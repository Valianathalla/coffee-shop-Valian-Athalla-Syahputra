<?php
// admin/product_form.php — add or edit a product (name, description, price, image)
include '../config.php';

if (!is_admin()) {
    header("Location: ../login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$product = ['name' => '', 'description' => '', 'price' => '', 'image' => null];

// Editing? Load the existing product
if ($id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$product) {
        die("Product not found.");
    }
}

$error = "";

if (isset($_POST['save'])) {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = (int)$_POST['price'];
    $image       = $product['image']; // keep the old image unless a new one is uploaded

    if ($name === '' || $price < 1) {
        $error = "Enter a name and a price greater than 0.";
    }

    // Image upload (optional)
    if (!$error && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed) || !getimagesize($_FILES['image']['tmp_name'])) {
            $error = "Please upload a JPG, PNG, or WebP image.";
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $error = "The image must be 2 MB or smaller.";
        } else {
            $new_name = uniqid('product_') . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $new_name);
            if ($image) {
                @unlink('../uploads/' . basename($image)); // remove the old file
            }
            $image = $new_name;
        }
    }

    if (!$error) {
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, "UPDATE products SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssisi", $name, $description, $price, $image, $id);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssis", $name, $description, $price, $image);
        }
        mysqli_stmt_execute($stmt);

        $_SESSION['msg'] = "Product saved.";
        header("Location: index.php");
        exit;
    }

    // Keep what was typed when there is an error
    $product['name'] = $name;
    $product['description'] = $description;
    $product['price'] = $price;
}

$root = '../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id ? 'Edit' : 'Add' ?> Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include '../navbar.php'; ?>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><?= $id ? 'Edit Product' : 'Add Product' ?></h5>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?><div class="alert alert-danger py-2"><?= e($error) ?></div><?php endif; ?>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= e($product['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <input type="text" class="form-control" id="description" name="description" value="<?= e($product['description']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price (Rp, whole number)</label>
                                <input type="number" class="form-control" id="price" name="price" value="<?= e($product['price']) ?>" min="1" required>
                            </div>
                            <div class="mb-4">
                                <label for="image" class="form-label">Image (JPG, PNG, WebP, max 2 MB)</label>
                                <?php if ($product['image']): ?>
                                    <div class="mb-2"><img src="../uploads/<?= e($product['image']) ?>" width="80" alt="Current image"></div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" name="save" class="btn btn-success">Save Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
