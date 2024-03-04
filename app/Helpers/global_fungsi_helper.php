<?php

use Config\Services;

function kirim_email($attachment, $to, $title, $message)
{
    $email          = Services::email();
    $email_pengirim = EMAIL_ALAMAT;
    $email_nama     = EMAIL_NAMA;

    $config['protocol']   = "smtp";
    $config['SMTPHost']   = "smtp.gmail.com";
    $config['SMTPUser']   = $email_pengirim;
    $config['SMTPPass']   = EMAIL_PASSWORD;
    $config['SMTPPort']   = 465;
    $config['SMTPCrypto'] = "SSL";
    $config['mailType']   = "html";
    $config['charset']    = 'UTF-8';
    $config['newline']    = "\r\n";

    $email->initialize($config);
    $email->setFrom($email_pengirim, $email_nama);
    $email->setTo($to);

    if ($attachment && file_exists($attachment)) {
        $email->attach($attachment);
    }

    $email->setSubject($title);
    $email->setMessage($message);

    if (!$email->send()) {
        return false;
    } else {
        return true;
    }
}