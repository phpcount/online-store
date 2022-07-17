<?php

namespace App\Messenger\MessageHandler\Command;

use App\Entity\User;
use App\Messenger\Message\Command\ResetUserPasswordEvent;
use App\Utils\Mailer\Sender\ResetUserPasswordEmailSender;
use App\Utils\Manager\UserManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class ResetUserPasswordHandler implements MessageHandlerInterface
{
    /**
     * @var ResetPasswordHelperInterface
     */
    private $resetPasswordHelper;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var ResetUserPasswordEmailSender
     */
    private $emailSender;

    public function __construct(UserManager $userManager, ResetUserPasswordEmailSender $emailSender, ResetPasswordHelperInterface $resetPasswordHelper)
    {
        $this->userManager = $userManager;
        $this->emailSender = $emailSender;
        $this->resetPasswordHelper = $resetPasswordHelper;
    }

    public function __invoke(ResetUserPasswordEvent $event)
    {
        $email = $event->getEmail();
        $resetToken = null;

        /** @var User $user */
        $user = $this->userManager->getRepository()->findOneBy(['email' => $email]);
        if (!$user) {
            return;
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $ex) {
        }

        $this->emailSender->sendEmailToClient($user, $resetToken);
    }
}
