<?php
require_once 'config/database.php';
require_once 'auth.php';
requireAuth();

$user = getLoggedInUser();
$id = intval($_GET['id'] ?? 0);
$type = $_GET['type'] ?? '';

if ($id > 0 && in_array($type, ['post', 'question'])) {
    $pdo = getDBConnection();
    $table = ($type === 'post') ? 'posts' : 'questions';
    
    // Verify target ownership explicitly
    $stmt = $pdo->prepare("DELETE FROM {$table} WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user['id']]);
}
header("Location: index.php");
exit;