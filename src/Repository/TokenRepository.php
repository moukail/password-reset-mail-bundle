<?php

namespace Moukail\PasswordResetMailBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use Moukail\CommonToken\Entity\TokenInterface;
use Moukail\CommonToken\Repository\TokenRepositoryInterface;
use Moukail\CommonToken\Repository\TokenRepositoryTrait;
use Moukail\PasswordResetMailBundle\Entity\Token;

/**
 * @method Token|null find($id, $lockMode = null, $lockVersion = null)
 * @method Token|null findOneBy(array $criteria, array $orderBy = null)
 * @method Token[]    findAll()
 * @method Token[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenRepository extends ServiceEntityRepository implements TokenRepositoryInterface
{
    use TokenRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Token::class);
    }

    public function createTokenEntity(object $user, \DateTimeInterface $expiresAt, string $token): TokenInterface
    {
        return new Token($user, $expiresAt, $token);
    }
}
