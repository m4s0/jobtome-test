<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\InputParameters;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\Request;

class GetInputParameters
{
    public static function execute(Request $request): InputParameters
    {
        try {
            $decoded = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException('Error, invalid JSON');
        }

        $url = self::checkUrl($decoded);
        $validSince = self::checkValidSince($decoded);
        $validUntil = self::checkValidUntil($decoded);
        $maxVisits = self::checkMaxVisits($decoded);

        return new InputParameters($url, $validSince, $validUntil, $maxVisits);
    }

    private static function checkUrl(array $decoded): string
    {
        if (!isset($decoded['url'])) {
            throw new \RuntimeException('Error, missing url');
        }
        if (false === filter_var($decoded['url'], FILTER_VALIDATE_URL)) {
            throw new \RuntimeException('Error, invalid url');
        }

        return $decoded['url'];
    }

    private static function checkValidSince(array $decoded): ?\DateTimeImmutable
    {
        if (!isset($decoded['valid_since'])) {
            return null;
        }

        $validSince = \DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $decoded['valid_since']);
        if (false === $validSince) {
            throw new \RuntimeException('Error, invalid valid_since');
        }

        return $validSince;
    }

    private static function checkValidUntil(array $decoded): ?\DateTimeImmutable
    {
        if (!isset($decoded['valid_until'])) {
            return null;
        }

        $validUntil = \DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $decoded['valid_until']);
        if (false === $validUntil) {
            throw new \RuntimeException('Error, invalid valid_until');
        }

        return $validUntil;
    }

    private static function checkMaxVisits(array $decoded): ?int
    {
        if (!isset($decoded['max_visits'])) {
            return null;
        }

        $maxVisits = filter_var($decoded['max_visits'], FILTER_VALIDATE_INT);
        if (false === $maxVisits) {
            throw new \RuntimeException('Error, invalid max_visits');
        }

        return $maxVisits;
    }
}
