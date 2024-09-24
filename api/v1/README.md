## WHMCS front API

### Add `auth_token` column in table `tblusers` by: 

ALTER TABLE `tblusers` ADD `auth_token` TEXT NULL DEFAULT NULL AFTER `reset_token`;
