<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->setLanguage('ru', 'PHPMailer/language/');
    $mail->isHTML(true);

    $mail->setFrom("hacker@gmail.com", "Хакер-помощник");
    $mail->addAddress("akkyevnurbek@gmail.com");
    $mail->Subject = "Получены данные пользователя";

    $body = "<h1>Данные пользователя:</h1>";
    $body.="<p>hello</p>";

    $mail->Body = $body;

    header('Content-type: application/json');
