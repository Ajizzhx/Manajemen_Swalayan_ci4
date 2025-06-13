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
    public string $SMTPPass   = 'mqqk pufy qran cwut'; // app password gmail
    public int    $SMTPPort   = 465; 
    public string $SMTPCrypto = 'ssl'; 
    public string $mailType   = 'html';
    
    public string $userAgent = 'CodeIgniter';
    public string $mailPath = '/usr/sbin/sendmail';
    public int    $SMTPTimeout = 60;
    public bool   $SMTPKeepAlive = true;
    public bool   $wordWrap = true;
    public int    $wrapChars = 76;
    public string $charset = 'UTF-8';
    public bool   $validate = true;
    public int    $priority = 3;
    public string $CRLF = "\r\n";
    public string $newline = "\r\n";
    public bool   $BCCBatchMode = false;
    public int    $BCCBatchSize = 200;
    public bool   $DSN = false;
    public int    $SMTPDebug = 2; // Enable SMTP debugging
    public bool   $debug = true; // Enable general debugging
}
