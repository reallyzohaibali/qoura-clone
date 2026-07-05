<?php
// Ensure session initialized safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Dynamically target files relative to the current folder location
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/auth.php';

$sidebarUser = getLoggedInUser();
$profilePic = 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&h=100&q=80'; // Default fallback image

if ($sidebarUser) {
    try {
        $sidebarPdo = getDBConnection();
        $sideStmt = $sidebarPdo->prepare("SELECT profile_pic FROM users WHERE id = ?");
        $sideStmt->execute([$sidebarUser['id']]);
        $dbUserData = $sideStmt->fetch();
        if ($dbUserData && !empty($dbUserData['profile_pic'])) {
            $profilePic = $dbUserData['profile_pic'];
        }
    } catch (Exception $e) {
        error_log("Sidebar DB Error: " . $e->getMessage());
    }
}
?>

<div class="position-sticky" style="top: 80px;">
    <?php if ($sidebarUser): ?>
        <!-- Logged In User Profile Widget Card -->
        <div class="card p-3 shadow-sm border-0 bg-white mb-3" style="border-radius: 8px;">
            <div class="text-center mb-3">
                <img src="<?= htmlspecialchars($profilePic) ?>" class="rounded-circle border border-2 shadow-sm mb-2" style="width: 80px; height: 80px; object-fit: cover;" alt="Avatar">
                <h6 class="fw-bold mb-0"><?= htmlspecialchars($sidebarUser['name']) ?></h6>
                <p class="text-muted fs-8 mb-2"><?= htmlspecialchars($sidebarUser['bio']) ?></p>
            </div>
            
            <!-- Secure Image Upload Form -->
            <form action="upload-profile.php" method="POST" enctype="multipart/form-data" class="border-top pt-3">
                <label class="form-label fs-8 fw-bold text-muted text-uppercase mb-1">Update Profile Picture</label>
                <div class="input-group input-group-sm mb-2">
                    <input type="file" name="profile_image" class="form-control fs-8" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-sm btn-outline-danger w-100 rounded-pill fs-8">Upload Image</button>
            </form>
        </div>
    <?php else: ?>
        <!-- Default Fallback Ad Box when logged out -->
        <div class="ad-box d-flex align-items-center justify-content-center text-muted" style="border: 1px solid #dee2e6; background: #f7f7f8; height: 250px; border-radius: 8px 8px 0 0;">
            <span class="fs-7 text-muted-custom">Login to customize your space</span>
        </div>
        <div class="ad-footer shadow-sm bg-white text-center py-2 border text-muted fs-8" style="border-top: none !important; border-radius: 0 0 8px 8px;"> Advertisement </div>
    <?php endif; ?>
</div>