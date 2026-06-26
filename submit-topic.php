<?php
/**
 * Topic Submission Processor Engine
 */
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDBConnection();

    $topicName = trim($_POST['topic_name'] ?? '');
    $iconUrl   = trim($_POST['icon_url'] ?? '');

    // Fallback placeholder image asset if the user leaves the icon input blank
    if (empty($iconUrl)) {
        $iconUrl = 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=50&q=80';
    }

    if (empty($topicName)) {
        header("Location: index.php?error=empty_topic");
        exit;
    }

    // SLUG GENERATION LOGIC: Converts "Proofs (mathematics)" to "proofs-mathematics"
    $slug = strtolower($topicName);
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug); // Remove special characters
    $slug = preg_replace('/[\s-]+/', '-', $slug);      // Clean spaces/hyphens down to single dashes
    $slug = trim($slug, '-');                          // Trim hanging dashes

    try {
        // Double check if slug string collides in system database matrix
        $check = $pdo->prepare("SELECT id FROM topics WHERE slug = :slug");
        $check->execute([':slug' => $slug]);
        
        if ($check->fetch()) {
            header("Location: index.php?error=topic_exists");
            exit;
        }

        // Insert new structural row tracking information
        $sql = "INSERT INTO topics (name, icon_url, slug) VALUES (:name, :icon_url, :slug)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name'     => $topicName,
            ':icon_url' => $iconUrl,
            ':slug'     => $slug
        ]);

        // Refresh baseline back to dashboard index
        header("Location: index.php?success=topic_created");
        exit;

    } catch (PDOException $e) {
        error_log("Topic Creation Database Error: " . $e->getMessage());
        header("Location: index.php?error=db_failure");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}