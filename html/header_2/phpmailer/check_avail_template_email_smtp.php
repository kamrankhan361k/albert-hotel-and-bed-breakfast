<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

$mail = new PHPMailer(true);

try {

    //Server settings
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtpserver';                           // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'username';                             // SMTP username
    $mail->Password   = 'password';                             // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients - main edits
    $mail->setFrom('info@domain.com', 'Booking request from Albert Hotel');             // Email Address and Name FROM
    $mail->addAddress('info@domain.com', 'Jhon Doe');                           // Email Address and Name TO - Name is optional
    $mail->addReplyTo('noreply@domain.com', 'Booking request from Albert Hotel');       // Email Address and Name NOREPLY
    $mail->isHTML(true);                                                       
    $mail->Subject = 'Booking request from Albert Hotel';                       // Email Subject       

    // Email verification, do not edit
    function isEmail($email_booking ) {
        return(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/",$email_booking));
    }

    // Form fields
    $room_type     = $_POST['room_type'];
    $check_in     = $_POST['check_in'];
    $check_out    = $_POST['check_out'];
    $adults    = $_POST['adults'];
    $children   = $_POST['children'];
    $room_type = $_POST['room_type'];
    $name_booking   = $_POST['name_booking'];
    $email_booking   = $_POST['email_booking'];

    if(trim($check_in) == '') {
    echo '<div class="error_message">Enter check in date.</div>';
    exit();
    } else if(trim($check_out ) == '') {
        echo '<div class="error_message">Enter check out date.</div>';
        exit();
    } else if(trim($adults ) == '') {
        echo '<div class="error_message">Enter adults number.</div>';
        exit();
    } else if(trim($name_booking ) == '') {
        echo '<div class="error_message">Enter your Name and Last name.</div>';
        exit();
    } else if(trim($email_booking) == '') {
        echo '<div class="error_message">Please enter a valid email address.</div>';
        exit();
    } else if(!isEmail($email_booking)) {
        echo '<div class="error_message">You have enter an invalid e-mail address, try again.</div>';
        exit();
    }                               
            
    // Get the email's html content
    $email_html = file_get_contents('template-email.html');

   // Setup html content
    $e_content = "<strong>$name_booking</strong> with send a message with the following booking request:<br><br>Check in: $check_in<br>Check out: $check_out<br>Room Type: $room_type<br>Number of adults: $adults<br>Number of child: $children.<br><br>You can contact $name_booking via email: $email_booking.";

    $body = str_replace(array('message'),array($e_content),$email_html);
    $mail->MsgHTML($body);

    $mail->CharSet = 'UTF-8';
    $mail->send();

    // Confirmation/autoreplay email send to who fill the form
    $mail->ClearAddresses();
    $mail->isSMTP();
    $mail->addAddress($_POST['email_booking']); // Email address entered on form
    $mail->isHTML(true);
    $mail->Subject    = 'Confirmation'; // Custom subject
    
    // Get the email's html content
    $email_html_confirm = file_get_contents('confirmation.html');

    // Setup html content, do not edit
    $body = str_replace(array('message'),array($e_content),$email_html_confirm);
    $mail->MsgHTML($body);

    $mail->CharSet = 'UTF-8';
    $mail->Send();

    // Succes message
    echo '<div id="success_page" style="padding:30px; text-align:center; font-size:18px;">
            <strong>Thank you.</strong>
            Your message has been submitted. We will contact you shortly.
        </div>';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    } 
?> 
