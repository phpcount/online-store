<?php

namespace App\Utils\Mailer\Sender;

use App\Entity\User;
use App\Utils\Mailer\DTO\MailerOptions;
use App\Utils\Mailer\MailerSender;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

class ResetUserPasswordEmailSender
{
    /**
     * @var MailerSender
     */
    private $mailerSender;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(MailerSender $mailerSender, UrlGeneratorInterface $urlGenerator)
    {
        $this->mailerSender = $mailerSender;
        $this->urlGenerator = $urlGenerator;
    }

    public function sendEmailToClient(User $user, ResetPasswordToken $resetPasswordToken)
    {
        $subject = 'Online Store shop - Your password reset request';

        $context = [
            'resetToken' => $resetPasswordToken,
            'user' => $user,
            'profileUrl' => $this->urlGenerator->generate('main_profile_index', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        $mailerOptions = new MailerOptions();
        $mailerOptions
            ->setRecipient($user->getEmail())
            ->setSubject($subject)
            ->setHtmlTemplate('main/email/security/reset_password.html.twig')
            ->setContext($context)
        ;

        $this->mailerSender->sendTemplatedEmail($mailerOptions);
    }
}
