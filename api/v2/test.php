<?php

use WHMCS\Mail\Mailer;
use WHMCS\Mail\Message;

require_once '../../init.php';
require_once '../../vendor/whmcs/whmcs-foundation/lib/Mail/Mailer.php';

$email = 'srapsware@gmail.com';
$subject = 'Your Subject';
$messageBody = 'Your Message'; // Can be HTML content

// Create a new Message instance
$message = new Message();

// Set the email type (e.g., 'general', 'product', 'domain')
$message->setType('general'); 

// Add the recipient
$message->addRecipient('to', $email, 'Recipient Name'); // Or leave name empty

// Set the subject and body
$message->setSubject($subject);
$message->setBody($messageBody);

// Optionally, add an attachment (example with string attachment)
// $attachmentData = 'This is the attachment content.';
// $message->addStringAttachment('my_attachment.txt', $attachmentData);

// Create a Mailer instance and send the message
$mailer = new Mailer();
$mailer->setFromName('Whoisextractor');
$mailer->setFromEmail('support@whoisextractor.com');
$mailer->sendMessage($message); // Send the constructed message

if ($mailer->send()) {
    echo 'Email sent successfully!';
} else {
    echo 'Email sending failed: ' . $mailer->ErrorInfo;
}

?>