<?php

class Custom_Controller_Plugin_Mail {

    /**
     * 发送邮件模块
     *
     * @param string $toemail
     * @param string $toemailusername
     * @param string $emailobject
     */
    public function sendmail($toemail, $toemailusername, $mail_title, $mailbody) {
        $config = array(
            //'ssl' => EMAIL_SSL,//ssl tls
            'port' => EMAIL_PORT, //25 587
            'auth' => 'login',
            'username' => EMAIL_USER,
            'password' => EMAIL_PASS
        );
        //$mailbody = $_SERVER["SERVER_NAME"];
        $transport = new Zend_Mail_Transport_Smtp(EMAIL_SMTP, $config);
        $mail = new Zend_Mail('utf-8');
        // $mail->setBodyText('My Nice Test Text');
        $mail->setBodyHtml($mailbody);
        $mail->setFrom(EMAIL_ADD, EMAIL_USER);
        $mail->addTo($toemail, $toemailusername);
        $mail->setSubject($mail_title);
        $mail->send($transport);
    }

}
