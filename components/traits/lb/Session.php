<?php

namespace lb\components\traits\lb;

use RequestKit;
use ResponseKit;

trait Session
{
    /**
     * Get Session Value
     *
     * @param $session_key
     * @return bool
     */
    public function getSession($session_key)
    {
        if ($this->isSingle()) {
            return RequestKit::getSession($session_key);
        }
        return false;
    }

    /**
     * Set Session Value
     *
     * @param $session_key
     * @param $session_value
     */
    public function setSession($session_key, $session_value)
    {
        if ($this->isSingle()) {
            ResponseKit::setSession($session_key, $session_value);
        }
    }

    /**
     * Delete Session
     *
     * @param $session_key
     */
    public function delSession($session_key)
    {
        if ($this->isSingle()) {
            ResponseKit::delSession($session_key);
        }
    }

    /**
     * Delete Multi Sessions
     *
     * @param $session_keys
     */
    public function delSessions($session_keys)
    {
        if ($this->isSingle()) {
            foreach ($session_keys as $session_key) {
                $this->delSession($session_key);
            }
        }
    }
}
