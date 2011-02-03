<?php

class Tdxio_SendMail{
     

    public static function sendFeedback($subject,$body,$from) {
        $configuration = new Zend_Config_Ini(
            APPLICATION_PATH . '/configs/application.ini',
            APPLICATION_ENV
        );
        //print_r($configuration->mail->smtp->server);
        $config = array();/*'auth' => 'login',
                        'username' => 'noreply@porphyry.org',
                        'password' => '8t3n8');*/
        if (isset($configuration->mail->smtp->auth)) {
            $config['auth']=$configuration->mail->smtp->auth;
            if ($config['auth']=='login') {
                $config['username']=$configuration->mail->smtp->username;
                $config['password']=$configuration->mail->smtp->password;
            }
        }

        $transport = new Zend_Mail_Transport_Smtp($configuration->mail->smtp->server, $config);

        $mail = new Zend_Mail('UTF8');
        $mail->setBodyText($body);
        $mail->setFrom($configuration->mail->feedback->from);
        $mail->setReplyTo($from);
        $tolist=$configuration->mail->feedback->to;
        if (!$tolist) throw new Zend_Mail_Exception("bad configuration (no to), please contact system administrator");
        $tos=explode(',',$tolist);
        if (!$tos) throw new Zend_Mail_Exception("bad configuration (to list empty), please contact system administrator");
        foreach ($tos as $to) {
            $to=trim($to);
            list($addr,$name)=explode('|',$to);
            $mail->addTo(trim($addr),trim($name));
        }
        $mail->setSubject($subject);
        $mail->send($transport);
        return true;
    }
    
    
    public static function sendInscription($email,$body) {
        $configuration = new Zend_Config_Ini(
            APPLICATION_PATH . '/configs/application.ini',
            APPLICATION_ENV
        );
        $config = array();
        if (isset($configuration->mail->smtp->auth)) {
            $config['auth']=$configuration->mail->smtp->auth;
            if ($config['auth']=='login') {
                $config['username']=$configuration->mail->smtp->username;
                $config['password']=$configuration->mail->smtp->password;
            }
        }
        $transport = new Zend_Mail_Transport_Smtp($configuration->mail->smtp->server, $config);

        $mail = new Zend_Mail('UTF8');
        $mail->setBodyText($body);
        $mail->setFrom($configuration->mail->feedback->from);
        $mail->setReplyTo($email);
        $tolist=$configuration->mail->feedback->to;
        if (!$tolist) throw new Zend_Mail_Exception("bad configuration (no to), please contact system administrator");
        $tos=explode(',',$tolist);
        if (!$tos) throw new Zend_Mail_Exception("bad configuration (to list empty), please contact system administrator");
        foreach ($tos as $to) {
            $to=trim($to);            
            list($addr,$name)=explode('|',$to);
            $mail->addTo(trim($addr),trim($name));
        }
        $mail->setSubject('Inscription to TraduXio');
        $mail->send($transport);
        return true;
    }
}
