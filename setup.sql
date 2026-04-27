-- MySQL 초기 설정. root 권한으로 한 번만 실행.
-- 사용법: mysql -u root < setup.sql

CREATE DATABASE IF NOT EXISTS aiweb
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'aiweb_user'@'localhost' IDENTIFIED BY 'wjsansrk';
GRANT ALL PRIVILEGES ON aiweb.* TO 'aiweb_user'@'localhost';
FLUSH PRIVILEGES;
