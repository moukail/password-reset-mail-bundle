<?php

namespace Moukail\PasswordResetMailBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Moukail\CommonToken\Entity\TokenInterface;

class Token implements TokenInterface
{
    private int $id;

    private UserInterface $user;

    private string $token;

    private \DateTimeImmutable $createdAt;

    private \DateTimeInterface $expiresAt;

    public function __construct(UserInterface $user, \DateTimeInterface $expiresAt, string $token)
    {
        $this->user = $user;
        $this->token = $token;
        $this->createdAt = new \DateTimeImmutable('now');
        $this->expiresAt = $expiresAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt->getTimestamp() <= \time();
    }

    public function getExpiresAt(): \DateTimeInterface
    {
        return $this->expiresAt;
    }
}
