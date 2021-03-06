<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:50
 */

namespace Omnibus\Core\Utility;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;


/**
 * Class Email
 * @package Omnibus\Core\Utility
 */
class Email
{
    private $template_root;

    /** @var PHPMailer $mailer */
    private $mailer;

    /** @var string $to_email */
    private $to_email;

    /** @var string $to_name */
    private $to_name;

    /** @var string $from_email */
    private $from_email;

    /** @var string $from_name */
    private $from_name;

    /** @var string $subject */
    private $subject;

    /** @var string $body */
    private $body;

    /**
     * Email constructor.
     */
    public function __construct()
    {
        $this->template_root = dirname(__DIR__, 3).'/public/assets/static/emails';
        $this->mailer = new PHPMailer();
    }


    /**
     * @param string $template
     * @param array $data
     * @return Email
     */
    public function setBody(string $template, array $data): Email
    {
        $this->body = $this->renderTemplate($template, $data);
        return $this;
    }

    /**
     * @param string $to_email
     * @return Email
     */
    public function setToEmail(string $to_email): Email
    {
        $this->to_email = $to_email;
        return $this;
    }

    /**
     * @param string $to_name
     * @return Email
     */
    public function setToName(string $to_name): Email
    {
        $this->to_name = $to_name;
        return $this;
    }

    /**
     * @param string $from_email
     * @return Email
     */
    public function setFromEmail(string $from_email): Email
    {
        $this->from_email = $from_email;
        return $this;
    }

    /**
     * @param string $from_name
     * @return Email
     */
    public function setFromName(string $from_name): Email
    {
        $this->from_name = $from_name;
        return $this;
    }

    /**
     * @param string $subject
     * @return Email
     */
    public function setSubject(string $subject): Email
    {
        $this->subject = $subject;
        return $this;
    }


    /**
     * @return string|null
     */
    public function Send(): ?string
    {
        $mail = $this->mailer;

        $mail->isHTML();
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'BASE64';

        $mail->From     = $this->from_email;
        $mail->FromName = $this->from_name;

        $mail->addAddress($this->to_email, $this->to_name);
        $mail->addReplyTo($this->from_email, $this->from_name);

        $mail->Subject = $this->subject;
        $mail->Body    = $this->body;

        try {
            if (!$mail->send()) {
                return $mail->ErrorInfo;
            } else {
                return null;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    protected function renderTemplate(string $template, array $data): string
    {
        $file = file_get_contents($this->template_root.'/'.$template.'.html');
        foreach ($data as $key => $value) {
            $file = str_replace("{{ $key }}", $value, $file);
        }
        return $file;
    }
}
