## WHMCS front API

### Add bellow to your configuration.php file: 


define('JWT_SECRET', 'your_secret_key_here'); // Replace with a secure key
define('JWT_ALGORITHM', 'HS256'); // Use a suitable algorithm (HS256 is common)
define('JWT_ISS', 'HS256'); // Issuer (e.g., your application's domain)
define('JWT_AUD', 'HS256'); // Audience (e.g., the intended recipient)
