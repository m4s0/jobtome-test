<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ShortUrl;
use App\Repository\ShortUrlRepository;

class Shortener
{
    private GenerateRandomShortCode $generateRandomShortCode;
    private ShortUrlRepository $shortUrlRepository;
    private int $shortCodeLength;

    public function __construct(
        ShortUrlRepository $shortUrlRepository,
        GenerateRandomShortCode $generateRandomShortCode,
        int $shortCodeLength
    ) {
        $this->shortUrlRepository = $shortUrlRepository;
        $this->generateRandomShortCode = $generateRandomShortCode;
        $this->shortCodeLength = $shortCodeLength;
    }

    public function execute(
        string $originalUrl,
        ?\DateTimeImmutable $validSince = null,
        ?\DateTimeImmutable $validUntil = null,
        ?int $maxVisits = null
    ): ShortUrl {
        $existingShortUrl = $this->shortUrlRepository->findByOriginalUrl($originalUrl);
        if (null !== $existingShortUrl) {
            throw new \RuntimeException('Short URL already exists');
        }

        $shortCode = $this->generateRandomShortCode->execute($this->shortCodeLength);

        return new ShortUrl(
            $originalUrl,
            $shortCode,
            $validSince,
            $validUntil,
            $maxVisits
        );
    }
}
