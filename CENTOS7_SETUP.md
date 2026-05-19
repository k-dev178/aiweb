# CentOS 7 실행 방법

CentOS 7 기본 PHP 5.4에서도 동작하도록 코드가 맞춰져 있습니다.

## 1. 패키지 설치

```bash
sudo yum install -y httpd mariadb-server php php-pdo php-mysql
```

## 2. MariaDB 시작

```bash
sudo systemctl enable mariadb
sudo systemctl start mariadb
```

## 3. DB 생성

프로젝트 폴더에서 root 권한으로 한 번 실행합니다.

```bash
mysql -u root < setup.sql
```

## 4. Apache 시작

프로젝트를 Apache 문서 루트에 둔 뒤 실행합니다.

```bash
sudo systemctl enable httpd
sudo systemctl start httpd
```

## 5. DB 접속 확인

```bash
mysql -u aiweb_user -pwjsansrk aiweb
```

## 기본 로그인

- 관리자: `gemma / wjsansrk`
- 일반 계정 예시: `samuel / wjsansrk`
