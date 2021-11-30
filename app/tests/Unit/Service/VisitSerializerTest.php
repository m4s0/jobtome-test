<?php
declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\ShortUrl;
use App\Entity\Visit;
use App\Service\VisitSerializer;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use SlopeIt\ClockMock\ClockMock;

class VisitSerializerTest extends TestCase
{
    protected function setUp(): void
    {
        ClockMock::freeze(new \DateTime('2021-01-01 00:00:00'));

        parent::setUp();
    }

    /** @test */
    public function it_will_serialize_single_item(): void
    {
        $shortUrl = new ShortUrl(
            'https://www.domain.com/test-url',
            '123abc'
        );

        $visit = new Visit(
            $shortUrl,
            'referer',
            '127.0.0.1',
            'user-agent'
        );

        $visitSerialized = VisitSerializer::serialize($visit);

        Assert::assertEquals(
            [
                'id' => null,
                'date' => '2021-01-01T00:00:00+01:00',
                'referer' => 'referer',
                'ip' => '127.0.0.1',
                'userAgent' => 'user-agent',
            ],
            $visitSerialized
        );
    }

    /** @test */
    public function it_will_serialize_a_collection(): void
    {
        $shortUrl = new ShortUrl(
            'https://www.domain.com/test-url',
            '123abc'
        );

        $visit1 = new Visit(
            $shortUrl,
            'referer-1',
            '127.0.0.1',
            'user-agent-1'
        );

        $visit2 = new Visit(
            $shortUrl,
            'referer-2',
            '127.0.0.2',
            'user-agent-2'
        );

        $collection = new ArrayCollection();
        $collection->add($visit1);
        $collection->add($visit2);

        $visitsSerialized = VisitSerializer::serializeCollection($collection->toArray());

        Assert::assertEquals(
            [
                'count' => 2,
                'visits' => [
                    [
                        'id'        => null,
                        'date'      => '2021-01-01T00:00:00+01:00',
                        'referer'   => 'referer-1',
                        'ip'        => '127.0.0.1',
                        'userAgent' => 'user-agent-1',
                    ],
                    [
                        'id'        => null,
                        'date'      => '2021-01-01T00:00:00+01:00',
                        'referer'   => 'referer-2',
                        'ip'        => '127.0.0.2',
                        'userAgent' => 'user-agent-2',
                    ]
                ]
            ],
            $visitsSerialized
        );
    }
}
