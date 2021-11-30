<?php

declare(strict_types=1);

namespace App\DTO;

class InputParameters
{
    private string $url;
    private ?\DateTimeImmutable $validSince;
    private ?\DateTimeImmutable $validUntil;
    private ?int $maxVisits;

    public function __construct(
        string $url,
        ?\DateTimeImmutable $validSince = null,
        ?\DateTimeImmutable $validUntil = null,
        ?int $maxVisits = null
    ) {
        $this->url = $url;
        $this->validSince = $validSince;
        $this->validUntil = $validUntil;
        $this->maxVisits = $maxVisits;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getValidSince(): ?\DateTimeImmutable
    {
        return $this->validSince;
    }

    public function getValidUntil(): ?\DateTimeImmutable
    {
        return $this->validUntil;
    }

    public function getMaxVisits(): ?int
    {
        return $this->maxVisits;
    }
}
