<div class="modal fade" id="composerModal" tabindex="-1" aria-labelledby="composerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 8px;">
            
            <div class="modal-header border-bottom-0 pb-0">
                <ul class="nav nav-tabs w-100 border-bottom-0" id="composerTabs" role="tablist">
                    <li class="nav-item flex-fill text-center" role="presentation">
                        <button class="nav-link active fw-bold w-100 border-0 text-secondary" id="ask-tab" data-bs-toggle="tab" data-bs-target="#tab-pane" type="button" role="tab" onclick="setSubmissionType('question')">Add Question</button>
                    </li>
                    <li class="nav-item flex-fill text-center" role="presentation">
                        <button class="nav-link fw-bold w-100 border-0 text-secondary" id="post-tab" data-bs-toggle="tab" data-bs-target="#tab-pane" type="button" role="tab" onclick="setSubmissionType('post')">Create Post</button>
                    </li>
                </ul>
                <button type="button" class="btn-close" data-bs-shadow="none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="submit-content.php" method="POST">
                <div class="modal-body pt-3">
                    
                    <input type="hidden" name="content_type" id="content_type" value="question">
                    
                    <input type="hidden" name="user_id" value="1">

                    <div class="mb-3">
                        <label for="topic_id" class="form-label fs-7 fw-bold text-muted">Select a Topic</label>
                        <select class="form-select fs-7" name="topic_id" id="topic_id" required>
                            <option value="" disabled selected>Choose the most relevant category...</option>
                            <?php foreach ($topics as $topic): ?>
                                <option value="<?= $topic['id'] ?>"><?= htmlspecialchars($topic['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-2">
                        <textarea class="form-control border-0 ps-0" name="content_body" id="content_body" rows="4" placeholder="Start your question with 'What', 'How', 'Why', etc..." style="resize: none; font-size: 15px;" required></textarea>
                    </div>

                </div>
                
                <div class="modal-footer border-top d-flex justify-content-end py-2">
                    <button type="button" class="btn btn-light rounded-pill fs-7 px-3 text-muted border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill fs-7 px-4" style="background-color: #b92b27; border: none;">Add content</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
function setSubmissionType(type) {
    document.getElementById('content_type').value = type;
    const inputTextArea = document.getElementById('content_body');
    
    if (type === 'question') {
        inputTextArea.placeholder = "Start your question with 'What', 'How', 'Why', etc...";
    } else {
        inputTextArea.placeholder = "What do you want to share?";
    }
}
</script>