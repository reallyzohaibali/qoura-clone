<?php
/**
 * Post Content Modification Processing Engine
 */
require_once 'config/database.php';
require_once 'auth.php';
requireAuth();

$user = getLoggedInUser();
$id = intval($_POST['post_id'] ?? 0);
$newContent = trim($_POST['post_content'] ?? '');

if ($id > 0 && !empty($newContent)) {
    $pdo = getDBConnection();
    // Validate that the active user owns the post row target
    $stmt = $pdo->prepare("UPDATE posts SET content = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$newContent, $id, $user['id']]);
}
header("Location: index.php");
exit;