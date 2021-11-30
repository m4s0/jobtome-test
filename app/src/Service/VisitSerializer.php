<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Visit;
use DateTimeInterface;

class VisitSerializer
{
    public static function serializeCollection(array $visits): array
    {
        $visitsSerialized = [];
        foreach ($visits as $visit) {
            $visitsSerialized[] = self::serialize($visit);
        }

        return [
            'count' => count($visits),
            'visits' => $visitsSerialized,
        ];
    }

    public static function serialize(Visit $visit): array
    {
        return [
            'id' => $visit->getId(),
            'date' => $visit->getDate()->format(DateTimeInterface::ATOM),
            'referer' => $visit->getReferer(),
            'ip' => $visit->getIp(),
            'userAgent' => $visit->getUserAgent(),
        ];
    }
}