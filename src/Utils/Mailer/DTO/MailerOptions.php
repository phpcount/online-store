<?php

namespace App\Utils\Mailer\DTO;

class MailerOptions
{
    /**
     * @var string
     */
    private $recipient;

    /**
     * @var ?string
     */
    private $cc;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $htmlTemplate;

    /**
     * @var array
     */
    private $context;

    /**
     * @var string
     */
    private $text;

    /**
     * Get the value of recipient.
     *
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Set the value of recipient.
     *
     * @return self
     */
    public function setRecipient(string $recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Get the value of cc.
     */
    public function getCc(): ?string
    {
        return $this->cc;
    }

    /**
     * Set the value of cc.
     *
     * @return self
     */
    public function setCc(string $cc)
    {
        $this->cc = $cc;

        return $this;
    }

    /**
     * Get the value of subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set the value of subject.
     *
     * @return self
     */
    public function setSubject(string $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get the value of htmlTemplate.
     *
     * @return string
     */
    public function getHtmlTemplate()
    {
        return $this->htmlTemplate;
    }

    /**
     * Set the value of htmlTemplate.
     *
     * @return self
     */
    public function setHtmlTemplate(string $htmlTemplate)
    {
        $this->htmlTemplate = $htmlTemplate;

        return $this;
    }

    /**
     * Get the value of context.
     *
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set the value of context.
     *
     * @return self
     */
    public function setContext(array $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get the value of text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set the value of text.
     *
     * @return self
     */
    public function setText(string $text)
    {
        $this->text = $text;

        return $this;
    }
}
