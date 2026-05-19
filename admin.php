<?php
require_once 'db.php';
require_admin();

$flash = isset($_SESSION['admin_flash']) ? $_SESSION['admin_flash'] : '';
$error = isset($_SESSION['admin_error']) ? $_SESSION['admin_error'] : '';
unset($_SESSION['admin_flash'], $_SESSION['admin_error']);

function admin_redirect() {
    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    try {
        if ($action === 'create') {
            $username = trim(isset($_POST['username']) ? $_POST['username'] : '');
            $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $ip_address = trim(isset($_POST['ip_address']) ? $_POST['ip_address'] : '');
            $room_name = trim(isset($_POST['room_name']) ? $_POST['room_name'] : '');
            $room_number = trim(isset($_POST['room_number']) ? $_POST['room_number'] : '');
            $is_admin = isset($_POST['is_admin']) ? 1 : 0;

            if ($username === '') {
                throw new RuntimeException('로그인명을 입력해 주세요.');
            }

            if ($email === '') {
                $email = $username . '@gemma.sm.jj.ac.kr';
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('이메일 형식이 올바르지 않습니다.');
            }

            if ($password === '') {
                $password = 'wjsansrk';
            }

            $stmt = $db->prepare('
                INSERT INTO users (username, email, password, ip_address, room_name, room_number, is_admin)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $username,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $ip_address !== '' ? $ip_address : null,
                $room_name !== '' ? $room_name : null,
                $room_number !== '' ? $room_number : null,
                $is_admin,
            ]);

            $_SESSION['admin_flash'] = '계정이 추가되었습니다.';
            admin_redirect();
        }

        if ($action === 'update') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $username = trim(isset($_POST['username']) ? $_POST['username'] : '');
            $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $ip_address = trim(isset($_POST['ip_address']) ? $_POST['ip_address'] : '');
            $room_name = trim(isset($_POST['room_name']) ? $_POST['room_name'] : '');
            $room_number = trim(isset($_POST['room_number']) ? $_POST['room_number'] : '');
            $is_admin = isset($_POST['is_admin']) ? 1 : 0;

            if (!$id) {
                throw new RuntimeException('수정할 계정을 찾을 수 없습니다.');
            }

            if ($username === '' || $email === '') {
                throw new RuntimeException('로그인명과 이메일을 입력해 주세요.');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('이메일 형식이 올바르지 않습니다.');
            }

            if ($id === (int) $_SESSION['user_id']) {
                $is_admin = 1;
            }

            if ($password !== '') {
                $stmt = $db->prepare('
                    UPDATE users
                    SET username = ?, email = ?, password = ?, ip_address = ?, room_name = ?, room_number = ?, is_admin = ?
                    WHERE id = ?
                ');
                $stmt->execute([
                    $username,
                    $email,
                    password_hash($password, PASSWORD_DEFAULT),
                    $ip_address !== '' ? $ip_address : null,
                    $room_name !== '' ? $room_name : null,
                    $room_number !== '' ? $room_number : null,
                    $is_admin,
                    $id,
                ]);
            } else {
                $stmt = $db->prepare('
                    UPDATE users
                    SET username = ?, email = ?, ip_address = ?, room_name = ?, room_number = ?, is_admin = ?
                    WHERE id = ?
                ');
                $stmt->execute([
                    $username,
                    $email,
                    $ip_address !== '' ? $ip_address : null,
                    $room_name !== '' ? $room_name : null,
                    $room_number !== '' ? $room_number : null,
                    $is_admin,
                    $id,
                ]);
            }

            if ($id === (int) $_SESSION['user_id']) {
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['is_admin'] = true;
            }

            $_SESSION['admin_flash'] = '계정 정보가 수정되었습니다.';
            admin_redirect();
        }

        if ($action === 'delete') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

            if (!$id) {
                throw new RuntimeException('삭제할 계정을 찾을 수 없습니다.');
            }

            if ($id === (int) $_SESSION['user_id']) {
                throw new RuntimeException('현재 로그인한 관리자 계정은 삭제할 수 없습니다.');
            }

            $stmt = $db->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$id]);

            $_SESSION['admin_flash'] = '계정이 삭제되었습니다.';
            admin_redirect();
        }
    } catch (PDOException $e) {
        $_SESSION['admin_error'] = 'DB 오류: ' . $e->getMessage();
        admin_redirect();
    } catch (RuntimeException $e) {
        $_SESSION['admin_error'] = $e->getMessage();
        admin_redirect();
    }
}

$users = $db->query('
    SELECT id, username, email, ip_address, room_name, room_number, is_admin, created_at
    FROM users
    ORDER BY username ASC
')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자 - 글 저장소</title>
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
            <a href="dashboard.php"><?= htmlspecialchars(isset($_SESSION['username']) ? $_SESSION['username'] : '계정') ?></a>
            <a href="logout.php">로그아웃</a>
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
                <h1>관리자</h1>
            </div>
            <div class="board-stat">
                <span><?= count($users) ?></span>
                <small>계정</small>
            </div>
        </section>

        <?php if ($flash): ?>
            <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <section class="composer-card">
            <div class="section-heading">
                <h2>계정 추가</h2>
            </div>
            <form method="POST" action="admin.php" class="admin-create-form">
                <input type="hidden" name="action" value="create">
                <input type="text" name="username" placeholder="로그인명" required>
                <input type="email" name="email" placeholder="이메일 비우면 @gemma.sm.jj.ac.kr">
                <input type="password" name="password" placeholder="비밀번호 비우면 wjsansrk">
                <input type="text" name="ip_address" placeholder="IP">
                <input type="text" name="room_name" placeholder="룹명">
                <input type="text" name="room_number" placeholder="룹번">
                <label class="admin-check">
                    <input type="checkbox" name="is_admin" value="1">
                    관리자
                </label>
                <button type="submit" class="btn-submit">추가</button>
            </form>
        </section>

        <section class="board-card">
            <div class="section-heading">
                <h2>계정 관리</h2>
                <span><?= count($users) ?>개</span>
            </div>
            <div class="admin-list">
                <?php foreach ($users as $user): ?>
                    <div class="admin-row">
                        <form method="POST" action="admin.php" class="admin-edit-form">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            <input type="password" name="password" placeholder="새 비밀번호">
                            <input type="text" name="ip_address" value="<?= htmlspecialchars(isset($user['ip_address']) ? $user['ip_address'] : '') ?>" placeholder="IP">
                            <input type="text" name="room_name" value="<?= htmlspecialchars(isset($user['room_name']) ? $user['room_name'] : '') ?>" placeholder="룹명">
                            <input type="text" name="room_number" value="<?= htmlspecialchars(isset($user['room_number']) ? $user['room_number'] : '') ?>" placeholder="룹번">
                            <label class="admin-check">
                                <input type="checkbox" name="is_admin" value="1" <?= $user['is_admin'] ? 'checked' : '' ?> <?= (int) $user['id'] === (int) $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                관리자
                            </label>
                            <button type="submit" class="btn-submit">저장</button>
                        </form>
                        <form method="POST" action="admin.php" onsubmit="return confirm('이 계정을 삭제할까요?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                            <button type="submit" class="btn btn-danger" <?= (int) $user['id'] === (int) $_SESSION['user_id'] ? 'disabled' : '' ?>>삭제</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
    <script src="theme.js"></script>
</body>
</html>
