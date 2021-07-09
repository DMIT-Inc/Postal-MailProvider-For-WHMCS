<?php

namespace WHMCS\Module\Mail;

require __DIR__ . '/vendor/autoload.php';

use WHMCS\Exception\Mail\SendFailure;
use WHMCS\Exception\Module\InvalidConfiguration;
use WHMCS\Mail\Message;
use WHMCS\Module\Contracts\SenderModuleInterface;
use WHMCS\Module\MailSender\DescriptionTrait;
use WHMCS\Database\Capsule;
use Postal;

class PostalMail implements SenderModuleInterface
{
    use DescriptionTrait;
    
    /**
     * Constructor
     *
     * Any instance of a mail module should have the display name at the ready.
     * Therefore it is recommend to ensure these
     * values are set during object instantiation.
     *
     * @see \WHMCS\Module\MailSender\DescriptionTrait::setDisplayName()
     */
    public function __construct()
    {
        $this->setDisplayName('PostalMail');
    }

    /**
     * An array of configuration options for the Mail Provider.
     *
     * @return array
     */
    public function settings()
    {
        return [
            'server' => [
                'FriendlyName' => 'Postal Server Address',
                'Type' => 'text',
                'Description' => 'Address of the Postal server.',
            ],
            'api_key' => [
                'FriendlyName' => 'Postal API Key',
                'Type' => 'password',
                'Description' => 'The access key for the Postal API.',
            ],
        ];
    }

    /**
     * Test the connection to the Mail Provider.
     *
     * @param array $params Module configuration parameters.
     * @throws InvalidConfiguration On error, InvalidConfiguration will be thrown.
     */
    public function testConnection(array $params)
    {
        $client = new Postal\Client($params['server'], $params['api_key']);

        // Create a new message
        $message = new Postal\SendMessage($client);

        // Set the sender
        $message->sender($GLOBALS['CONFIG']['Email']);

        // Get Admin ID
        $adminid = $_SESSION['adminid'];
        $adminemail = Capsule::table('tbladmins')->where('id', $adminid)->value('email');
        // Add some recipients
        $message->to($adminemail);

        // Specify who the message should be from. This must be from a verified domain
        // on your mail server.
        $message->from($GLOBALS['CONFIG']['CompanyName'] . ' <' . $GLOBALS['CONFIG']['Email'] . '>');

        // Set the subject
        $message->subject('Postal Email Server Connection Test.');

        // Set the content for the e-mail
        $message->plainBody('When you receive this email, it means that you can connect to this postal server.');
        $message->htmlBody('<p>When you receive this email, it means that you can connect to this postal server.</p>');

        // Send the message and get the result
        $result = $message->send();

        return $result;
    }

    /**
     * Send an email.
     *
     * @param array $params Module configuration parameters.
     * @param Message $message The Message object containing details specific to the message.
     *
     * @return void
     * @throws SendFailure
     */
    public function send(array $params, Message $message)
    {
        // Get parameters
        $subject = $message->getSubject();
        $body = $message->getBody();
        $plainTextBody = $message->getPlainText();

        $replyTo = '';
        if ($message->getReplyTo()) {
            $replyTo = $message->getReplyTo();
        }

        $client = new Postal\Client($params['server'], $params['api_key']);

        // Create a new message
        $sendMessage = new Postal\SendMessage($client);

        // Retrieve recipients.
        foreach ($message->getRecipients('to') as $to) {
            $sendMessage->to($to[0]);
        }
        foreach ($message->getRecipients('cc') as $to) {
            $sendMessage->cc($to[0]);
        }
        foreach ($message->getRecipients('bcc') as $to) {
            $sendMessage->bcc($to[0]);
        }

        // Specify who the message should be from. This must be from a verified domain
        // on your mail server.
        $sendMessage->from($message->getFromName() . ' <' . $message->getFromEmail() . '>');
        // Set the sender
        $sendMessage->sender($message->getFromEmail());
        // Set the subject
        $sendMessage->subject($subject);

        // Set the content for the e-mail
        $sendMessage->plainBody($plainTextbody);
        $sendMessage->htmlBody($body);

        // Set the replyTo
        $sendMessage->replyTo($replyTo);

        // Set attachments
        foreach ($message->getAttachments() as $attachment) {
            if (array_key_exists('data', $attachment)) {
                $sendMessage->attach($attachment['filename'], 'text/plain', $attachment['data']);
            } else {
                $sendMessage->attach($attachment['filename'], 'text/plain', $attachment['filepath']);
            }
        }

        // Send the message and get the result
        $result = $sendMessage->send();

        return $result;
    }
}