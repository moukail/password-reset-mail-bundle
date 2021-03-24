<?php

namespace Moukail\PasswordResetMailBundle\MessageHandler;

use Moukail\PasswordResetMailBundle\Message\PasswordResetMail;
use Psr\Log\LoggerInterface;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Address;

class PasswordResetMailHandler implements MessageHandlerInterface
{
    private MailerInterface $mailer;
    private LoggerInterface $logger;
    private string $fromAddress;
    private string $fromName;

    /**
     * PartnerActivationMailHandler constructor.
     *
     * @param MailerInterface $mailer
     * @param LoggerInterface $logger
     * @param string $fromAddress
     * @param string $fromName
     */
    public function __construct(MailerInterface $mailer, LoggerInterface $logger, string $fromAddress, string $fromName)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
    }

    public function __invoke(PasswordResetMail $message)
    {
        $message = (new TemplatedEmail())
            ->from(new Address($this->fromAddress, $this->fromName))
            ->to(new Address($message->getEmail()))
            ->subject('Reset your password') // todo add translation

            // path of the Twig template to render
            ->htmlTemplate('@MoukailPasswordResetMail/emails/reset-password-request.html.twig')
            ->textTemplate('@MoukailPasswordResetMail/emails/reset-password-request.text.twig')
            // pass variables (name => value) to the template
            ->context($message->getContext())
        ;

        // todo get headers from yaml config file
        $headers = $message->getHeaders();
        $headers->addTextHeader('X-Mailgun-Tag', 'PasswordResetMail');
        $headers->addTextHeader('X-Mailgun-Track-Clicks', 'yes');
        $headers->addTextHeader('X-Mailgun-Track-Opens', 'yes');
        $headers->addTextHeader('X-Mailgun-Variables', json_encode(['my_message_id' => 123]));

        try {
            $this->mailer->send($message);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('PasswordResetMailHandler::reset your password to ', ['error' => $e->getMessage()]);
        }
    }
}
