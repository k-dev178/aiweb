-- MySQL 초기 설정. root 권한으로 한 번만 실행.
-- 사용법: mysql -u root < setup.sql

CREATE DATABASE IF NOT EXISTS aiweb
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON aiweb.* TO 'aiweb_user'@'localhost' IDENTIFIED BY 'wjsansrk';
FLUSH PRIVILEGES;

USE aiweb;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    ip_address VARCHAR(20) NULL,
    room_name VARCHAR(20) NULL,
    room_number VARCHAR(20) NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (username, email, password, ip_address, room_name, room_number, is_admin) VALUES
    ('samuel', 'samuel@gemma.sm.jj.ac.kr', '$2y$12$Kv6KYDvBFh.EOthxjfIy8eYrPYV7e1cRyUJ4Dpy3yxuqaqMBP548y', NULL, 'kt, skt, lgt', '1000, 1001, 1002', 0),
    ('yelena', 'yelena@gemma.sm.jj.ac.kr', '$2y$12$Kv6KYDvBFh.EOthxjfIy8eYrPYV7e1cRyUJ4Dpy3yxuqaqMBP548y', '.155', 'kt', '1000', 0),
    ('scarlett', 'scarlett@gemma.sm.jj.ac.kr', '$2y$12$Kv6KYDvBFh.EOthxjfIy8eYrPYV7e1cRyUJ4Dpy3yxuqaqMBP548y', '.160', 'skt', '1001', 0),
    ('daisy', 'daisy@gemma.sm.jj.ac.kr', '$2y$12$Kv6KYDvBFh.EOthxjfIy8eYrPYV7e1cRyUJ4Dpy3yxuqaqMBP548y', '.140', 'lgt', '1002', 0),
    ('sienna', 'sienna@gemma.sm.jj.ac.kr', '$2y$12$Kv6KYDvBFh.EOthxjfIy8eYrPYV7e1cRyUJ4Dpy3yxuqaqMBP548y', '.143', 'lgt', '1002', 0),
    ('yummer', 'yummer@gemma.sm.jj.ac.kr', '$2y$12$Kv6KYDvBFh.EOthxjfIy8eYrPYV7e1cRyUJ4Dpy3yxuqaqMBP548y', '.138', 'skt', '1001', 0),
    ('gemma', 'gemma@gemma.sm.jj.ac.kr', '$2y$12$Kv6KYDvBFh.EOthxjfIy8eYrPYV7e1cRyUJ4Dpy3yxuqaqMBP548y', '.149', 'kt', '1000', 1),
    ('ruby', 'ruby@gemma.sm.jj.ac.kr', '$2y$12$Kv6KYDvBFh.EOthxjfIy8eYrPYV7e1cRyUJ4Dpy3yxuqaqMBP548y', '.158', 'kt', '1000', 0),
    ('giselle', 'giselle@gemma.sm.jj.ac.kr', '$2y$12$Kv6KYDvBFh.EOthxjfIy8eYrPYV7e1cRyUJ4Dpy3yxuqaqMBP548y', '.170', 'lgt', '1002', 0),
    ('thea', 'thea@gemma.sm.jj.ac.kr', '$2y$12$Kv6KYDvBFh.EOthxjfIy8eYrPYV7e1cRyUJ4Dpy3yxuqaqMBP548y', '.150', 'skt', '1001', 0),
    ('kiera', 'kiera@gemma.sm.jj.ac.kr', '$2y$12$Kv6KYDvBFh.EOthxjfIy8eYrPYV7e1cRyUJ4Dpy3yxuqaqMBP548y', '.145', 'lgt', '1002', 0),
    ('molly', 'molly@gemma.sm.jj.ac.kr', '$2y$12$Kv6KYDvBFh.EOthxjfIy8eYrPYV7e1cRyUJ4Dpy3yxuqaqMBP548y', '.154', 'lgt', '1002', 0),
    ('duber', 'duber@gemma.sm.jj.ac.kr', '$2y$12$Kv6KYDvBFh.EOthxjfIy8eYrPYV7e1cRyUJ4Dpy3yxuqaqMBP548y', '.151', 'kt', '1000', 0),
    ('amelia', 'amelia@gemma.sm.jj.ac.kr', '$2y$12$Kv6KYDvBFh.EOthxjfIy8eYrPYV7e1cRyUJ4Dpy3yxuqaqMBP548y', '.153', 'skt', '1001', 0),
    ('gavin', 'gavin@gemma.sm.jj.ac.kr', '$2y$12$Kv6KYDvBFh.EOthxjfIy8eYrPYV7e1cRyUJ4Dpy3yxuqaqMBP548y', '.167', 'kt', '1000', 0),
    ('glenn', 'glenn@gemma.sm.jj.ac.kr', '$2y$12$Kv6KYDvBFh.EOthxjfIy8eYrPYV7e1cRyUJ4Dpy3yxuqaqMBP548y', '.146', 'lgt', '1002', 0),
    ('silas', 'silas@gemma.sm.jj.ac.kr', '$2y$12$Kv6KYDvBFh.EOthxjfIy8eYrPYV7e1cRyUJ4Dpy3yxuqaqMBP548y', '.147', 'kt', '1000', 0),
    ('nigel', 'nigel@gemma.sm.jj.ac.kr', '$2y$12$Kv6KYDvBFh.EOthxjfIy8eYrPYV7e1cRyUJ4Dpy3yxuqaqMBP548y', '.148', 'lgt', '1002', 0)
ON DUPLICATE KEY UPDATE
    is_admin = IF(VALUES(is_admin) = 1, 1, is_admin);

CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    title VARCHAR(140) NOT NULL DEFAULT '제목 없음',
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_posts_created_at (created_at),
    CONSTRAINT fk_posts_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
