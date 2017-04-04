<?php

namespace lb\components\traits\lb;

use RequestKit;

trait Session
{
    // Get Session Value
    public function getSession($session_key)
    {
        if ($this->isSingle()) {
            return RequestKit::getSession($session_key);
        }
        return false;
    }

    // Set Session Value
    public function setSession($session_key, $session_value)
    {
        if ($this->isSingle()) {
            $_SESSION[$session_key] = $session_value;
        }
    }

    // Delete Session
    public function delSession($session_key)
    {
        if ($this->isSingle()) {
            if (isset($_SESSION[$session_key])) {
                unset($_SESSION[$session_key]);
            }
        }
    }

    // Delete Multi Sessions
    public function delSessions($session_keys)
    {
        if ($this->isSingle()) {
            foreach ($session_keys as $session_key) {
                $this->delSession($session_key);
            }
        }
    }
}
