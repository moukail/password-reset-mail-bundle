<?php

namespace Moukail\PasswordResetMailBundle\Message;

class PasswordResetMail
{
    private $email;
    private $context;

    public function __construct(string $email, array $context)
    {
        $this->email = $email;
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
