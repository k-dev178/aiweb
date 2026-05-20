<?php
require_once 'db.php';

$post_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$post_id) {
    http_response_code(404);
    die('게시글을 찾을 수 없습니다.');
}

$stmt = $db->prepare('
    SELECT posts.id, posts.user_id, posts.title, posts.content, posts.created_at, users.username
    FROM posts
    LEFT JOIN users ON users.id = posts.user_id
    WHERE posts.id = ?
');
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    http_response_code(404);
    die('게시글을 찾을 수 없습니다.');
}

$can_edit = is_logged_in() && (int) $post['user_id'] === (int) $_SESSION['user_id'];
$can_delete = $can_edit || is_admin();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> - 글 저장소</title>
    <script>
        document.documentElement.dataset.theme = localStorage.getItem('theme') === 'dark' ? 'dark' : 'light';
    </script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo">게시판</a>
        <div class="nav-links">
            <a href="index.php">목록</a>
            <?php if (is_logged_in()): ?>
                <?php if (is_admin()): ?>
                    <a href="admin.php">관리자</a>
                <?php endif; ?>
                <a href="dashboard.php"><?= htmlspecialchars(isset($_SESSION['username']) ? $_SESSION['username'] : '계정') ?></a>
                <form method="POST" action="logout.php" class="nav-form">
                    <?= csrf_field() ?>
                    <button type="submit" class="nav-link-button">로그아웃</button>
                </form>
            <?php else: ?>
                <a href="login.php">로그인</a>
            <?php endif; ?>
            <button type="button" class="theme-toggle" id="themeToggle" aria-label="다크 모드로 전환" aria-pressed="false">
                <span class="theme-toggle-track">
                    <span class="theme-toggle-thumb"></span>
                </span>
            </button>
        </div>
    </header>

    <main class="container">
        <article class="post-detail">
            <a href="index.php#posts" class="back-link">‹ 목록으로</a>
            <header class="post-detail-header">
                <p class="post-detail-kicker">게시글 #<?= htmlspecialchars($post['id']) ?></p>
                <h1><?= htmlspecialchars($post['title']) ?></h1>
                <div class="post-detail-meta">
                    <span><?= htmlspecialchars(isset($post['username']) ? $post['username'] : '방문자') ?></span>
                    <time><?= htmlspecialchars($post['created_at']) ?></time>
                </div>
                <?php if ($can_edit || $can_delete): ?>
                    <div class="post-detail-actions">
                        <?php if ($can_edit): ?>
                            <a href="edit_post.php?id=<?= urlencode((string) $post['id']) ?>" class="btn">수정</a>
                        <?php endif; ?>
                        <?php if ($can_delete): ?>
                            <form method="POST" action="delete_post.php" onsubmit="return confirm('게시글을 삭제할까요?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= htmlspecialchars($post['id']) ?>">
                                <button type="submit" class="btn btn-danger">삭제</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </header>
            <div class="post-detail-body">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </div>
        </article>
    </main>
    <script src="theme.js"></script>
</body>
</html>
