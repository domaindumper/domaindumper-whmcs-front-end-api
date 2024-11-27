## WHMCS front API

### Add `auth_token` column in table `tblusers` by: 

```sql
ALTER TABLE `tblusers` ADD `auth_token` TEXT NULL DEFAULT NULL AFTER `reset_token`;

```


### JWT_SECRET and JWT_ALGORITHM in configuration.php file as bellow

```php
define("JWT_SECRET", "JJERTJER345638GDGJDJ23645236JGDDJKGFKDKH45895HFSHWR78");
define("JWT_ALGORITHM", "HS256");
```
