# WHMCS Frontend API Documentation

## Database Customizations

### 1. Client Authentication (`tblclients`)
```sql
ALTER TABLE `tblclients` 
ADD `authToken` TEXT NULL DEFAULT NULL AFTER `api_key`,
ADD `authTokenExpireAt` TIMESTAMP NOT NULL AFTER `authToken`;
```

### 2. Shopping Cart Tables
```sql
CREATE TABLE `carts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `config_options` TEXT NULL,
  `custom_fields` TEXT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
```

### 3. Email Verification Table
```sql
CREATE TABLE `email_verification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
```

## Configuration Required

Add these constants to your WHMCS configuration.php file:
```php
define("JWT_ISS", "your-domain.com");
define("JWT_AUD", "your-domain.com");
define("JWT_SECRET", "your-secure-secret-key");
define("JWT_ALGORITHM", "HS256");
```

## Available APIs

### Authentication
1. **Login** (`/api/v2/auth/login`)
   - Method: POST
   - Handles user authentication and JWT token generation

2. **Logout** (`/api/v2/user/logout`)
   - Method: POST
   - Invalidates JWT token

### User Management
1. **Check User** (`/api/v2/user/check-user`)
   - Method: POST
   - Verifies if email exists and sends OTP

2. **Register** (`/api/v2/user/register`)
   - Method: POST
   - Creates new user account

3. **Profile** (`/api/v2/user/profile`)
   - Method: GET/PUT
   - Manages user profile information

4. **Update** (`/api/v2/user/update`)
   - Method: POST
   - Updates user details

### Shopping Cart
1. **Add to Cart** (`/api/v2/cart/add`)
   - Method: POST
   - Adds items to cart

2. **Get Cart** (`/api/v2/cart/get`)
   - Method: POST
   - Retrieves cart contents

3. **Remove from Cart** (`/api/v2/cart/remove`)
   - Method: POST
   - Removes items from cart

### Products
1. **All Products** (`/api/v2/product/all`)
   - Method: GET
   - Lists all available products

2. **Old Database** (`/api/v2/product/old`)
   - Method: GET
   - Lists old database products

### Services
1. **List Services** (`/api/v2/services/list`)
   - Method: GET
   - Lists user's active services

2. **Service Details** (`/api/v2/services/detail`)
   - Method: POST
   - Shows detailed service information

### Support Tickets
1. **List Tickets** (`/api/v2/tickets/list`)
   - Method: GET
   - Lists user's support tickets

2. **View Ticket** (`/api/v2/tickets/view`)
   - Method: POST
   - Shows ticket details

3. **Reply to Ticket** (`/api/v2/tickets/reply`)
   - Method: POST
   - Adds reply to ticket

4. **Departments** (`/api/v2/tickets/departments`)
   - Method: GET
   - Lists support departments

### Invoices
1. **List Invoices** (`/api/v2/invoices/list`)
   - Method: GET
   - Lists user's invoices

2. **Invoice Details** (`/api/v2/invoices/detail`)
   - Method: POST
   - Shows invoice details

## Authentication Flow
1. All secure endpoints require X-Authorization header
2. Token format: `Bearer <JWT_TOKEN>`
3. Tokens expire based on remember me option:
   - Normal: 24 hours
   - Remember Me: 7 days

## Error Handling
All APIs return standardized error responses:
```json
{
    "status": "error",
    "code": <HTTP_STATUS_CODE>,
    "message": "<error_message>"
}
```

## Currency Support
- Supports multiple currencies (INR, USD)
- Currency details fetched from `tblcurrencies`
- Includes formatting preferences and exchange rates

## File Upload Restrictions
- Maximum file size: 2MB
- Allowed file types: jpg, gif, jpeg, png
