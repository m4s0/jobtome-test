<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ShortUrl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShortUrl>
 */
class ShortUrlRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShortUrl::class);
    }

    public function findByShortCode(string $shortCode): ?ShortUrl
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.shortCode = :shortCode')
            ->setParameter('shortCode', $shortCode);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findByOriginalUrl(string $originalUrl): ?ShortUrl
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.originalUrl = :originalUrl')
            ->setParameter('originalUrl', $originalUrl);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
