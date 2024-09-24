<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require '../../../../../init.php';
require '../../../lib/Session.php';

$ca = new ClientArea();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    $reset_token = !empty($_REQUEST['reset_token']) ? $_REQUEST['reset_token'] : '';
    $password = !empty($_REQUEST['password']) ? $_REQUEST['password'] : '';
    $password2 = !empty($_REQUEST['password2']) ? $_REQUEST['password2'] : '';

    if (isPasswordResetToken($reset_token)) {

        // Check for empty fields
        if (empty($password) || empty($password2)) {

            $ResponseCode = 400;

            $response = [
                'status' => 'error',
                'code' => 400,
                'message' => 'Please enter both passwords.'
            ];
        } else {
            // Check if passwords match
            if ($password === $password2) {

                // Check if password is valid

                if (isPasswordVaild($password2)) {

                    $encripted_password = password_hash($password2, PASSWORD_BCRYPT);

                    // Now update the password

                    Illuminate\Database\Capsule\Manager::table('tblusers')
                        ->updateOrInsert(
                            ['reset_token' => $reset_token],
                            ['password' => $encripted_password, 'reset_token' => '', 'updated_at' => date('Y-m-d H:i:s')]
                        );


                    // Prepare the response data

                    $ResponseCode = 200;

                    $response = [
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Please log in again to continue as the password has been updated.'
                    ];


                } else {
                    $ResponseCode = 400;

                    $response = [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Password must contain at least least one digit, one uppercase letter, one lowercase letter, one number, and one special character from: @$!%*#?&'
                    ];
                }


            } else {
                // Passwords don't match
                $ResponseCode = 400;

                $response = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'There is a mismatch between your password and confirm password.'
                ];
            }
        }

    } else {
        $ResponseCode = 400;
        $response = [
            'status' => 'error',
            'code' => 400,
            'message' => 'The password reset link you used to reset your password is no longer valid.'
        ];
    }


} else {

    // Handle invalid request method
    $ResponseCode = 405;
    $response = [
        'status' => 'error',
        'code' => 405,
        'message' => 'Method not allowed'
    ];

}



http_response_code($ResponseCode);
header('Content-Type: application/json');
echo json_encode($response);