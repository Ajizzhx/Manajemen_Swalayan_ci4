<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = 'swalayan2fa@gmail.com'; // email pengirim
    public string $fromName   = 'Swalayan 2FA';
    public string $protocol   = 'smtp';
    public string $SMTPHost   = 'smtp.gmail.com';
    public string $SMTPUser   = 'swalayan2fa@gmail.com'; // email pengirim
    public string $SMTPPass   = 'ijyg iqqf yjan rmco'; // app password gmail
    public int    $SMTPPort   = 587;
    public string $SMTPCrypto = 'tls';
    public string $mailType   = 'html';

    public string $userAgent = 'CodeIgniter';
    public string $mailPath = '/usr/sbin/sendmail';
    public int $SMTPTimeout = 5;
    public bool $SMTPKeepAlive = false;
    public bool $wordWrap = true;
    public int $wrapChars = 76;
    public string $charset = 'UTF-8';
    public bool $validate = false;
    public int $priority = 3;
    public string $CRLF = "\r\n";
    public string $newline = "\r\n";
    public bool $BCCBatchMode = false;
    public int $BCCBatchSize = 200;
    public bool $DSN = false;
}
