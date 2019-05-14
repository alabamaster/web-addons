<?php 
// need PHP Mailer
require_once 'cfg.php';

$lasttime = time() - 86400*3; // 3days
$lasttime2 = time() + 86400*3; // 3days
//var_dump($lasttime,$lasttime2);

require 'email/src/Exception.php';
require 'email/src/PHPMailer.php';
require 'email/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$get = qry("SELECT id, admin_id, created, expired, email, steamid, nickname, flags, tarif_id, server_id FROM amx_amxadmins, amx_admins_servers WHERE expired >= ? AND admin_id = id AND email != 'NULL' AND expired <= ?", array($lasttime, $lasttime2));

if ( $get ) {
	while ( $row = $get->fetch(PDO::FETCH_ASSOC) ) {
		if ( $row['email'] == NULL ) {
			continue;
		} else {
			$mail = new PHPMailer(true); // Passing `true` enables exceptions
			try {
				//Server settings
				$mail->CharSet = 'UTF-8';
				$mail->SMTPDebug = 0; // Включить подробный debug вывод
				$mail->isSMTP(); // Настройте почтовую программу на использование SMTP
				$mail->Host = 'smtp.gmail.com'; // Укажите основной и резервный SMTP-серверы
				$mail->SMTPAuth = true; // Включить аутентификацию SMTP
				$mail->Username = 'your-mail@gmail.com'; // SMTP username
				$mail->Password = 'your-password'; // SMTP password
				$mail->SMTPSecure = 'ssl'; // Включить шифрование TLS, `ssl` также разрешен
				$mail->Port = 465; // TCP-порт для подключения

			    //Получатели
			    $mail->setFrom('mail228@gmail.com', 'CS Shop'); // от кого + имя
			    $mail->addAddress($row['email']); // кому
			    $mail->addReplyTo('mail228@gmail.com'); // адрес для ответа

			    //Content
			    $mail->isHTML(true); // Set email format to HTML
			    $mail->Subject = 'CS Shop - Срок привилегий подходит к концу!'; // тема сообщения

				if ( $row['flags'] == 'ac' ) {
					$name = '<span>Ваш SteamID: <b>' . $row['steamid'] . '</b>';
				} else {
					$name = '<span>Ваш никнейм: <b>' . $row['nickname'] . '</b>';
				}
				$start = $row['created'];
				$end = $row['expired'];
				$usluga = ( tarif_name($row['tarif_id']) == 'NULL' ) ? 'unknown' : tarif_name($row['tarif_id']);

			    // отпавка html письма
			    $mail->Body    = "
					<div style='background-color:#ffb4b4;padding:10px;border-radius:10px;font-size:14px;max-width:50%;border:2px solid #187ab3;'>
			            <span><i class='fas fa-calendar-times'></i> Срок ваших привилегий подходит к концу!</span><br>
			            <span><i class='fas fa-link'></i> Ссылка на продление <a href='".$url."'' target='_blank'>клик</a></span><br>
			            <div style='padding:5px;background-color:#c9ebff;border-radius:5px;margin-top:4px;'>
			            	".$name."<br>
				            <span>Дата покупки: <b>".date('d.m.Y',$start)."</b></span><br>
				            <span>Дата окончания: <b>".date('d.m.Y',$end)."</b></span><br>
				            <span>Привилегия: <b>".$usluga."</b></span><br>
				            <span>Сервер: <b>".serv_info($row['server_id'])."</b></span>
			            </div>
					</div>
		        ";
			    
			    // если html не выводится
			    $mail->AltBody = "
					Срок ваших привилегий подходит к концу! | 
					Ссылка на продление ".$url." | ".$name." | Дата покупки: ".date('d.m.Y',$start)." | Дата окончания: ".date('d.m.Y',$end)." | Привилегия: ".$usluga." | Сервер: ".serv_info($row['server_id'])."
				";

			    $mail->send();
			    echo 'Message has been sent<br>'; // сообщение было отправлено
			} catch (Exception $e) {
			    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo; // Сообщение не может быть отправлено
			}
		}
	}
}

?>
