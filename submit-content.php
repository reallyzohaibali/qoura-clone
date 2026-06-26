<?php
/**
 * Content Submission Processor Engine
 * Handles explicit POST routing for newly engineered user content blocks.
 */

require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDBConnection();

    // Sanitize incoming payload strings safely
    $contentType = $_POST['content_type'] ?? 'question';
    $userId      = intval($_POST['user_id'] ?? 1);
    $topicId     = intval($_POST['topic_id'] ?? 0);
    $contentBody = trim($_POST['content_body'] ?? '');

    // Prevent submission routing if empty fields are intercepted
    if (empty($contentBody) || $topicId === 0) {
        header("Location: index.php?error=missing_fields");
        exit;
    }

    try {
        if ($contentType === 'question') {
            // Write to the questions table
            $sql = "INSERT INTO questions (user_id, topic_id, title) VALUES (:user_id, :topic_id, :title)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':user_id'  => $userId,
                ':topic_id' => $topicId,
                ':title'    => $contentBody
            ]);
        } else {
            // Write to the posts table
            $sql = "INSERT INTO posts (user_id, topic_id, content) VALUES (:user_id, :topic_id, :content)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':user_id'  => $userId,
                ':topic_id' => $topicId,
                ':content'  => $contentBody
            ]);
        }

        // Send them back to refresh the main timeline upon successful execution
        header("Location: index.php?success=1");
        exit;

    } catch (PDOException $e) {
        error_log("Submission Processing Failure: " . $e->getMessage());
        header("Location: index.php?error=database_failure");
        exit;
    }
} else {
    // Redirect if accessed directly via browser URL bar instead of form button click
    header("Location: index.php");
    exit;
}