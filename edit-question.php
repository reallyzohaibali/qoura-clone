<?php
require_once 'config/database.php';
require_once 'auth.php';
requireAuth();

$user = getLoggedInUser();
$id = intval($_POST['question_id'] ?? 0);
$newTitle = trim($_POST['question_title'] ?? '');

if ($id > 0 && !empty($newTitle)) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE questions SET title = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$newTitle, $id, $user['id']]);
}
header("Location: index.php");
exit;