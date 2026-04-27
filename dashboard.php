<?php
require_once 'db.php';
require_login();

$stmt = $db->prepare('SELECT username, email, created_at FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$initial = mb_strtoupper(mb_substr($user['username'], 0, 1));
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>대시보드 — AIWeb</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">AI<span>Web</span></a>
        <div class="nav-links">
            <a href="dashboard.php">대시보드</a>
            <a href="logout.php">로그아웃</a>
        </div>
    </nav>

    <div class="dashboard">
        <div class="welcome-card">
            <div class="avatar"><?= htmlspecialchars($initial) ?></div>
            <h1>안녕하세요, <?= htmlspecialchars($user['username']) ?>님 👋</h1>
            <p class="greeting">로그인에 성공했습니다. 아래는 회원님의 정보입니다.</p>

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
                    <span class="label">가입일</span>
                    <span class="value"><?= htmlspecialchars($user['created_at']) ?></span>
                </div>
            </div>

            <a href="logout.php" class="btn btn-fill" style="background: linear-gradient(135deg, #667eea, #764ba2); color: #fff;">로그아웃</a>
        </div>
    </div>
</body>
</html>
