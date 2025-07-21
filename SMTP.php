<?php
                    header("Content-Type:text/html; charset=utf-8");
                    date_default_timezone_set("Asia/Taipei");
					//recapture寫法參照 https://www.kaplankomputing.com/blog/tutorials/php/setting-recaptcha-2-0-ajax-demotutorial/
					
					// Import PHPMailer classes into the global namespace
					 // These must be at the top of your script, not inside a function
					 //use PHPMailer\PHPMailer\PHPMailer;
					 //use PHPMailer\PHPMailer\SMTP;
					 //use PHPMailer\PHPMailer\Exception;
					 // Load Composer's autoloader
					 //require 'vendor/autoload.php';
					
					//新增
					use PHPMailer\PHPMailer\PHPMailer;
					use PHPMailer\PHPMailer\Exception;
					
					//設定檔案路徑
					require 'PHPMailer/src/Exception.php';
					require 'PHPMailer/src/PHPMailer.php';
					require 'PHPMailer/src/SMTP.php';
					
					//建立物件                                                                
					$mail = new PHPMailer(true);
					
					try {
							//Server settings
							//$mail->SMTPDebug = SMTP::DEBUG_SERVER;  // Enable verbose debug output
							$mail->SMTPDebug = 0; // DEBUG訊息
							//$mail->isSMTP(); //使用SMTP
							$mail->Host = 'localhost'; // SMTP server 位址
							$mail->SMTPAuth = true;  // 開啟SMTP驗證
							$mail->Username = 'service@snewup.com'; // SMTP 帳號
							$mail->Password = '5?PjxcF*1B$%'; // SMTP 密碼
							//$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
							$mail->SMTPSecure = "ssl"; // Gmail要透過SSL連線
							$mail->Port       = 25; // SMTP TCP port 
						
							//設定收件人資料
							$mail->setFrom('service@snewup.com', 'Mailer'); // 寄件人(透過Gmail發送會顯示Gmail帳號為寄件者)
							$mail->addAddress('johnbabalingbubu@gmail.com', '建台興官網表單'); // 收件人會顯示 Apple User<apple@example.com>(*註2)
							// $mail->addAddress('banana@example.com'); // 名字非必填
							//$mail->addReplyTo('info@example.com', 'Information'); //回信的收件人
							$mail->addCC('lililala0112@gmail.com'); //副本
							//$mail->addBCC('bcc@example.com'); //密件副本
						
							// 附件
							// $mail->addAttachment('/var/tmp/file.tar.gz');  附件 (*註3) 
							// $mail->addAttachment('/tmp/image.jpg', 'new.jpg'); // 插入附件可更改檔名

							// 信件內容
							$mail->isHTML(true); // 設定為HTML格式
							$mail->Subject = "=?UTF-8?B?".base64_encode("建台興 官網表單")."?=";  //信件標題
							
							$message = '聯絡人 : '.stripslashes($_POST['name']).'<br>';
							$message .= '手機 : '.stripslashes($_POST['phone']).'<br>';
							$message .= '信箱 : '.stripslashes($_POST['email']).'<br>';
							$message .= 'LIND ID : '.stripslashes($_POST['lineID']).'<br>';
                            $message .= '留言 : '.stripslashes($_POST['message']).'<br>';
							$message .= '時間 : '. date('Y/m/d H:i:s') ."\n";

							//Captcha
							$response = $_POST["captcha"];
							$sitekey = '6Lc8epsaAAAAAMwIiRbWupNuTrvvzH-AoLusX4b4';
							$verify=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$sitekey}&response={$response}");

							$captcha_success=json_decode($verify);
							if ($captcha_success->success==1) {

							   //This user was verified by recaptcha.
								$mail->Body    = '<h1>官網表單</h1> <p style="font-size:16px; line-height=24px">'.$message.'</p>'; // 信件內容
								$mail->AltBody = 'This is the body in plain text for non-HTML mail clients'; // 對方若不支援HTML的信件內容

								$mail->send();
								$result = array(
									'status' => 1,
									'message'=> '已送出，感謝您的填寫，我們會盡快與您連繫，如有久候未聯絡有可能漏信，請來電諮詢'
								);

							}
							else{
								//This user was not verified by recaptcha
								$result = array(
									'status' => 0,
									'message'=>  "驗證錯誤，請勾選驗證方框"
								);
							}


				} catch (Exception $e) {
                    $result = array(
                        'status' => 0,
                        'message'=>  "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"
                    );
                }
                
                echo json_encode($result); 
?>