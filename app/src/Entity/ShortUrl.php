<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ShortUrl
{
    private ?int $id = null;

    private string $originalUrl;

    private string $shortCode;

    private \DateTimeImmutable $createdAt;

    private ?\DateTimeImmutable $validSince;

    private ?\DateTimeImmutable $validUntil;

    private ?int $maxVisits;

    private Collection $visits;

    public function __construct(
        string $originalUrl,
        string $shortCode,
        ?\DateTimeImmutable $validSince = null,
        ?\DateTimeImmutable $validUntil = null,
        ?int $maxVisits = null
    ) {
        if (null !== $maxVisits && $maxVisits < 1) {
            throw new \InvalidArgumentException('maxVisits must be greater than 0');
        }
        if ($validSince && $validUntil && $validSince > $validUntil) {
            throw new \InvalidArgumentException('validSince must be before validUntil');
        }

        $this->createdAt = new \DateTimeImmutable();
        $this->originalUrl = $originalUrl;
        $this->shortCode = $shortCode;
        $this->validSince = $validSince;
        $this->validUntil = $validUntil;
        $this->maxVisits = $maxVisits;
        $this->visits = new ArrayCollection();
    }

    public function addVisit(Visit $visit): void
    {
        if ($visit->getShortUrl() !== $this) {
            throw new \InvalidArgumentException('Cannot add Visit as this is not its parent.');
        }

        if (!$this->visits->contains($visit)) {
            $this->visits->add($visit);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOriginalUrl(): string
    {
        return $this->originalUrl;
    }

    public function getShortCode(): string
    {
        return $this->shortCode;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
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

    public function getVisits(): Collection
    {
        return $this->visits;
    }
}
