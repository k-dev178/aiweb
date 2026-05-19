<?php
require_once 'db.php';
require_login();

$post_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$post_id) {
    http_response_code(404);
    die('게시글을 찾을 수 없습니다.');
}

$stmt = $db->prepare('SELECT id, user_id, title, content FROM posts WHERE id = ?');
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    http_response_code(404);
    die('게시글을 찾을 수 없습니다.');
}

if ((int) $post['user_id'] !== (int) $_SESSION['user_id']) {
    http_response_code(403);
    die('본인이 작성한 게시글만 수정할 수 있습니다.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim(isset($_POST['title']) ? $_POST['title'] : '');
    $content = trim(isset($_POST['content']) ? $_POST['content'] : '');

    if ($title === '' || $content === '') {
        $error = '제목과 내용을 모두 입력해 주세요.';
    } elseif (text_length($title) > 140) {
        $error = '제목은 140자 이하로 입력해 주세요.';
    } elseif (text_length($content) > 1000) {
        $error = '내용은 1000자 이하로 입력해 주세요.';
    } else {
        $stmt = $db->prepare('UPDATE posts SET title = ?, content = ? WHERE id = ? AND user_id = ?');
        $stmt->execute([$title, $content, $post_id, $_SESSION['user_id']]);

        if ($stmt->rowCount() === 0) {
            http_response_code(403);
            die('본인이 작성한 게시글만 수정할 수 있습니다.');
        }

        header('Location: post.php?id=' . urlencode((string) $post_id));
        exit;
    }

    $post['title'] = $title;
    $post['content'] = $content;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시글 수정 - 글 저장소</title>
    <script>
        document.documentElement.dataset.theme = localStorage.getItem('theme') === 'dark' ? 'dark' : 'light';
    </script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo">게시판</a>
        <div class="nav-links">
            <a href="post.php?id=<?= urlencode((string) $post_id) ?>">상세</a>
            <a href="logout.php">로그아웃</a>
            <button type="button" class="theme-toggle" id="themeToggle" aria-label="다크 모드로 전환" aria-pressed="false">
                <span class="theme-toggle-track">
                    <span class="theme-toggle-thumb"></span>
                </span>
            </button>
        </div>
    </header>

    <main class="container">
        <section class="composer-card">
            <div class="section-heading">
                <h2>게시글 수정</h2>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="edit_post.php" class="post-form">
                <input type="hidden" name="id" value="<?= htmlspecialchars($post['id']) ?>">
                <div class="form-group">
                    <label for="title">제목</label>
                    <input type="text" id="title" name="title" maxlength="140" value="<?= htmlspecialchars($post['title']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="content">내용</label>
                    <textarea id="content" name="content" rows="7" maxlength="1000" required><?= htmlspecialchars($post['content']) ?></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-submit">수정 저장</button>
                </div>
            </form>
        </section>
    </main>
    <script src="theme.js"></script>
</body>
</html>
