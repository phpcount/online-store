<?php

namespace App\Messenger\Message\Command;

class ResetUserPasswordEvent
{
    /**
     *
     * @var string
     */
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
