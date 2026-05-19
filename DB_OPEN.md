# DB 여는 방법

터미널에서 프로젝트 서버의 MySQL DB를 열려면 아래 명령을 실행합니다.

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

종료:

```sql
exit;
```
