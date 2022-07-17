<?php

namespace App\Exception\Security;

use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

class EmptyUserPlainPasswordException extends InvalidArgumentException
{
}
