<?php
require_once 'db.php';
require_logout();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(isset($_POST['username']) ? $_POST['username'] : '');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

    if ($username === '' || $email === '' || $password === '') {
        $error = '모든 항목을 입력해 주세요.';
    } elseif (strlen($username) < 3) {
        $error = '아이디는 3자 이상이어야 합니다.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '올바른 이메일 형식이 아닙니다.';
    } elseif (strlen($password) < 6) {
        $error = '비밀번호는 6자 이상이어야 합니다.';
    } elseif ($password !== $password_confirm) {
        $error = '비밀번호가 일치하지 않습니다.';
    } else {
        try {
            $stmt = $db->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $error = '이미 사용 중인 아이디 또는 이메일입니다.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
                $stmt->execute([$username, $email, $hash]);
                $_SESSION['flash'] = '회원가입이 완료되었습니다. 로그인해 주세요.';
                header('Location: login.php');
                exit;
            }
        } catch (PDOException $e) {
            $error = '오류가 발생했습니다: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입 - 글 저장소</title>
    <script>
        document.documentElement.dataset.theme = localStorage.getItem('theme') === 'dark' ? 'dark' : 'light';
    </script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo">글 저장소</a>
        <div class="nav-links">
            <a href="login.php">로그인</a>
            <button type="button" class="theme-toggle" id="themeToggle" aria-label="다크 모드로 전환" aria-pressed="false">
                <span class="theme-toggle-track">
                    <span class="theme-toggle-thumb"></span>
                </span>
            </button>
        </div>
    </header>

    <div class="auth-wrapper">
        <div class="auth-card">
            <h2>회원가입</h2>
            <p class="auth-subtitle">새 계정을 만들어 시작해 보세요.</p>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="signup.php">
                <div class="form-group">
                    <label for="username">아이디</label>
                    <input type="text" id="username" name="username" placeholder="3자 이상" value="<?= htmlspecialchars(isset($_POST['username']) ? $_POST['username'] : '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">이메일</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" value="<?= htmlspecialchars(isset($_POST['email']) ? $_POST['email'] : '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">비밀번호</label>
                    <input type="password" id="password" name="password" placeholder="6자 이상" required>
                </div>
                <div class="form-group">
                    <label for="password_confirm">비밀번호 확인</label>
                    <input type="password" id="password_confirm" name="password_confirm" placeholder="비밀번호를 다시 입력" required>
                </div>
                <button type="submit" class="btn-submit">계정 만들기</button>
            </form>

            <div class="divider">또는</div>

            <p class="auth-footer">
                이미 계정이 있으신가요? <a href="login.php">로그인</a>
            </p>
        </div>
    </div>
    <script src="theme.js"></script>
</body>
</html>
