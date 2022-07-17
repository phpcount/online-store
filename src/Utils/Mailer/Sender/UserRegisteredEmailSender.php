<?php

namespace App\Utils\Mailer\Sender;

use App\Entity\User;
use App\Utils\Mailer\DTO\MailerOptions;
use App\Utils\Mailer\MailerSender;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;

class UserRegisteredEmailSender
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

    public function sendEmailToClient(User $user, VerifyEmailSignatureComponents $signatureComponents)
    {
        $subject = 'Please confirm your email';

        $context = [
            'signedUrl' => $signatureComponents->getSignedUrl(),
            'expiresAtMessageKey' => $signatureComponents->getExpirationMessageKey(),
            'expiresAtMessageData' => $signatureComponents->getExpirationMessageData(),
            'h1Title' => $subject,
            'user' => $user,
            'profileUrl' => $this->urlGenerator->generate('main_profile_index', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        $mailerOptions = new MailerOptions();
        $mailerOptions
            ->setRecipient($user->getEmail())
            ->setSubject($subject)
            ->setHtmlTemplate('main/email/security/confirmation_email.html.twig')
            ->setContext($context)
        ;

        $this->mailerSender->sendTemplatedEmail($mailerOptions);
    }
}
