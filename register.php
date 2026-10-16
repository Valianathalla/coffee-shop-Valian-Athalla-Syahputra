<?php
// register.php — creates a customer account
include 'config.php';

$error = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username === '' || strlen($password) < 6) {
        $error = "Enter a username and a password of at least 6 characters.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, 'customer')");
        mysqli_stmt_bind_param($stmt, "ss", $username, $hash);

        try {
            mysqli_stmt_execute($stmt);
            $_SESSION['msg'] = "Account created. Please log in to get 10% off.";
            header("Location: login.php");
            exit;
        } catch (mysqli_sql_exception $e) {
            $error = "Username is already taken.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    <div class="container mt-5" style="max-width: 420px;">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white"><h5 class="mb-0">Register</h5></div>
            <div class="card-body">
                <?php if ($error): ?><div class="alert alert-danger py-2"><?= e($error) ?></div><?php endif; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password (min. 6 characters)</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="6" required>
                    </div>
                    <div class="d-grid"><button type="submit" name="register" class="btn btn-success">Create Account</button></div>
                </form>
                <p class="mt-3 mb-0 small">Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>
