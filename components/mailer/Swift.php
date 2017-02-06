<?php
/**
 * Created by PhpStorm.
 * User: 224
 * Date: 2015/11/13
 * Time: 9:25
 * Lb framework swift mailer component file
 */

namespace lb\components\mailer;

use lb\BaseClass;
use lb\Lb;

class Swift extends BaseClass
{
    public $containers = [];
    public $transport = false;
    protected $_smtp = '';
    protected $_smtp_port = 25;
    protected $_username = '';
    protected $_password = '';
    protected static $instance = false;

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

    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    protected function getConnection()
    {
        $this->transport = \Swift_SmtpTransport::newInstance($this->_smtp, $this->_smtp_port);
        $this->transport->setUsername($this->_username);
        $this->transport->setPassword($this->_password);
    }

    public static function component($containers = [], $reset = false)
    {
        if (static::$instance instanceof static) {
            return $reset ? (static::$instance = new static($containers ? : Lb::app()->containers)) : static::$instance;
        } else {
            return (static::$instance = new static($containers ? : Lb::app()->containers));
        }
    }

    public function send($from_name, $receivers, $subject, $body, $content_type = 'text/html', $charset = 'UTF-8')
    {
        $err_msg = '';
        if ($this->transport) {
            if ($from_name && is_array($receivers) && $receivers && $subject && $body) {
                $mailer = \Swift_Mailer::newInstance($this->transport);
                $message = \Swift_Message::newInstance();
                $message->setFrom([$this->_username => $from_name]);
                $message->setTo($receivers);
                $message->setSubject($subject);
                $message->setBody($body, $content_type, $charset);
                try {
                    $mailer->send($message);
                } catch (\Swift_SwiftException $e) {
                    $err_msg = 'There was a problem communicating with SMTP: ' . $e->getMessage();
                }
            } else {
                $err_msg = 'Invalid Parameter.';
            }
        } else {
            $err_msg = 'Transport not set.';
        }
        return $err_msg;
    }
}
