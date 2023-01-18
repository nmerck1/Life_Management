<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require '../vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'lifemanagement.me';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'donotreply@lifemanagement.me';                     //SMTP username
    $mail->Password   = 'w4kg93%wV$!F';                               //SMTP password
    $mail->SMTPSecure = "tls";                      // Connect using a TLS connection
    //$mail->Port       = 25;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    $mail->Port = 587;
    $mail->Encoding = '7bit';       // SMS uses 7-bit encoding

    //Recipients
    $mail->setFrom('donotreply@lifemanagement.me', 'Life Management');
    $mail->addAddress('8646842252@tmomail.net', 'Anna');     //Add a recipient
    $mail->addAddress('8643444256@tmomail.net', 'Marie');     //Add a recipient
    $mail->addAddress('8435437185@tmomail.net', 'Nathaniel iPhone');     //Add a recipient
    $mail->addAddress('8643542267@txt.att.net', 'Nathaniel LG');     //Add a recipient
    $mail->addAddress('nathanielmerck@yahoo.com', 'Nathaniel Email');     //Add a recipient
    //$mail->addAddress('ellen@example.com');               //Name is optional
    //$mail->addReplyTo('info@example.com', 'Information');
    //$mail->addCC('8435437185@tmomail.net');
    //$mail->addCC('8643542267@txt.att.net');
    //$mail->addCC('8646842252@tmomail.net');
    //$mail->addCC('8643444256@tmomail.net');
    //$mail->addBCC('bcc@example.com');

    //Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    // I made this false since its sms... //
    $mail->ContentType = 'text/plain';
    $mail->IsHTML(false);                               //Set email format to HTML
    $mail->CharSet = 'UTF-8';

    $body = 'This is your daily reminder to update your finances for the day.';
    $body .= 'Thank you for being a member of the Life Management Services!';

    $mail->Subject = '';                                // subject needs to be blank for sms...
    $mail->Body    = $body;
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}


/*
$to = "8643542267@txt.att.net";   // 8435437185@tmomail.net
$from = "donotreply@lifemanagement.me";
$message = "This is a text message \n New line...";
$headers = "From: $from \n";
$subject = "";

mail($to, $subject, $message, $headers);
*/
?>
