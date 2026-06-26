<?php
/**
 * Feed Service Module - Clean Split Count
 */

function getHomeFeed($pdo, $topicSlug = null) {
    $feedItems = [];
    $whereClause = "";
    $params = [];

    if ($topicSlug !== null) {
        $whereClause = " WHERE t.slug = :topic_slug ";
        $params[':topic_slug'] = $topicSlug;
    }

    try {
        // 1. Fetch Posts with independent count states
        $postSql = "SELECT 
                        p.id as item_id,
                        'post' as type,
                        p.content as content,
                        p.created_at,
                        u.name as author_name,
                        u.bio as author_box,
                        u.profile_pic,
                        t.name as topic_name,
                        (SELECT COUNT(*) FROM upvotes WHERE item_id = p.id AND item_type = 'post') as vote_score
                    FROM posts p
                    JOIN users u ON p.user_id = u.id
                    JOIN topics t ON p.topic_id = t.id
                    {$whereClause}
                    GROUP BY p.id, u.name, u.bio, u.profile_pic, t.name, p.content, p.created_at";
        
        $postStmt = $pdo->prepare($postSql);
        $postStmt->execute($params);
        $posts = $postStmt->fetchAll();

        // 2. Fetch Questions with independent count states
        $questionSql = "SELECT 
                            q.id as item_id,
                            'question' as type,
                            q.title as question_title,
                            COALESCE(a.content, 'No answers yet. Be the first to answer!') as content,
                            q.created_at,
                            u.name as author_name,
                            u.bio as author_box,
                            u.profile_pic,
                            t.name as topic_name,
                            (SELECT COUNT(*) FROM upvotes WHERE item_id = q.id AND item_type = 'question') as vote_score
                        FROM questions q
                        JOIN users u ON q.user_id = u.id
                        JOIN topics t ON q.topic_id = t.id
                        LEFT JOIN answers a ON a.question_id = q.id
                        {$whereClause}
                        GROUP BY q.id, q.title, a.content, u.name, u.bio, u.profile_pic, t.name, q.created_at";

        $questionStmt = $pdo->prepare($questionSql);
        $questionStmt->execute($params);
        $questions = $questionStmt->fetchAll();

        // 3. Combine and Sort
        $feedItems = array_merge($posts, $questions);
        usort($feedItems, function($a, $b) {
            return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        });

    } catch (PDOException $e) {
        error_log("Error in getHomeFeed: " . $e->getMessage());
    }

    return $feedItems;
}