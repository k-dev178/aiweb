<?php
session_start();

if (!defined('PASSWORD_BCRYPT')) {
    define('PASSWORD_BCRYPT', 1);
}

if (!defined('PASSWORD_DEFAULT')) {
    define('PASSWORD_DEFAULT', PASSWORD_BCRYPT);
}

if (!function_exists('hash_equals')) {
    function hash_equals($known_string, $user_string) {
        if (!is_string($known_string) || !is_string($user_string)) {
            return false;
        }

        if (strlen($known_string) !== strlen($user_string)) {
            return false;
        }

        $result = 0;
        for ($i = 0; $i < strlen($known_string); $i++) {
            $result |= ord($known_string[$i]) ^ ord($user_string[$i]);
        }

        return $result === 0;
    }
}

if (!function_exists('password_hash')) {
    function password_hash($password, $algo, array $options = array()) {
        $cost = isset($options['cost']) ? (int) $options['cost'] : 10;
        $cost = max(4, min(31, $cost));

        if (function_exists('openssl_random_pseudo_bytes')) {
            $rawSalt = openssl_random_pseudo_bytes(16);
        } else {
            $rawSalt = uniqid(mt_rand(), true);
        }

        if ($rawSalt === false || $rawSalt === '') {
            $rawSalt = uniqid(mt_rand(), true);
        }

        $salt = substr(str_replace('=', '', strtr(base64_encode($rawSalt), '+', '.')), 0, 22);
        $hash = crypt($password, sprintf('$2y$%02d$', $cost) . $salt);

        return strlen($hash) >= 60 ? $hash : false;
    }
}

if (!function_exists('password_verify')) {
    function password_verify($password, $hash) {
        if (!is_string($hash) || $hash === '') {
            return false;
        }

        return hash_equals($hash, crypt($password, $hash));
    }
}

function text_length($value) {
    if (function_exists('mb_strlen')) {
        return mb_strlen($value, 'UTF-8');
    }

    return strlen($value);
}

$db_host = 'localhost';
$db_name = 'aiweb';
$db_user = 'aiweb_user';
$db_pass = 'wjsansrk';

try {
    $db = new PDO(
        "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        ip_address VARCHAR(20) NULL,
        room_name VARCHAR(20) NULL,
        room_number VARCHAR(20) NULL,
        is_admin TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $userColumns = [
        'ip_address' => "ALTER TABLE users ADD ip_address VARCHAR(20) NULL AFTER password",
        'room_name' => "ALTER TABLE users ADD room_name VARCHAR(20) NULL AFTER ip_address",
        'room_number' => "ALTER TABLE users ADD room_number VARCHAR(20) NULL AFTER room_name",
        'is_admin' => "ALTER TABLE users ADD is_admin TINYINT(1) NOT NULL DEFAULT 0 AFTER room_number",
    ];

    foreach ($userColumns as $column => $sql) {
        $existingColumn = $db->query("SHOW COLUMNS FROM users LIKE " . $db->quote($column))->fetch();
        if (!$existingColumn) {
            $db->exec($sql);
        }
    }

    $db->exec("CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        title VARCHAR(140) NOT NULL DEFAULT '제목 없음',
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_posts_created_at (created_at),
        CONSTRAINT fk_posts_user
            FOREIGN KEY (user_id) REFERENCES users(id)
            ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $postUserIdColumn = $db->query("SHOW COLUMNS FROM posts LIKE 'user_id'")->fetch();
    if ($postUserIdColumn && strtoupper($postUserIdColumn['Null']) !== 'YES') {
        $db->exec('ALTER TABLE posts MODIFY user_id INT NULL');
    }

    $postTitleColumn = $db->query("SHOW COLUMNS FROM posts LIKE 'title'")->fetch();
    if (!$postTitleColumn) {
        $db->exec("ALTER TABLE posts ADD title VARCHAR(140) NOT NULL DEFAULT '제목 없음' AFTER user_id");
    }

    $defaultUsers = [
        ['samuel', null, 'kt, skt, lgt', '1000, 1001, 1002'],
        ['yelena', '.155', 'kt', '1000'],
        ['scarlett', '.160', 'skt', '1001'],
        ['daisy', '.140', 'lgt', '1002'],
        ['sienna', '.143', 'lgt', '1002'],
        ['yummer', '.138', 'skt', '1001'],
        ['gemma', '.149', 'kt', '1000'],
        ['ruby', '.158', 'kt', '1000'],
        ['giselle', '.170', 'lgt', '1002'],
        ['thea', '.150', 'skt', '1001'],
        ['kiera', '.145', 'lgt', '1002'],
        ['molly', '.154', 'lgt', '1002'],
        ['duber', '.151', 'kt', '1000'],
        ['amelia', '.153', 'skt', '1001'],
        ['gavin', '.167', 'kt', '1000'],
        ['glenn', '.146', 'lgt', '1002'],
        ['silas', '.147', 'kt', '1000'],
        ['nigel', '.148', 'lgt', '1002'],
    ];

    $hash = password_hash('wjsansrk', PASSWORD_DEFAULT);
    $upsert = $db->prepare('
        INSERT INTO users (username, email, password, ip_address, room_name, room_number, is_admin)
        VALUES (?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            email = VALUES(email),
            password = VALUES(password),
            ip_address = VALUES(ip_address),
            room_name = VALUES(room_name),
            room_number = VALUES(room_number),
            is_admin = VALUES(is_admin)
    ');

    $emailDomain = 'gemma.sm.jj.ac.kr';

    foreach ($defaultUsers as $defaultUser) {
        $username = $defaultUser[0];
        $ipAddress = $defaultUser[1];
        $roomName = $defaultUser[2];
        $roomNumber = $defaultUser[3];
        $isAdmin = $username === 'gemma' ? 1 : 0;
        $upsert->execute([
            $username,
            $username . '@' . $emailDomain,
            $hash,
            $ipAddress,
            $roomName,
            $roomNumber,
            $isAdmin,
        ]);
    }
} catch (PDOException $e) {
    die('DB 연결 실패: ' . $e->getMessage());
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function is_admin() {
    if (!is_logged_in()) {
        return false;
    }

    if (isset($_SESSION['is_admin'])) {
        return (bool) $_SESSION['is_admin'];
    }

    global $db;
    $stmt = $db->prepare('SELECT is_admin FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $_SESSION['is_admin'] = (bool) $stmt->fetchColumn();

    return (bool) $_SESSION['is_admin'];
}

function require_admin() {
    require_login();

    if (!is_admin()) {
        http_response_code(403);
        die('관리자만 접근할 수 있습니다.');
    }
}

function require_logout() {
    if (is_logged_in()) {
        header('Location: dashboard.php');
        exit;
    }
}
