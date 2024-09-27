## WHMCS front API

### Add bellow to your configuration.php file: 


define('JWT_SECRET', 'your_secret_key_here'); // Replace with a secure key
define('JWT_ALGORITHM', 'HS256'); // Use a suitable algorithm (HS256 is common)
define('JWT_ISS', 'api.example.com'); // Issuer (e.g., your application's domain)
define('JWT_AUD', 'example.com'); // Audience (e.g., the intended recipient)


### Add authToken row to tblusers tabele

ALTER TABLE `tblusers` ADD `authToken` TEXT NULL DEFAULT NULL AFTER `reset_token`;


