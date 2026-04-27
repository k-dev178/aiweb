<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIWeb — 간단한 인증 사이트</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">AI<span>Web</span></a>
        <div class="nav-links">
            <?php if (is_logged_in()): ?>
                <a href="dashboard.php">대시보드</a>
                <a href="logout.php">로그아웃</a>
            <?php else: ?>
                <a href="login.php">로그인</a>
                <a href="signup.php" class="btn-primary">회원가입</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <section class="hero">
            <h1>리눅스 위에서 동작하는<br><span class="accent">나만의 웹 서비스</span></h1>
            <p>Apache와 PHP, SQLite로 구성된 가벼운 인증 시스템.<br>회원가입과 로그인부터 시작해 보세요.</p>
            <div class="hero-actions">
                <?php if (is_logged_in()): ?>
                    <a href="dashboard.php" class="btn btn-fill">내 대시보드 보기</a>
                <?php else: ?>
                    <a href="signup.php" class="btn btn-fill">시작하기</a>
                    <a href="login.php" class="btn btn-ghost">로그인</a>
                <?php endif; ?>
            </div>
        </section>

        <section class="features">
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>빠르고 가벼운</h3>
                <p>외부 의존성 없이 PHP와 SQLite만으로 동작하는 단순한 구조.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔐</div>
                <h3>안전한 인증</h3>
                <p>비밀번호는 password_hash로 안전하게 저장되며 세션으로 로그인 상태를 유지합니다.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🐧</div>
                <h3>리눅스 친화적</h3>
                <p>Apache + PHP가 설치된 어떤 리눅스 서버에든 폴더를 그대로 올리면 끝.</p>
            </div>
        </section>
    </div>
</body>
</html>
