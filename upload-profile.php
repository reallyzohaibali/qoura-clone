<?php
/**
 * Secure Profile Image Upload Processor
 */
require_once 'config/database.php';
require_once 'auth.php';
requireAuth(); // Ensure user is logged in

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $user = getLoggedInUser();
    $file = $_FILES['profile_image'];

    // 1. Check for basic upload transfer errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        header("Location: index.php?error=upload_failed");
        exit;
    }

    // 2. Validate file size constraint (Cap at 3MB)
    if ($file['size'] > 3 * 1024 * 1024) {
        header("Location: index.php?error=file_too_large");
        exit;
    }

    // 3. Strict extension verification
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        header("Location: index.php?error=invalid_file_type");
        exit;
    }

    // 4. Generate a completely unique filename to avoid overwrites
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    // $newFileName = 'avatar_' . $user['id'] . '_' . time() . '.' . $extension;
    // $uploadTarget = 'uploads/' . $newFileName;
    // Replace the old hardcoded slash line with this:
    $newFileName = 'avatar_' . $user['id'] . '_' . time() . '.' . $extension;
    $uploadTarget = 'uploads' . DIRECTORY_SEPARATOR . $newFileName;

    // 5. Move the temporary file to permanent storage folder
    if (move_uploaded_file($file['tmp_name'], $uploadTarget)) {
        try {
            $pdo = getDBConnection();
            
            // Update image string reference inside the specific user row
            $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
            $stmt->execute([$uploadTarget, $user['id']]);

            header("Location: index.php?success=profile_updated");
            exit;
        } catch (PDOException $e) {
            error_log("Upload Database Error: " . $e->getMessage());
            header("Location: index.php?error=db_failure");
            exit;
        }
    } else {
        header("Location: index.php?error=move_file_failed");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}