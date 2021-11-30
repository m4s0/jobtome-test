<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\ShortUrl;
use DateTimeInterface;

class ShortUrlSerializer
{
    public static function serializeCollection(array $shortUrls, string $baseName): array
    {
        $shortUrlSerialized = [];
        foreach ($shortUrls as $shortUrl) {
            $shortUrlSerialized[] = self::serialize($shortUrl, $baseName);
        }

        return [
            'count' => count($shortUrls),
            'short_urls' => $shortUrlSerialized,
        ];
    }

    public static function serialize(ShortUrl $shortUrl, string $baseName): array
    {
        return [
            'id' => $shortUrl->getId(),
            'short_code' => $shortUrl->getShortCode(),
            'short_url' => $baseName . $shortUrl->getShortCode(),
            'url' => $shortUrl->getOriginalUrl(),
            'created_at' => $shortUrl->getCreatedAt()->format(DateTimeInterface::ATOM),
            'visits' => $shortUrl->getVisits()->count(),
            'valid_since' => $shortUrl->getValidSince()?->format(DateTimeInterface::ATOM),
            'valid_until' => $shortUrl->getValidUntil()?->format(DateTimeInterface::ATOM),
            'max_visits' => $shortUrl->getMaxVisits(),
        ];
    }
}