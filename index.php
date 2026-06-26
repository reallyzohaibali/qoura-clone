<?php
// Include required modular files 
require_once 'config/database.php';
require_once 'src/TopicService.php';
require_once 'src/FeedService.php';

// Initialize the central PDO Database resource
$pdo = getDBConnection();

// Check if a specific topic filter has been clicked via the sidebar URL query string
$selectedTopic = isset($_GET['topic']) ? $_GET['topic'] : null;

// Extract database components, passing our filter string if it exists
$topics    = getAllTopics($pdo);
$feedItems = getHomeFeed($pdo, $selectedTopic);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Modular Quora Clone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar navbar-expand-xl navbar-light bg-white border-bottom py-2 shadow-sm fixed-top">
    <div class="container" style="max-width: 1200px;">    
        <a class="navbar-brand quora-brand me-4 pe-1" href="#" style="color: #b92b27; font-weight: 800; font-family: 'Georgia', serif;">Quora</a>
        <form class="d-flex flex-grow-1 search-container me-3 position-relative">
            <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-muted" style="pointer-events: none;"></i>
            <input class="form-control ps-5" type="search" placeholder="Search Quora" aria-label="Search">
        </form>
    </div>
</nav>

<div style="height: 5rem;"></div>

<div class="container" style="max-width: 1040px;">
    <div class="row g-4">
        
        <div class="col-lg-2 d-none d-lg-block">
            <?php include 'includes/sidebar-topics.php'; ?>
        </div>

        <div class="col-10 col-lg-6 mx-auto">
            
            <!-- <div class="card-quora p-3 mb-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&h=100&q=80" class="profile-pic-md" alt="User Profile">
                    <div class="flex-grow-1 composer-input fs-7" style="background-color: #f7f7f8; border: 1px solid #dee2e6; border-radius: 20px; padding: 6px 16px; color: #939598;">What do you want to ask or share?</div>
                </div>
            </div> -->

        <div class="card-quora p-3 mb-3">
            <div class="d-flex align-items-center gap-2">
                <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&h=100&q=80" class="profile-pic-md" alt="User Profile">
                <button class="flex-grow-1 text-start composer-input fs-7 btn border text-muted w-100 bg-light py-2 px-3" 
                        data-bs-toggle="modal" 
                        data-bs-target="#composerModal" 
                        style="border-radius: 20px; border-color: #dee2e6 !important; text-align: left;">
                    What do you want to ask or share?
                </button>
            </div>
        </div>

            <?php if (empty($feedItems)): ?>
                <div class="card-quora p-4 text-center text-muted"> Your feed is clear! Nothing has been posted yet. </div>
            <?php else: ?>
                <?php foreach ($feedItems as $item): ?>
                    <?php include 'includes/feed-card.php'; ?>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>

        <div class="col-lg-4 d-none d-lg-block">
            <div class="position-sticky" style="top: 80px;">
                <div class="ad-box d-flex align-items-center justify-content-center text-muted" style="border: 1px solid #dee2e6; background: #f7f7f8; height: 300px; border-radius: 4px 4px 0 0;"></div>
                <div class="ad-footer shadow-sm bg-white text-center py-2 border text-muted fs-8" style="border-top: none !important;"> Advertisement </div>
            </div>
        </div>

    </div>
</div>

<?php include 'includes/composer-modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>