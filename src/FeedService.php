<?php
require_once 'auth.php';

function getHomeFeed($pdo, $topicSlug = null, $searchQuery = null) {
    $feedItems = [];
    $params = [];
    $postWhere = [];
    $questWhere = [];

    if ($topicSlug !== null) {
        $postWhere[] = "t.slug = :topic_slug";
        $questWhere[] = "t.slug = :topic_slug";
        $params[':topic_slug'] = $topicSlug;
    }

    if ($searchQuery !== null && trim($searchQuery) !== '') {
        $term = "%" . trim($searchQuery) . "%";
        $postWhere[] = "(p.content LIKE :search OR u.name LIKE :search)";
        $questWhere[] = "(q.title LIKE :search OR u.name LIKE :search)";
        $params[':search'] = $term;
    }

    $postWhereStr = !empty($postWhere) ? " WHERE " . implode(" AND ", $postWhere) : "";
    $questWhereStr = !empty($questWhere) ? " WHERE " . implode(" AND ", $questWhere) : "";

    try {
        // Posts Query
        $postSql = "SELECT p.id as item_id, 'post' as type, p.content, p.created_at, p.user_id as author_id,
                           u.name as author_name, u.bio as author_box, u.profile_pic, t.name as topic_name,
                           (SELECT COUNT(*) FROM upvotes WHERE item_id = p.id AND item_type = 'post') as vote_score
                    FROM posts p JOIN users u ON p.user_id = u.id JOIN topics t ON p.topic_id = t.id
                    {$postWhereStr}";
        $stmt = $pdo->prepare($postSql); $stmt->execute($params);
        $posts = $stmt->fetchAll();

        // Questions Query
        $questSql = "SELECT q.id as item_id, 'question' as type, q.title as question_title, q.created_at, q.user_id as author_id,
                            u.name as author_name, u.bio as author_box, u.profile_pic, t.name as topic_name,
                            (SELECT COUNT(*) FROM upvotes WHERE item_id = q.id AND item_type = 'question') as vote_score
                     FROM questions q JOIN users u ON q.user_id = u.id JOIN topics t ON q.topic_id = t.id
                     {$questWhereStr}";
        $stmt = $pdo->prepare($questSql); $stmt->execute($params);
        $questions = $stmt->fetchAll();

        // Hydrate answers explicitly inside the questions loop array block
        foreach ($questions as &$q) {
            $ansSql = "SELECT a.id, a.content, a.created_at, u.name as author_name, u.bio as author_box, u.profile_pic 
                       FROM answers a JOIN users u ON a.user_id = u.id 
                       WHERE a.question_id = ? ORDER BY a.created_at ASC";
            $ansStmt = $pdo->prepare($ansSql); $ansStmt->execute([$q['item_id']]);
            $q['answers'] = $ansStmt->fetchAll();
        }

        $feedItems = array_merge($posts, $questions);
        usort($feedItems, function($a, $b) { return strtotime($b['created_at']) <=> strtotime($a['created_at']); });

    } catch (PDOException $e) { error_log($e->getMessage()); }
    return $feedItems;
}