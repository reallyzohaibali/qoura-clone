<?php
/**
 * Topic Service Module
 * Handles all structural queries relating to sidebar navigation categories.
 */

function getAllTopics($pdo) {
    try {
        $sql = "SELECT id, name, icon_url, slug FROM topics ORDER BY name ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        // In development, error logging helps isolate problems safely
        error_log("Error in getAllTopics: " . $e->getMessage());
        return [];
    }
}