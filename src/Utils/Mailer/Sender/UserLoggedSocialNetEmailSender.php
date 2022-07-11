<?php

namespace App\Utils\Mailer\Sender;

use App\Entity\User;
use App\Utils\Mailer\DTO\MailerOptions;
use App\Utils\Mailer\MailerSender;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserLoggedSocialNetEmailSender
{

    /**
     *
     * @var MailerSender
     */
    private $mailerSender;

    /**
     *
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(MailerSender $mailerSender, UrlGeneratorInterface $urlGenerator)
    {
        $this->mailerSender = $mailerSender;
        $this->urlGenerator = $urlGenerator;
    }



    public function sendEmailToClient(User $user, string $plainPassword)
    {
        $mailerOptions = new MailerOptions();

        $subject = 'OnlineStore Shop - Your new password';
        $mailerOptions
            ->setRecipient($user->getEmail())
            ->setSubject($subject)
            ->setHtmlTemplate('main/email/client/user_logged_social_net.html.twig')
            ->setContext([
                'user' => $user,
                'plainPassword' => $plainPassword,
                'h1Title' => $subject,
                'profileUrl' => $this->urlGenerator->generate('main_profile_index', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ])
        ;

        $this->mailerSender->sendTemplatedEmail($mailerOptions);
    }
}
