<?php 
// NEED INSTALL PHPMailer
$url = 'https://csonelove.ru/';

$lasttime = time() - 86400*3; // 3 days

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// в amx_admins_servers + поле lasttime, тип int(11), NULL - Да
$get = $pdo->query("SELECT * FROM amx_admins_servers admsrv JOIN amx_amxadmins amxadm WHERE admsrv.lasttime <= '".$lasttime."'");
$row = $get->fetch(PDO::FETCH_ASSOC);

if ( $row['email'] != NULL ) {
	require 'inc/email/src/Exception.php';
	require 'inc/email/src/PHPMailer.php';
	require 'inc/email/src/SMTP.php';

	$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
	try {
	    //Server settings
	    $mail->CharSet = 'UTF-8';
	    $mail->SMTPDebug = 0;                                 // Включить подробный debug вывод
	    $mail->isSMTP();                                      // Настройте почтовую программу на использование SMTP
	    $mail->Host = 'smtp.gmail.com;smtp.gmail.com';  // Укажите основной и резервный SMTP-серверы
	    $mail->SMTPAuth = true;                               // Включить аутентификацию SMTP
	    $mail->Username = 'alabamaster1@gmail.com';                 // SMTP username
	    $mail->Password = '';                           // SMTP password
	    $mail->SMTPSecure = 'ssl';                            // Включить шифрование TLS, `ssl` также разрешен
	    $mail->Port = 465;                                    // TCP-порт для подключения

	    //Получатели
	    $mail->setFrom('alabamaster1@gmail.com', 'CS Shop'); // от кого + имя
	    $mail->addAddress($row['email']); // кому
	    $mail->addReplyTo('alabamaster1@gmail.com'); // адрес для ответа

	    //Content
	    $mail->isHTML(true);                                  // Set email format to HTML
	    $mail->Subject = 'CS Shop - Услуга подходит к концу!'; // тема сообщения
	    
	    // отпавка html письма
	    $mail->Body    = "<div style='background-color:#ffb4b4;padding:10px;border-radius:10px;font-size: 14px;'>
            Ваши привилегии истекают через: <b>3 дня</b>!<br>
            Ссылка на покупку привилегий: <b><a href='".$url."'' target='_blank'>кликнуть сюда</a></b><br>
            Ваш никнейм: ".$row['nickname']."</div>
        ";
	    
	    // if html not supported
	    $mail->AltBody = "
			Ваши привилегии истекают через 3 дня! | 
			Ссылка на покупку привилегий: ".$url."
		";

	    $mail->send();
	    echo 'Message has been sent'; // сообщение было отправлено
	} catch (Exception $e) {
	    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo; // Сообщение не может быть отправлено
	}
}