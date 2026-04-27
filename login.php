<?php
require_once 'db.php';
require_logout();

$error = '';
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = '아이디와 비밀번호를 입력해 주세요.';
    } else {
        $stmt = $db->prepare('SELECT * FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = '아이디 또는 비밀번호가 올바르지 않습니다.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인 — AIWeb</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">AI<span>Web</span></a>
        <div class="nav-links">
            <a href="signup.php" class="btn-primary">회원가입</a>
        </div>
    </nav>

    <div class="auth-wrapper">
        <div class="auth-card">
            <h2>로그인</h2>
            <p class="auth-subtitle">다시 오신 것을 환영합니다.</p>

            <?php if ($flash): ?>
                <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="username">아이디 또는 이메일</label>
                    <input type="text" id="username" name="username" placeholder="아이디 또는 이메일" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">비밀번호</label>
                    <input type="password" id="password" name="password" placeholder="비밀번호" required>
                </div>
                <button type="submit" class="btn-submit">로그인</button>
            </form>

            <div class="divider">또는</div>

            <p class="auth-footer">
                계정이 없으신가요? <a href="signup.php">회원가입</a>
            </p>
        </div>
    </div>
</body>
</html>
