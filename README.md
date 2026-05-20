# AIWeb 게시판

PHP와 MySQL로 만든 로그인 기반 게시판입니다. CentOS 7 기본 APM 환경의 PHP 5.4에서도 동작하도록 맞춰져 있습니다.

## 주요 기능

- 로그인한 사용자만 게시글 작성 가능
- 게시글 목록과 상세 페이지 제공
- 본인이 작성한 게시글만 수정 가능
- 본인 글 또는 관리자 계정만 게시글 삭제 가능
- 공개 회원가입 차단, `gemma` 관리자 계정으로만 계정 추가, 수정, 삭제 가능
- 라이트모드 기본, 다크모드 전환 지원

## 기본 계정

```text
관리자: gemma / wjsansrk
일반 계정: samuel / wjsansrk
```

## 로컬에서 바로 실행

프로젝트 폴더에서 PHP 내장 서버를 켭니다.

```bash
php -S 127.0.0.1:8000
```

브라우저에서 접속합니다.

```text
http://127.0.0.1:8000/index.php
```

종료는 서버가 켜진 터미널에서 `Ctrl + C`를 누르면 됩니다.

포트 `8000`이 이미 사용 중이면 다른 포트로 실행합니다.

```bash
php -S 127.0.0.1:8001
```

## CentOS 7 APM 실행

CentOS 7 기본 PHP 5.4 기준입니다.

### 1. 패키지 설치

```bash
sudo yum install -y httpd mariadb-server php php-pdo php-mysql
```

### 2. 프로젝트 배치

프로젝트 폴더를 Apache 문서 루트 아래에 둡니다.

```bash
sudo mkdir -p /var/www/html/aiweb
sudo cp -R . /var/www/html/aiweb/
sudo chown -R apache:apache /var/www/html/aiweb
```

### 3. MariaDB 시작

```bash
sudo systemctl enable mariadb
sudo systemctl start mariadb
```

### 4. DB 생성

프로젝트 폴더에서 root 권한으로 한 번 실행합니다.

```bash
mysql -u root < setup.sql
```

이미 DB가 있어도 테이블은 유지됩니다. 기존 계정의 비밀번호와 관리자가 수정한 정보는 덮어쓰지 않고, `gemma` 계정의 관리자 권한만 보장합니다.

### 5. Apache 시작

```bash
sudo systemctl enable httpd
sudo systemctl start httpd
```

서버 IP가 `192.168.0.10`이면 브라우저에서 이렇게 접속합니다.

```text
http://192.168.0.10/aiweb/index.php
```

## 서버 관리

상태 확인:

```bash
sudo systemctl status httpd
sudo systemctl status mariadb
```

재시작:

```bash
sudo systemctl restart httpd
sudo systemctl restart mariadb
```

종료:

```bash
sudo systemctl stop httpd
sudo systemctl stop mariadb
```

방화벽을 쓰는 서버라면 HTTP 포트를 엽니다.

```bash
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --reload
```

## DB 열기

터미널에서 MySQL DB를 열려면 아래 명령을 실행합니다.

```bash
mysql -u aiweb_user -pwjsansrk aiweb
```

자주 쓰는 확인 명령:

```sql
SHOW TABLES;
SELECT * FROM users;
SELECT * FROM posts;
```

계정 정보 확인:

```sql
SELECT username, email, ip_address, room_name, room_number, is_admin FROM users;
```

게시글 확인:

```sql
SELECT posts.id, users.username, posts.title, posts.content, posts.created_at
FROM posts
LEFT JOIN users ON users.id = posts.user_id
ORDER BY posts.id DESC;
```

DB 종료:

```sql
exit;
```

## 문제 확인

Apache 로그:

```bash
sudo tail -f /var/log/httpd/error_log
```

DB 접속 확인:

```bash
mysql -u aiweb_user -pwjsansrk aiweb -e "SHOW TABLES;"
```
