<?php
declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\ShortUrl;
use App\Service\ShortUrlSerializer;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use SlopeIt\ClockMock\ClockMock;

class ShortUrlSerializerTest extends TestCase
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
            '123abc',
            new \DateTimeImmutable('2021-01-01 00:00:00'),
            new \DateTimeImmutable('2021-01-02 00:00:00'),
            1
        );

        $shortUrlSerialized = ShortUrlSerializer::serialize($shortUrl, 'http://example.com');

        Assert::assertEquals(
            [
            'id'          => null,
            'short_code'  => '123abc',
            'short_url'   => 'http://example.com123abc',
            'url'         => 'https://www.domain.com/test-url',
            'created_at'  => '2021-01-01T00:00:00+01:00',
            'visits'      => 0,
            'valid_since' => '2021-01-01T00:00:00+01:00',
            'valid_until' => '2021-01-02T00:00:00+01:00',
            'max_visits'  => 1
            ],
            $shortUrlSerialized
        );
    }

    /** @test */
    public function it_will_serialize_a_collection(): void
    {
        $shortUrl1 = new ShortUrl(
            'https://www.domain.com/test-url-1',
            '123abc',
            new \DateTimeImmutable('2021-01-01 00:00:00'),
            new \DateTimeImmutable('2021-01-02 00:00:00'),
            1
        );

        $shortUrl2 = new ShortUrl(
            'https://www.domain.com/test-url-2',
            '456DEF',
            new \DateTimeImmutable('2021-01-03 00:00:00'),
            new \DateTimeImmutable('2021-01-04 00:00:00'),
            1
        );

        $collection = new ArrayCollection();
        $collection->add($shortUrl1);
        $collection->add($shortUrl2);

        $shortUrlsSerialized = ShortUrlSerializer::serializeCollection($collection->toArray(), 'http://example.com');

        Assert::assertEquals(
            [
                'count' => 2,
                'short_urls' => [
                    [
                        'id'          => null,
                        'short_code'  => '123abc',
                        'short_url'   => 'http://example.com123abc',
                        'url'         => 'https://www.domain.com/test-url-1',
                        'created_at'  => '2021-01-01T00:00:00+01:00',
                        'visits'      => 0,
                        'valid_since' => '2021-01-01T00:00:00+01:00',
                        'valid_until' => '2021-01-02T00:00:00+01:00',
                        'max_visits'  => 1
                    ],
                    [
                        'id'          => null,
                        'short_code'  => '456DEF',
                        'short_url'   => 'http://example.com456DEF',
                        'url'         => 'https://www.domain.com/test-url-2',
                        'created_at'  => '2021-01-01T00:00:00+01:00',
                        'visits'      => 0,
                        'valid_since' => '2021-01-03T00:00:00+01:00',
                        'valid_until' => '2021-01-04T00:00:00+01:00',
                        'max_visits'  => 1
                    ]
                ]
            ],
            $shortUrlsSerialized
        );
    }
}
