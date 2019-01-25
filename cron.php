<?php 
// need PHPMailer!!!
require_once 'cfg.php';

$lasttime = time() - 86400/3; // 3days

require 'email/src/Exception.php';
require 'email/src/PHPMailer.php';
require 'email/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$get = $pdo->query("SELECT admin_id, email, id, username, nickname, expired FROM amx_admins_servers adm JOIN amx_amxadmins amx WHERE amx.expired <= '".$lasttime."' AND adm.admin_id = amx.id");

if ( $get != NULL ) {
	while ( $row = $get->fetch(PDO::FETCH_ASSOC) ) {
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
		    $mail->Subject = 'CS Shop - Срок привилегий подходит к концу!'; // тема сообщения
		    
		    if ( $row['username'] == NULL ) {
				$name = 'Ваш никнейм: <b>' . $row['nickname'] . '</b>';
			} else {
				$name = 'Ваш SteamID: <b>' . $row['username'] . '</b>';
			}

		    // отпавка html письма
		    $mail->Body    = "<div style='background-color:#ffb4b4;padding:10px;border-radius:10px;font-size: 14px;'>
	            Срок ваших привилегий подходит к концу!<br>
	            Ссылка на продление <a href='".$url."'' target='_blank'>клик</a><br>
	            ".$name."</div>
	        ";
		    
		    // если html не выводится
		    $mail->AltBody = "
				Срок ваших привилегий подходит к концу! | 
				Ссылка на продление ".$url." | ".$name."
			";

		    $mail->send();
		    echo 'Message has been sent'; // сообщение было отправлено
		} catch (Exception $e) {
		    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo; // Сообщение не может быть отправлено
		}
	}
}

?>
