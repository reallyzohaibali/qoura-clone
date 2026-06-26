<?php
/**
 * Clean Split-Table Voting Engine
 */
require_once 'config/database.php';

$itemId   = isset($_GET['item_id']) ? intval($_GET['item_id']) : 0;
$itemType = isset($_GET['type']) ? $_GET['type'] : ''; // 'post' or 'question'
$action   = isset($_GET['action']) ? $_GET['action'] : ''; // 'up' or 'down'
$userId   = 1; // Simulated active user (Shahriar)

if ($itemId === 0 || !in_array($itemType, ['post', 'question']) || !in_array($action, ['up', 'down'])) {
    header("Location: index.php");
    exit;
}

$pdo = getDBConnection();

try {
    if ($action === 'up') {
        // --- UPVOTE LOGIC ---
        // 1. Remove any downvote they might have cast on this item first
        $delDown = $pdo->prepare("DELETE FROM downvotes WHERE user_id = :u AND item_id = :i AND item_type = :t");
        $delDown->execute([':u' => $userId, ':i' => $itemId, ':t' => $itemType]);

        // 2. Check if they already upvoted this
        $checkUp = $pdo->prepare("SELECT id FROM upvotes WHERE user_id = :u AND item_id = :i AND item_type = :t");
        $checkUp->execute([':u' => $userId, ':i' => $itemId, ':t' => $itemType]);
        
        if ($checkUp->fetch()) {
            // Already upvoted? Remove it (Toggle off)
            $removeUp = $pdo->prepare("DELETE FROM upvotes WHERE user_id = :u AND item_id = :i AND item_type = :t");
            $removeUp->execute([':u' => $userId, ':i' => $itemId, ':t' => $itemType]);
        } else {
            // Not upvoted yet? Add it
            $addUp = $pdo->prepare("INSERT INTO upvotes (user_id, item_id, item_type) VALUES (:u, :i, :t)");
            $addUp->execute([':u' => $userId, ':i' => $itemId, ':t' => $itemType]);
        }
    } else {
        // --- DOWNVOTE LOGIC ---
        // 1. Remove any upvote they might have cast on this item first
        $delUp = $pdo->prepare("DELETE FROM upvotes WHERE user_id = :u AND item_id = :i AND item_type = :t");
        $delUp->execute([':u' => $userId, ':i' => $itemId, ':t' => $itemType]);

        // 2. Check if they already downvoted this
        $checkDown = $pdo->prepare("SELECT id FROM downvotes WHERE user_id = :u AND item_id = :i AND item_type = :t");
        $checkDown->execute([':u' => $userId, ':i' => $itemId, ':t' => $itemType]);
        
        if ($checkDown->fetch()) {
            // Already downvoted? Remove it (Toggle off)
            $removeDown = $pdo->prepare("DELETE FROM downvotes WHERE user_id = :u AND item_id = :i AND item_type = :t");
            $removeDown->execute([':u' => $userId, ':i' => $itemId, ':t' => $itemType]);
        } else {
            // Not downvoted yet? Add it
            $addDown = $pdo->prepare("INSERT INTO downvotes (user_id, item_id, item_type) VALUES (:u, :i, :t)");
            $addDown->execute([':u' => $userId, ':i' => $itemId, ':t' => $itemType]);
        }
    }

    header("Location: index.php");
    exit;

} catch (PDOException $e) {
    error_log("Voting Error: " . $e->getMessage());
    header("Location: index.php?error=vote_failed");
    exit;
}