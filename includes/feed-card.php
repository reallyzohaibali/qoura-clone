<div class="card-quora p-3 mb-3">
    <div class="d-flex justify-content-between align-items-start">
        <div class="d-flex gap-2">
            <img src="<?= htmlspecialchars($item['profile_pic']) ?>" class="profile-pic-md" alt="Author Profile">
            <div>
                <div class="fw-bold fs-7">
                    <?= htmlspecialchars($item['author_name']) ?> 
                    <span class="text-muted-custom fw-normal">· <a href="#" class="text-decoration-none">Follow</a></span>
                </div>
                <div class="text-muted-custom fs-8">
                    <?= htmlspecialchars($item['author_box']) ?> · <?= date('M d', strtotime($item['created_at'])) ?>
                </div>
            </div>
        </div>
        <button class="btn-close fs-8" aria-label="Close"></button>
    </div>

    <?php if ($item['type'] === 'question'): ?>
        <div class="post-title"><?= htmlspecialchars($item['question_title']) ?></div>
    <?php endif; ?>

    <div class="fs-7 text-secondary mb-3 mt-2">
        <?= nl2br(htmlspecialchars($item['content'])) ?>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex gap-1 align-items-center">
            
            <div class="upvote-group d-flex align-items-center bg-light border rounded-pill overflow-hidden">
                <a href="vote.php?item_id=<?= $item['item_id'] ?>&type=<?= $item['type'] ?>&action=up" class="btn btn-sm btn-upvote text-decoration-none d-flex align-items-center px-3 py-1 border-0" style="color: #2e69ff; font-weight: 500; font-size: 13px;">
                    <i class="bi bi-arrow-up-circle-fill me-1"></i> Upvote 
                    <span class="fw-bold text-dark ms-2"><?= intval($item['vote_score']) ?></span>
                </a>
                <a href="vote.php?item_id=<?= $item['item_id'] ?>&type=<?= $item['type'] ?>&action=down" class="btn btn-sm btn-downvote text-decoration-none px-2 py-1 border-start border-0" style="color: #636466;">
                    <i class="bi bi-arrow-down-circle"></i>
                </a>
            </div>
            
            <?php if ($item['type'] === 'question'): ?>
                <button class="btn btn-light btn-sm border rounded-pill px-3 ms-2 text-muted-custom fw-semibold" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#answerForm<?= $item['item_id'] ?>" 
                        style="font-size: 13px;">
                    <i class="bi bi-pencil-square me-1"></i> Answer
                </button>
            <?php endif; ?>
            
            <button class="btn-action-icon ms-2" title="Comment"><i class="bi bi-chat-text"></i></button>
            <button class="btn-action-icon" title="Share"><i class="bi bi-repeat"></i></button>
        </div>
        <button class="btn-action-icon" title="More options"><i class="bi bi-three-dots"></i></button>
    </div>

    <?php if ($item['type'] === 'question'): ?>
        <div class="collapse mt-3" id="answerForm<?= $item['item_id'] ?>">
            <form action="submit-answer.php" method="POST" class="border-top pt-2">
                <input type="hidden" name="question_id" value="<?= $item['item_id'] ?>">
                <div class="mb-2">
                    <textarea class="form-control fs-7" name="answer_content" rows="3" placeholder="Write your answer here..." required></textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3" style="background-color: #b92b27; border: none;">Submit Answer</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

</div>