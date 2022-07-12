<?php

namespace App\Utils\Mailer;

use App\Utils\Mailer\DTO\MailerOptions;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailerSender
{

    /**
     *
     * @var MailerInterface
     */
    private $mailer;

    /**
     *
     * @var LoggerInterface
     */
    private $logger;


    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public function sendTemplatedEmail(MailerOptions $mailerOptions)
    {
        $email = new TemplatedEmail();
        $email
            ->to($mailerOptions->getRecipient())

            ->subject($mailerOptions->getSubject())
            ->htmlTemplate($mailerOptions->getHtmlTemplate())
            ->context($mailerOptions->getContext())
        ;

        if ($mailerOptions->getCc()) {
            $email->cc($mailerOptions->getCc());
        }

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $ex) {
            $errorText = $ex->getTraceAsString();
            $this->logger->critical($mailerOptions->getSubject(), compact('errorText'));

            $this->sendSystemEmail($errorText);
        }
    }

    /**
     *
     * @param string $errorText
     * @return void
     */
    private function sendSystemEmail($errorText)
    {
        $mailerOptions = new MailerOptions();
        $mailerOptions
            ->setSubject("[Exception] An error occured while sending the letter")
            ->setRecipient('admin@online-store.com')
            ->setText($errorText);
        ;

        $email = new TemplatedEmail();
        $email
            ->to($mailerOptions->getRecipient())
            ->subject($mailerOptions->getSubject())
            ->text($mailerOptions->getText())
        ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $ex) {
            $errorText = $ex->getTraceAsString();
            $this->logger->critical($mailerOptions->getSubject(), compact('errorText'));
        }
    }



}
