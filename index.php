<?php
require_once 'config/database.php';
require_once 'src/TopicService.php';
require_once 'src/FeedService.php';
require_once 'auth.php';

$pdo = getDBConnection();
$currentUser = getLoggedInUser();

$search = $_GET['search'] ?? null;
$topic = $_GET['topic'] ?? null;

$topics = getAllTopics($pdo);
$feedItems = getHomeFeed($pdo, $topic, $search);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quora</title>
    <!-- Core UI CSS Framework Stack -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<!-- Header Component Integration -->
<nav class="navbar navbar-expand-xl navbar-light bg-white border-bottom py-2 fixed-top shadow-sm">
    <div class="container" style="max-width: 1200px;">    
        <a class="navbar-brand fw-extrabold text-danger fs-3 me-4" href="index.php" style="font-family:serif;">Quora</a>  
        
        <!-- Live Search Field Input -->
        <form class="d-flex flex-grow-1 position-relative me-3" method="GET" action="index.php">
            <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
            <input class="form-control ps-5 rounded-pill" type="search" name="search" placeholder="Search questions or usernames..." value="<?= htmlspecialchars($search ?? '') ?>">
        </form>

        <div class="d-flex align-items-center gap-2">
            <?php if ($currentUser): ?>
                <span class="fs-7 me-2 text-muted">Hi, <b><?= htmlspecialchars($currentUser['name']) ?></b></span>
                <a href="logout.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-sm btn-outline-danger rounded-pill px-3">Login</a>
                <a href="signup.php" class="btn btn-sm btn-danger rounded-pill px-3" style="background:#b92b27;">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div style="height: 5.5rem;"></div>

<div class="container" style="max-width: 1040px;">
    <div class="row g-4">
        <!-- COLUMN 1: LEFT TOPICS SIDEBAR CONTAINER -->
        <div class="col-lg-2 d-none d-lg-block">
            <?php include 'includes/sidebar-topics.php'; ?>
        </div>

        <!-- COLUMN 2: MIDDLE FEEDS DISPLAY HUB CONTAINER -->
        <div class="col-10 col-lg-6 mx-auto">
            <!-- Composer Modal Toggle Bar Context -->
            <?php if ($currentUser): ?>
                <div class="card p-3 mb-3 shadow-sm border-0 bg-white" style="border-radius:8px;">
                    <button type="button" class="btn btn-light text-start text-muted w-100 rounded-pill ps-3 fs-7 border" data-bs-toggle="modal" data-bs-target="#composerModal">
                        What do you want to ask or share?
                    </button>
                </div>
            <?php endif; ?>

            <!-- Feed Loop Iteration Grid Execution Context -->
            <?php foreach ($feedItems as $item): ?>
                <div class="card p-3 mb-3 border-0 shadow-sm bg-white" style="border-radius:8px;">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex gap-2 mb-2">
                            <img src="<?= htmlspecialchars($item['profile_pic']) ?>" class="rounded-circle" style="width:40px; height:40px; object-fit:cover;" alt="avatar">
                            <div>
                                <div class="fs-7 fw-bold"><?= htmlspecialchars($item['author_name']) ?> <span class="text-muted fw-normal">in <?= htmlspecialchars($item['topic_name']) ?></span></div>
                                <div class="text-muted fs-8"><?= htmlspecialchars($item['author_box']) ?></div>
                            </div>
                        </div>
                        
                        <!-- Inline Edit & Delete Context Commands Matrix Check -->
                        <?php if ($currentUser && $currentUser['id'] == $item['author_id']): ?>
                            <div>
                                <?php if ($item['type'] === 'question'): ?>
                                    <button type="button" class="btn btn-sm btn-link text-secondary text-decoration-none" data-bs-toggle="collapse" data-bs-target="#editQuest<?= $item['item_id'] ?>" aria-expanded="false" aria-controls="editQuest<?= $item['item_id'] ?>"><i class="bi bi-pencil"></i></button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-link text-secondary text-decoration-none" data-bs-toggle="collapse" data-bs-target="#editPost<?= $item['item_id'] ?>" aria-expanded="false" aria-controls="editPost<?= $item['item_id'] ?>"><i class="bi bi-pencil"></i></button>
                                <?php endif; ?>
                                <a href="delete.php?id=<?= $item['item_id'] ?>&type=<?= $item['type'] ?>" class="btn btn-sm btn-link text-danger text-decoration-none" onclick="return confirm('Delete permanently?')"><i class="bi bi-trash"></i></a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Layout Content Switch Branching Layer -->
                    <?php if ($item['type'] === 'question'): ?>
                        <div class="fw-bold fs-6 my-1 text-dark"><?= htmlspecialchars($item['question_title']) ?></div>
                        <div class="collapse my-2" id="editQuest<?= $item['item_id'] ?>">
                            <form action="edit-question.php" method="POST" class="d-flex gap-2">
                                <input type="hidden" name="question_id" value="<?= $item['item_id'] ?>">
                                <input type="text" name="question_title" class="form-control form-control-sm" value="<?= htmlspecialchars($item['question_title']) ?>" required>
                                <button type="submit" class="btn btn-sm btn-success">Save</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="fs-7 text-secondary my-2"><?= nl2br(htmlspecialchars($item['content'])) ?></div>
                        <div class="collapse my-2" id="editPost<?= $item['item_id'] ?>">
                            <form action="edit-post.php" method="POST" class="d-flex flex-column gap-2">
                                <input type="hidden" name="post_id" value="<?= $item['item_id'] ?>">
                                <textarea name="post_content" class="form-control form-control-sm" rows="3" required><?= htmlspecialchars($item['content']) ?></textarea>
                                <button type="submit" class="btn btn-sm btn-success align-self-end px-3">Save Post</button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Nested Answers Loop Component Processing Block -->
                    <?php if ($item['type'] === 'question'): ?>
                        <div class="bg-light p-2 rounded mt-2 border-start border-3 border-secondary">
                            <h6 class="fs-8 fw-bold text-muted mb-2 text-uppercase"><i class="bi bi-chat-left-text me-1"></i> Answers</h6>
                            <?php if (empty($item['answers'])): ?>
                                <p class="text-muted fs-7 mb-0 italic">No answers logged yet.</p>
                            <?php else: ?>
                                <?php foreach ($item['answers'] as $ans): ?>
                                    <div class="border-bottom pb-2 mb-2 last-border-0 pt-1">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <img src="<?= htmlspecialchars($ans['profile_pic']) ?>" class="rounded-circle" style="width:24px; height:24px; object-fit:cover;" alt="avatar">
                                            <span class="fs-8 fw-bold text-dark"><?= htmlspecialchars($ans['author_name']) ?></span>
                                            <span class="fs-9 text-muted"><?= htmlspecialchars($ans['author_box']) ?></span>
                                        </div>
                                        <div class="fs-7 text-dark ps-4"><?= nl2br(htmlspecialchars($ans['content'])) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Active Inline Answer Submissions Input Interface -->
                        <?php if ($currentUser): ?>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-link text-decoration-none fs-7 p-0 text-primary" data-bs-toggle="collapse" data-bs-target="#ansBox<?= $item['item_id'] ?>" aria-expanded="false" aria-controls="ansBox<?= $item['item_id'] ?>">
                                    <i class="bi bi-reply"></i> Write Response
                                </button>
                            </div>
                            <div class="collapse mt-2" id="ansBox<?= $item['item_id'] ?>">
                                <form action="submit-answer.php" method="POST">
                                    <input type="hidden" name="question_id" value="<?= $item['item_id'] ?>">
                                    <textarea class="form-control form-control-sm mb-1" name="answer_content" rows="2" placeholder="Write your professional response..." required></textarea>
                                    <button type="submit" class="btn btn-sm btn-danger float-end" style="background:#b92b27; border:0;">Submit</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Bottom Functional Dynamic Votes Counters Context Link Bar -->
                    <div class="d-flex justify-content-between align-items-center mt-3 border-top pt-2">
                        <div class="upvote-group d-flex align-items-center bg-light border rounded-pill overflow-hidden">
                            <a href="vote.php?item_id=<?= $item['item_id'] ?>&type=<?= $item['type'] ?>&action=up" class="btn btn-sm px-3 text-primary border-0"><i class="bi bi-arrow-up-circle-fill"></i> Upvote <span class="badge bg-secondary ms-1"><?= $item['vote_score'] ?></span></a>
                            <a href="vote.php?item_id=<?= $item['item_id'] ?>&type=<?= $item['type'] ?>&action=down" class="btn btn-sm px-2 border-start border-0 text-muted"><i class="bi bi-arrow-down-circle"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- COLUMN 3: RIGHT SYSTEM WIDGET AD BAR CONTAINER -->
        <div class="col-lg-4 d-none d-lg-block">
            <?php include 'includes/ad-sidebar.php'; ?>
        </div>
    </div>
</div>

<!-- Bootstrapping JavaScript Layout Script Interceptor Handlers -->
<?php 
if ($currentUser) { 
    include 'includes/composer-modal.php'; 
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>