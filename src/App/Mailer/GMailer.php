<?php

namespace App\Mailer;

use PHPMailer\PHPMailer\PHPMailer;
use \Exception;
class GMailer extends PHPMailer
{
    function __construct($exceptions = null, $config = null)
    {
        $config = $config ?? parse_ini_file(ROOT_DIR.'/config/App.Mailer.GMailer.ini');
        if ( ! $config){
            throw new Exception('Missing configuration file config/App.Mailer.GMailer.ini, or config dependency');
        }
        $config = (object)$config;
        parent::__construct($exceptions);

        //Tell PHPMailer to use SMTP
        $this->isSMTP();

        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $this->SMTPDebug = isset($config->smtpDebug) ? $config->smtpDebug : 2;

        //Set the hostname of the mail server
        $this->Host = isset($config->host) ? $config->host : 'smtp.gmail.com';

        // use
        // $mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $this->Port = isset($config->port) ? (int)$config->port : 587;

        //Set the encryption system to use - ssl (deprecated) or tls
        $this->SMTPSecure = isset($config->smtpSecure) ? $config->smtpSecure : 'tls';

        //Whether to use SMTP authentication
        $this->SMTPAuth = isset($config->smtpAuth) ? (bool)$config->smtpAuth : true;

        //Username to use for SMTP authentication - use full email address for gmail
        $this->Username = $config->username;

        //Password to use for SMTP authentication
        $this->Password = $config->password;


    }


    /**
     * @param Email $email
     * @throws \PHPMailer\PHPMailer\Exception
     */
    function sendEmail(Email $email)
    {
        //Set who the message is to be sent from
        $from = EmailAddress::createFromString($email->from);
        $this->setFrom($from->address, $from->displayName);

        if($email->replyTo){
            $replyTo = EmailAddress::createFromString($email->replyTo);
            //Set an alternative reply-to address
            $this->addReplyTo($replyTo->address, $replyTo->displayName);
        }

        foreach($email->to as $to){
            $to = EmailAddress::createFromString($to);
            //Set who the message is to be sent to
            $this->addAddress($to->address, $to->displayName);
        }

        foreach($email->cc as $cc){
            $cc = EmailAddress::createFromString($cc);
            $this->addCC($cc->address,$cc->displayName);
        }

        foreach($email->bcc as $bcc){
            $bcc = EmailAddress::createFromString($bcc);
            $this->addBCC($bcc->address,$bcc->displayName);
        }

        //Set the subject line
        $this->Subject = $email->subject;

        if($email->htmlBody){
            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $this->msgHTML($email->htmlBody, __DIR__);
        }

        //Replace the plain text body with one created manually
        $this->AltBody = $email->body;

        foreach($email->attachments as $attachment){
            //Attach an image file
            $this->addAttachment($attachment);
        }

        //send the message, check for errors
        parent::send();

        //Section 2: IMAP
        //Uncomment these to save your message in the 'Sent Mail' folder.
        #if ($this->save_mail($this)) {
        #    echo "Message saved!";
        #}
        #

    }


    //Section 2: IMAP
    //IMAP commands requires the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
    //Function to call which uses the PHP imap_*() functions to save messages: https://php.net/manual/en/book.imap.php
    //You can use imap_getmailboxes($imapStream, '/imap/ssl', '*' ) to get a list of available folders or labels, this can
    //be useful if you are trying to get this working on a non-Gmail IMAP server.
    protected function save_mail(PHPMailer $mail)
    {
        //You can change 'Sent Mail' to any other folder or tag
        $path = "{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail";

        //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
        $imapStream = imap_open($path, $mail->Username, $mail->Password);
        $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
        imap_close($imapStream);
        return $result;
    }
}