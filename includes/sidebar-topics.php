<button class="btn btn-light border-0 text-start text-muted-custom fs-7 py-2 bg-body-secondary bg-opacity-50 mb-1 w-100" 
        data-bs-toggle="modal" 
        data-bs-target="#createTopicModal" 
        style="border-radius: 4px;">
    <i class="bi bi-plus-lg me-2"></i>Create Topic
</button>

<div class="d-flex flex-column gap-1 mb-4">
    <?php if (empty($topics)): ?>
        <p class="text-muted fs-7 ps-2">No topics found.</p>
    <?php else: ?>
        <?php foreach ($topics as $topic): ?>
            <a href="?topic=<?= urlencode($topic['slug']) ?>" class="sidebar-link">
                <div class="position-relative">
                    <img src="<?= htmlspecialchars($topic['icon_url']) ?>" class="sidebar-icon-wrapper" alt="<?= htmlspecialchars($topic['name']) ?>">
                </div>
                <span class="text-truncate"><?= htmlspecialchars($topic['name']) ?></span>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<hr class="text-muted opacity-25">

<div class="lh-sm">
    <ul class="list-unstyled d-flex flex-wrap gap-2 ps-2">
        <li><a href="#" class="footer-link">About</a></li>
        <li><a href="#" class="footer-link">Careers</a></li>
        <li><a href="#" class="footer-link">Terms</a></li>
        <li><a href="#" class="footer-link">Privacy</a></li>
    </ul>
</div>

<div class="modal fade" id="createTopicModal" tabindex="-1" aria-labelledby="createTopicModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 8px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark fs-6" id="createTopicModalLabel">Create a New Topic</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="submit-topic.php" method="POST">
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label for="topic_name" class="form-label fs-7 fw-bold text-muted">Topic Name</label>
                        <input type="text" class="form-control fs-7" name="topic_name" id="topic_name" placeholder="e.g., Artificial Intelligence, Algebra" required>
                    </div>

                    <div class="mb-2">
                        <label for="icon_url" class="form-label fs-7 fw-bold text-muted">Icon Image URL (Optional)</label>
                        <input type="url" class="form-control fs-7" name="icon_url" id="icon_url" placeholder="https://example.com/image.jpg">
                    </div>
                </div>
                
                <div class="modal-footer border-top d-flex justify-content-end py-2">
                    <button type="button" class="btn btn-light rounded-pill fs-7 px-3 text-muted border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill fs-7 px-4" style="background-color: #b92b27; border: none;">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>