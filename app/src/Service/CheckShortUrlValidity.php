<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ShortUrl;
use RuntimeException;

class CheckShortUrlValidity
{
    public static function execute(ShortUrl $shortUrl): bool
    {
        $now = new \DateTimeImmutable();

        $validSince = $shortUrl->getValidSince();
        if (null !== $validSince && $now < $validSince) {
            throw new RunTimeException('Short URL not found');
        }

        $validUntil = $shortUrl->getValidUntil();
        if (null !== $validUntil && $now > $validUntil) {
            throw new RunTimeException('Short URL not found');
        }

        $maxVisits = $shortUrl->getMaxVisits();
        if (null !== $maxVisits && $shortUrl->getVisits()->count() >= $maxVisits) {
            throw new RunTimeException('Visits to this Short URL exceed the maximum limit');
        }

        return true;
    }
}
