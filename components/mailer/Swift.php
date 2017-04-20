<?php

namespace lb\components\mailer;

use lb\BaseClass;
use lb\Lb;

class Swift extends BaseClass
{
    const DEFAULT_PORT = 25;

    public $containers = [];
    public $transport;
    protected $_smtp;
    protected $_smtp_port = self::DEFAULT_PORT;
    protected $_username;
    protected $_password;

    protected static $instance;

    public function __clone()
    {
        //
    }

    public function __construct($containers)
    {
        $this->containers = $containers;
        if (isset($this->containers['config'])) {
            $swift_config = $this->containers['config']->get('swift');
            if ($swift_config) {
                $this->_smtp = isset($swift_config['smtp']) ? $swift_config['smtp'] : '';
                $this->_smtp_port = isset($swift_config['smtp_port']) ? $swift_config['smtp_port'] : $this->_smtp_port;
                $this->_username = isset($swift_config['username']) ? $swift_config['username'] : '';
                $this->_password = isset($swift_config['password']) ? $swift_config['password'] : '';
                $this->getConnection();
            }
        }
    }

    protected function getConnection()
    {
        $this->transport = \Swift_SmtpTransport::newInstance($this->_smtp, $this->_smtp_port);
        $this->transport->setUsername($this->_username);
        $this->transport->setPassword($this->_password);
    }

    /**
     * @param array $containers
     * @param bool $reset
     * @return Swift
     */
    public static function component($containers = [], $reset = false)
    {
        if (static::$instance instanceof static) {
            return $reset ? (static::$instance = new static($containers ? : Lb::app()->containers)) : static::$instance;
        } else {
            return (static::$instance = new static($containers ? : Lb::app()->containers));
        }
    }

    /**
     * Send Email
     *
     * @param $from_name
     * @param array $receivers
     * @param $successfulRecipients
     * @param $failedRecipients
     * @param string $subject
     * @param string $body
     * @param string $content_type
     * @param string $charset
     * @return bool
     */
    public function send(
        $from_name,
        array $receivers,
        &$successfulRecipients,
        &$failedRecipients,
        $subject = '',
        $body = '',
        $content_type = 'text/html',
        $charset = 'UTF-8'
    )
    {
        $mailer = \Swift_Mailer::newInstance($this->transport);
        $message = \Swift_Message::newInstance();
        $message->setFrom([$this->_username => $from_name]);
        $message->setTo($receivers);
        $message->setSubject($subject);
        $message->setBody($body, $content_type, $charset);
        $successfulRecipients = $mailer->send($message, $failedRecipients);
        return true;
    }
}
