<?php
require_once 'db.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('허용되지 않은 요청입니다.');
}

require_csrf();

$post_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$post_id) {
    http_response_code(404);
    die('게시글을 찾을 수 없습니다.');
}

if (is_admin()) {
    $stmt = $db->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->execute([$post_id]);
} else {
    $stmt = $db->prepare('DELETE FROM posts WHERE id = ? AND user_id = ?');
    $stmt->execute([$post_id, $_SESSION['user_id']]);
}

if ($stmt->rowCount() === 0) {
    http_response_code(403);
    die('삭제할 수 없는 게시글입니다.');
}

header('Location: index.php#posts');
exit;
