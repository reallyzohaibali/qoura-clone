<?php
/**
 * Answer Submission Processor
 */
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDBConnection();

    $questionId    = intval($_POST['question_id'] ?? 0);
    $answerContent = trim($_POST['answer_content'] ?? '');
    $userId        = 1; // Simulated User (Shahriar)

    if ($questionId === 0 || empty($answerContent)) {
        header("Location: index.php?error=empty_answer");
        exit;
    }

    try {
        $sql = "INSERT INTO answers (question_id, user_id, content) VALUES (:question_id, :user_id, :content)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':question_id' => $questionId,
            ':user_id'     => $userId,
            ':content'     => $answerContent
        ]);

        header("Location: index.php?success=answer_added");
        exit;
    } catch (PDOException $e) {
        error_log("Answer submission error: " . $e->getMessage());
        header("Location: index.php?error=db_error");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}