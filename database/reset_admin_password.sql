-- 将下面的 {{BCRYPT_HASH}} 替换为：
-- php -r "echo password_hash('123456', PASSWORD_BCRYPT), PHP_EOL;"
UPDATE `admin_user`
SET `password` = '{{BCRYPT_HASH}}'
WHERE `username` = 'admin';

