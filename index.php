<?php
require_once 'db.php';

$post_error = '';
$post_flash = isset($_SESSION['index_post_flash']) ? $_SESSION['index_post_flash'] : '';
unset($_SESSION['index_post_flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_logged_in()) {
        $_SESSION['flash'] = '로그인 후 게시글을 작성할 수 있습니다.';
        header('Location: login.php');
        exit;
    }

    require_csrf();

    $title = trim(isset($_POST['title']) ? $_POST['title'] : '');
    $content = trim(isset($_POST['content']) ? $_POST['content'] : '');

    if ($title === '' || $content === '') {
        $post_error = '제목과 내용을 모두 입력해 주세요.';
    } elseif (text_length($title) > 140) {
        $post_error = '제목은 140자 이하로 입력해 주세요.';
    } elseif (text_length($content) > 1000) {
        $post_error = '내용은 1000자 이하로 입력해 주세요.';
    } else {
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $stmt = $db->prepare('INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)');
        $stmt->execute([$user_id, $title, $content]);
        $_SESSION['index_post_flash'] = '게시글이 저장되었습니다.';
        header('Location: index.php#posts');
        exit;
    }
}

$stmt = $db->query('
    SELECT posts.id, posts.title, posts.content, posts.created_at, users.username
    FROM posts
    LEFT JOIN users ON users.id = posts.user_id
    ORDER BY posts.created_at DESC, posts.id DESC
');
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>글 저장소</title>
    <script>
        document.documentElement.dataset.theme = localStorage.getItem('theme') === 'dark' ? 'dark' : 'light';
    </script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo">게시판</a>
        <div class="nav-links">
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
        <section class="board-hero">
            <div>
                <h1>게시판</h1>
            </div>
            <div class="board-stat">
                <span><?= count($posts) ?></span>
                <small>게시글</small>
            </div>
        </section>

        <section class="composer-card">
            <div class="section-heading">
                <div>
                    <h2>새 글 작성</h2>
                </div>
            </div>

            <?php if ($post_flash): ?>
                <div class="alert alert-success"><?= htmlspecialchars($post_flash) ?></div>
            <?php endif; ?>
            <?php if ($post_error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($post_error) ?></div>
            <?php endif; ?>

            <?php if (is_logged_in()): ?>
                <form method="POST" action="index.php#posts" class="post-form">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="title">제목</label>
                        <input type="text" id="title" name="title" maxlength="140" placeholder="제목을 입력하세요." value="<?= htmlspecialchars(isset($_POST['title']) ? $_POST['title'] : '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="content">내용</label>
                        <textarea id="content" name="content" rows="7" maxlength="1000" placeholder="내용을 입력하세요." required><?= htmlspecialchars(isset($_POST['content']) ? $_POST['content'] : '') ?></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">게시글 등록</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="login-required">
                    <p>게시글 작성은 로그인한 사용자만 가능합니다.</p>
                    <a href="login.php" class="btn">로그인</a>
                </div>
            <?php endif; ?>
        </section>

        <section class="board-card" id="posts">
            <div class="section-heading">
                <h2>저장된 글</h2>
                <span><?= count($posts) ?>개</span>
            </div>

            <div class="board-list">
                <div class="board-list-head">
                    <span>번호</span>
                    <span>제목</span>
                    <span>작성자</span>
                    <span>작성일</span>
                </div>
                <?php if (count($posts) === 0): ?>
                    <p class="empty-posts">아직 저장된 글이 없습니다.</p>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <a class="board-row" href="post.php?id=<?= urlencode((string) $post['id']) ?>">
                            <span class="post-number">#<?= htmlspecialchars($post['id']) ?></span>
                            <div class="post-main">
                                <h3><?= htmlspecialchars($post['title']) ?></h3>
                                <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                            </div>
                            <span class="post-author"><?= htmlspecialchars(isset($post['username']) ? $post['username'] : '방문자') ?></span>
                            <time><?= htmlspecialchars($post['created_at']) ?></time>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <script src="theme.js"></script>
</body>
</html>
