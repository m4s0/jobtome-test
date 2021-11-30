<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\ShortUrl;
use App\Entity\Visit;
use App\Service\CheckShortUrlValidity;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class CheckShortUrlValidityTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideNotValidShortUrls
     */
    public function it_will_raise_an_exception_if_it_is_not_valid(
        ShortUrl $shortUrl,
        string $expectExceptionMessage
    ): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($expectExceptionMessage);

        CheckShortUrlValidity::execute($shortUrl);
    }

    /** @test */
    public function it_will_not_raise_an_exception_if_it_is_valid(): void
    {
        $shortUrl = new ShortUrl(
            'https://www.domain.com/test-url',
            '123abc',
            new \DateTimeImmutable('-1 day'),
            new \DateTimeImmutable('+1 day'),
            1
        );

        Assert::assertTrue(CheckShortUrlValidity::execute($shortUrl));
    }

    public function provideNotValidShortUrls(): iterable
    {
        yield 'ShortUrl not yet active' => [
            '$shortUrl' => new ShortUrl(
                'https://www.domain.com/test-url',
                '123abc',
                new \DateTimeImmutable('+1 day'),
                null,
                10
            ),
            '$expectExceptionMessage' => 'Short URL not found',
        ];

        yield 'ShortUrl expired' => [
            '$shortUrl' => new ShortUrl(
                'https://www.domain.com/test-url',
                '123abc',
                new \DateTimeImmutable('-10 day'),
                new \DateTimeImmutable('-1 day'),
                10
            ),
            '$expectExceptionMessage' => 'Short URL not found',
        ];

        $shortUrl = new ShortUrl(
            'https://www.domain.com/test-url',
            '123abc',
            null,
            null,
            1
        );

        $shortUrl->addVisit(
            new Visit(
                $shortUrl,
                null,
                '127.0.0.1',
                'user-agent'
            )
        );

        yield 'ShortUrl max visits reached' => [
            '$shortUrl' => $shortUrl,
            '$expectExceptionMessage' => 'Visits to this Short URL exceed the maximum limit',
        ];
    }
}
