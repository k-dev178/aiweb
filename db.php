<?php
session_start();

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
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $count = $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($count == 0) {
        $usernames = [
            'samuel', 'yelena', 'scarlett', 'daisy', 'sienna', 'gemma',
            'ruby', 'giselle', 'thea', 'kiera', 'molly', 'duber',
            'amelia', 'gavin', 'glenn', 'silas', 'nigel'
        ];
        $hash = password_hash('wjsansrk', PASSWORD_DEFAULT);
        $insert = $db->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
        foreach ($usernames as $name) {
            $insert->execute([$name, $name . '@aiweb.local', $hash]);
        }
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

function require_logout() {
    if (is_logged_in()) {
        header('Location: dashboard.php');
        exit;
    }
}
