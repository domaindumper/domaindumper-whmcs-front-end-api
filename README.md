## WHMCS front API

### Add `authToken` and `authTokenExpireAt` column in table `tblclients` by: 

```sql
ALTER TABLE `tblclients` 
ADD `authToken` TEXT NULL DEFAULT NULL AFTER `api_key`,
ADD `authTokenExpireAt` TIMESTAMP NOT NULL AFTER `authToken`;
```


### JWT_ISS, JWT_AUD, JWT_SECRET and JWT_ALGORITHM in configuration.php file as bellow

```php
define("JWT_ISS", "dflgijk.com");
define("JWT_AUD", "dflgijk.com");
define("JWT_SECRET", "JJERTJER345638GDGJDJ23645236JGDDJKGFKDKH45895HFSHWR78");
define("JWT_ALGORITHM", "HS256");
```
