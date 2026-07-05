<?php
require_once 'config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDBConnection();
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $bio = trim($_POST['bio'] ?? 'Quora User');

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, bio) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $bio]);
        
        $_SESSION['user'] = ['id' => $pdo->lastInsertId(), 'name' => $name, 'bio' => $bio];
        header("Location: index.php");
        exit;
    } catch (PDOException $e) { $error = "Email already registered!"; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card p-4 shadow-sm" style="width: 400px;">
        <h4 class="fw-bold mb-3">Join Quora</h4>
        <?php if(isset($error)): ?><div class="alert alert-danger fs-7 py-2"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="mb-2"><input type="text" name="name" class="form-control" placeholder="Full Name" required></div>
            <div class="mb-2"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
            <div class="mb-2"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
            <div class="mb-3"><input type="text" name="bio" class="form-control" placeholder="Bio (e.g. Writer)"></div>
            <button class="btn btn-danger w-100 rounded-pill mb-2" style="background:#b92b27;">Sign Up</button>
            <p class="fs-7 text-center">Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>
</body>
</html>