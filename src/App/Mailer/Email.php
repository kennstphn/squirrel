<?php

namespace App\Mailer;


class Email
{
    /**
     * @var string
     */
    public $from;
    public $replyTo;
    public $subject;
    public $body;
    public $htmlBody;

    /**
     * @var array
     */
    public $to=[];
    public $cc=[];
    public $bcc=[];
    public $attachments=[];

    static function create($from, array $to, string $body, $subject = null){
        $me = new self;
        $me->from = $from;
        $me->to = $to;
        $me->body = $body;
        $me->subject = $subject;

        return $me;
    }
}