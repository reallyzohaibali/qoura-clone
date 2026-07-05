<?php
// login.php
require_once 'config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([trim($_POST['email'])]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user'] = ['id' => $user['id'], 'name' => $user['name'], 'bio' => $user['bio']];
        header("Location: index.php");
        exit;
    } else { $error = "Invalid Credentials!"; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card p-4 shadow-sm" style="width: 400px;">
        <h4 class="fw-bold mb-3">Login to Quora</h4>
        <?php if(isset($error)): ?><div class="alert alert-danger fs-7 py-2"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="mb-2"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
            <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
            <button class="btn btn-danger w-100 rounded-pill mb-2" style="background:#b92b27;">Login</button>
            <p class="fs-7 text-center">New? <a href="signup.php">Create account</a></p>
        </form>
    </div>
</body>
</html>