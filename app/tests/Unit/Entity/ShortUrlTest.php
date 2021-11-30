<?php
declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\ShortUrl;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use SlopeIt\ClockMock\ClockMock;

class ShortUrlTest extends TestCase
{
    protected function setUp(): void
    {
        ClockMock::freeze(new \DateTime('2021-01-01 00:00:00'));

        parent::setUp();
    }

    /**
     * @test
     * @dataProvider getNotValidShortUrls
     */
    public function it_will_raise_an_exception_if_it_is_not_valid(
        string $originalUrl,
        string $shortUrl,
        \DateTimeImmutable $validSince,
        \DateTimeImmutable $validUntil,
        int $maxVisits,
        string $expectExceptionMessage
    ): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectExceptionMessage);

        new ShortUrl(
            $originalUrl,
            $shortUrl,
            $validSince,
            $validUntil,
            $maxVisits
        );
    }

    /**
     * @test
     */
    public function it_will_create_a_valid_instance(): void
    {
        $shortUrl = new ShortUrl(
            'https://www.domain.com/test-url',
            '123abc',
            new \DateTimeImmutable('-1 day'),
            new \DateTimeImmutable('+1 day'),
            1
        );

        Assert::assertEquals('https://www.domain.com/test-url', $shortUrl->getOriginalUrl());
        Assert::assertEquals('123abc', $shortUrl->getShortCode());
        Assert::assertEquals(new \DateTimeImmutable('-1 day'), $shortUrl->getValidSince());
        Assert::assertEquals(new \DateTimeImmutable('+1 day'), $shortUrl->getValidUntil());
        Assert::assertEquals(1, $shortUrl->getMaxVisits());
    }


    public function getNotValidShortUrls(): iterable
    {
        yield 'ShortUrl with invalid "maxVisits"' => [
            '$originalUrl' => 'https://www.domain.com/test-url',
            '$shortUrl' => '123abc',
            '$validSince' => new \DateTimeImmutable(),
            '$validUntil' => new \DateTimeImmutable(),
            '$maxVisits' => 0,
            '$expectExceptionMessage' => 'maxVisits must be greater than 0',
        ];

        yield '"validSince" after "validUntil"' => [
            '$originalUrl' => 'https://www.domain.com/test-url',
            '$shortUrl' => '123abc',
            '$validSince' => new \DateTimeImmutable('+1 day'),
            '$validUntil' => new \DateTimeImmutable('-1 day'),
            '$maxVisits' => 1,
            '$expectExceptionMessage' => 'validSince must be before validUntil',
        ];
    }
}
