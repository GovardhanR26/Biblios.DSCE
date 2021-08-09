<?php

require 'PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer;
$mail->SMTPDebug = SMTP::DEBUG_SERVER;                  //Enable verbose debug output
$mail->isSMTP();                                        //Send using SMTP
$mail->Host       = 'smtp.gmail.com';                   //Set the SMTP server to send through
$mail->SMTPAuth   = true;                               //Enable SMTP authentication
$mail->Username   = 'dummy@example.com';            //SMTP username
$mail->Password   = 'password';                    //SMTP password
$mail->SMTPSecure = 'ssl';                              //Enable implicit TLS encryption
$mail->Port       = 465;
$mail->SMTPDebug = 0;                                //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

?>