<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class emailsettings {

    public function __construct($hostname,$username,$password) {
        $this->mail = new PHPMailer(true);// Passing `true` enables exceptions
        //Server settings
        $this->mail->SMTPDebug = 0;                                 // Enable verbose debug output
        $this->mail->isSMTP();                                      // Set mailer to use SMTP
        $this->mail->Host = $hostname;  // Specify main and backup SMTP servers
        $this->mail->SMTPAuth = true;                               // Enable SMTP authentication
        $this->mail->Username = $username;                 // SMTP username
        $this->mail->Password = $password;                           // SMTP password
        $this->mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $this->mail->Port = 587;                                    // TCP port to connect to

        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

    }

    /**
     * @param $toname
     * @param $toemail
     * @param $subject
     * @param $message
     * @param $altmessage
     * @return bool
     */
    function sendmail($toname, $toemail, $subject, $message, $altmessage) {
        try {
            //Recipients
            $this->mail->setFrom(USERNAMESMTP, 'Women Safety');
            $this->mail->addAddress($toemail, $toname);     // Add a recipient

            //Content
            $this->mail->isHTML(true); // Set email format to HTML
            $this->mail->Subject = $subject;
            $this->mail->Body = $message;
            $this->mail->AltBody = $altmessage; //When html email fails show this

            return $this->mail->send();
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $this->mail->ErrorInfo;
            return false;
        }
    }
}
