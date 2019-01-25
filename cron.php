<?php 
// NEED PHPMailer 
require_once 'cfg.php';

$lasttime = time() - 86400/3; // 3days
var_dump($lasttime);
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$get = $pdo->query("SELECT * FROM amx_admins_servers admsrv JOIN amx_amxadmins amxadm WHERE amxadm.expired <= '".$lasttime."'");
$row = $get->fetch(PDO::FETCH_ASSOC);
//echo '<pre>';
//print_r($row);

if ( $row['email'] != NULL ) {
	require 'email/src/Exception.php';
	require 'email/src/PHPMailer.php';
	require 'email/src/SMTP.php';

	$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
	try {
	    //Server settings
	    $mail->CharSet = 'UTF-8';
	    $mail->SMTPDebug = 0;                                 // Включить подробный debug вывод
	    $mail->isSMTP();                                      // Настройте почтовую программу на использование SMTP
	    $mail->Host = 'smtp.gmail.com;smtp.gmail.com';  // Укажите основной и резервный SMTP-серверы
	    $mail->SMTPAuth = true;                               // Включить аутентификацию SMTP
	    $mail->Username = '';                 // SMTP username
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
	    
	    // если html не выводится
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