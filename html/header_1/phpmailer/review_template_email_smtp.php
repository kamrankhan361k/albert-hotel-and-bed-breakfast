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
    $mail->setFrom('info@domain.com', 'Review from Albert Hotel');             // Email Address and Name FROM
    $mail->addAddress('info@domain.com', 'Jhon Doe');                            // Email Address and Name TO - Name is optional
    $mail->addReplyTo('noreply@domain.com', 'Review from Albert Hotel');       // Email Address and Name NOREPLY
    $mail->isHTML(true);                                                       
    $mail->Subject = 'Review from Albert Hotel';                                // Email Subject       

    // Email verification, do not edit.
    function isEmail($email_review ) {
       return(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/",$email_review ));
    }

    // Form fields
    $name_review     = $_POST['name_review'];
    $lastname_review    = $_POST['lastname_review'];
    $email_review    = $_POST['email_review'];
    $room_type_review   = $_POST['room_type_review'];
    $position_review = $_POST['position_review'];
    $comfort_review = $_POST['comfort_review'];
    $price_review = $_POST['price_review'];
    $quality_review = $_POST['quality_review'];
    $review_text = $_POST['review_text'];
    $verify_review   = $_POST['verify_review'];

    if(trim($name_review) == '') {
    echo '<div class="error_message">You must enter your Name.</div>';
    exit();
    } else if(trim($lastname_review ) == '') {
        echo '<div class="error_message">You must enter your Last name.</div>';
        exit();
    } else if(trim($email_review) == '') {
        echo '<div class="error_message">Please enter a valid email address.</div>';
        exit();
    } else if(!isEmail($email_review)) {
        echo '<div class="error_message">You have enter an invalid e-mail address, try again.</div>';
        exit();
    } else if(trim($room_type_review ) == '') {
        echo '<div class="error_message">You must enter your Room Type.</div>';
        exit();
    } else if(trim($position_review ) == '') {
        echo '<div class="error_message">Please rate Position.</div>';
        exit();
    } else if(trim($comfort_review ) == '') {
        echo '<div class="error_message">Please rate Comfort.</div>';
        exit();
    } else if(trim($price_review ) == '') {
        echo '<div class="error_message">Please rate Room price.</div>';
        exit();
    } else if(trim($quality_review ) == '') {
        echo '<div class="error_message">Please rate Quality.</div>';
        exit();
    } else if(trim($review_text) == '') {
        echo '<div class="error_message">Please enter your review.</div>';
        exit();
    } else if(!isset($verify_review) || trim($verify_review) == '') {
        echo '<div class="error_message"> Please enter the verification number.</div>';
        exit();
    } else if(trim($verify_review) != '4') {
        echo '<div class="error_message">The verification number you entered is incorrect.</div>';
        exit();
    }                              
            
    // Get the email's html content
    $email_html = file_get_contents('template-email.html');

   // Setup html content
    $e_content = "$name_review $lastname_review post the following review:<br><br>Room type: $room_type_review.<br>Position rate: $position_review.<br>Comfort rate: $comfort_review.<br>Room price rate: $price_review.<br>Quality rate: $quality_review.<br>Review: $review_text. <br><br>You can contact $name_review  $lastname_review via email: $email_review.";
    $body = str_replace(array('message'),array($e_content),$email_html);
    $mail->MsgHTML($body);

    $mail->CharSet = 'UTF-8';
    $mail->send();

    // Confirmation/autoreplay email send to who fill the form
    $mail->ClearAddresses();
    $mail->isSMTP();
    $mail->addAddress($_POST['email_review']); // Email address entered on form
    $mail->isHTML(true);
    $mail->Subject    = 'Review posted for Albert Hotel'; // Custom subject
    
    // Get the email's html content
    $email_html_confirm = file_get_contents('confirmation.html');

    // Setup html content, do not edit
    $body = str_replace(array('message'),array($e_content),$email_html_confirm);
    $mail->MsgHTML($body);

    $mail->CharSet = 'UTF-8';
    $mail->Send();

    // Succes message
    echo '<div id="success_page" style="padding:20px 0">
            <strong>Thank you.</strong>
            Your review has been submitted.
        </div>';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    } 
?> 
