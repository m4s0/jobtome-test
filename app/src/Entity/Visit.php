<?php

declare(strict_types=1);

namespace App\Entity;

class Visit
{
    private ?int $id = null;

    private \DateTimeImmutable $date;

    private ?string $referer;

    private ?string $ip;

    private ?string $userAgent;

    private ShortUrl $shortUrl;

    public function __construct(
        ShortUrl $shortUrl,
        ?string $referer,
        ?string $ip,
        ?string $userAgent
    ) {
        $this->shortUrl = $shortUrl;
        $this->date = new \DateTimeImmutable();
        $this->referer = $referer;
        $this->ip = $ip;
        $this->userAgent = $userAgent;

        $shortUrl->addVisit($this);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getReferer(): ?string
    {
        return $this->referer;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function getShortUrl(): ShortUrl
    {
        return $this->shortUrl;
    }
}
