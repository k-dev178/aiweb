<?php
require_once 'db.php';
require_login();

$stmt = $db->prepare('SELECT username, email, ip_address, room_name, room_number, created_at FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>계정 정보 - 글 저장소</title>
    <script>
        document.documentElement.dataset.theme = localStorage.getItem('theme') === 'dark' ? 'dark' : 'light';
    </script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo">글 저장소</a>
        <div class="nav-links">
            <?php if (is_admin()): ?>
                <a href="admin.php">관리자</a>
            <?php endif; ?>
            <a href="index.php">글쓰기</a>
            <a href="logout.php">로그아웃</a>
            <button type="button" class="theme-toggle" id="themeToggle" aria-label="다크 모드로 전환" aria-pressed="false">
                <span class="theme-toggle-track">
                    <span class="theme-toggle-thumb"></span>
                </span>
            </button>
        </div>
    </header>

    <main class="dashboard">
        <div class="welcome-card">
            <h1>계정 정보</h1>

            <div class="user-info">
                <div class="user-info-row">
                    <span class="label">아이디</span>
                    <span class="value"><?= htmlspecialchars($user['username']) ?></span>
                </div>
                <div class="user-info-row">
                    <span class="label">이메일</span>
                    <span class="value"><?= htmlspecialchars($user['email']) ?></span>
                </div>
                <div class="user-info-row">
                    <span class="label">IP</span>
                    <span class="value"><?= htmlspecialchars(isset($user['ip_address']) ? $user['ip_address'] : '-') ?></span>
                </div>
                <div class="user-info-row">
                    <span class="label">룹명</span>
                    <span class="value"><?= htmlspecialchars(isset($user['room_name']) ? $user['room_name'] : '-') ?></span>
                </div>
                <div class="user-info-row">
                    <span class="label">룹번</span>
                    <span class="value"><?= htmlspecialchars(isset($user['room_number']) ? $user['room_number'] : '-') ?></span>
                </div>
                <div class="user-info-row">
                    <span class="label">가입일</span>
                    <span class="value"><?= htmlspecialchars($user['created_at']) ?></span>
                </div>
            </div>

            <a href="index.php" class="btn">글쓰기 화면으로</a>
        </div>
    </main>
    <script src="theme.js"></script>
</body>
</html>
