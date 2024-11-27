## WHMCS front API

### Add `authToken` column in table `tblusers` by: 

```sql
ALTER TABLE `tblusers` ADD `authToken` TEXT NULL DEFAULT NULL AFTER `reset_token`;

```


### JWT_ISS, JWT_AUD, JWT_SECRET and JWT_ALGORITHM in configuration.php file as bellow

```php
define("JWT_ISS", "dflgijk.com");
define("JWT_AUD", "dflgijk.com");
define("JWT_SECRET", "JJERTJER345638GDGJDJ23645236JGDDJKGFKDKH45895HFSHWR78");
define("JWT_ALGORITHM", "HS256");
```
