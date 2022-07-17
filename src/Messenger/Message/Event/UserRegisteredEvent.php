<?php

namespace App\Messenger\Message\Event;

class UserRegisteredEvent
{
    /**
     * @var string
     */
    private $userid;

    public function __construct(string $userid)
    {
        $this->userid = $userid;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userid;
    }
}
