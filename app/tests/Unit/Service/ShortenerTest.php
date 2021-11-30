<?php
declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\ShortUrl;
use App\Repository\ShortUrlRepository;
use App\Service\GenerateRandomShortCode;
use App\Service\Shortener;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use SlopeIt\ClockMock\ClockMock;

class ShortenerTest extends TestCase
{
    use ProphecyTrait;

    protected function setUp(): void
    {
        ClockMock::freeze(new \DateTime('2021-01-01 00:00:00'));

        parent::setUp();
    }

    /** @test */
    public function it_will_raise_an_exception_if_the_url_exists()
    {
        $shortUrlRepository = $this->prophesize(ShortUrlRepository::class);
        $generateRandomShortCode = $this->prophesize(GenerateRandomShortCode::class);

        $shortener = new Shortener(
            $shortUrlRepository->reveal(),
            $generateRandomShortCode->reveal(),
            6
        );

        $originalUrl = 'https://www.domain.com/test-url';
        $shortUrl = new ShortUrl(
            $originalUrl,
            '123abc',
            new \DateTimeImmutable('2021-01-01 00:00:00'),
            new \DateTimeImmutable('2021-01-02 00:00:00'),
            10
        );
        $shortUrlRepository->findByOriginalUrl($originalUrl)->shouldBeCalledOnce()->willReturn($shortUrl);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Short URL already exists');

        $shortener->execute($originalUrl);
    }

    /** @test */
    public function it_will_create_a_short_url_if_not_exists(): void
    {
        $shortUrlRepository = $this->prophesize(ShortUrlRepository::class);
        $generateRandomShortCode = $this->prophesize(GenerateRandomShortCode::class);

        $shortener = new Shortener(
            $shortUrlRepository->reveal(),
            $generateRandomShortCode->reveal(),
            6
        );

        $originalUrl = 'https://www.domain.com/test-url';
        $shortUrlRepository->findByOriginalUrl($originalUrl)->shouldBeCalledOnce()->willReturn(null);
        $generateRandomShortCode->execute(6)->shouldBeCalledOnce()->willReturn('123abc');

        $shortUrl = $shortener->execute(
            $originalUrl,
            new \DateTimeImmutable('2021-01-01 00:00:00'),
            new \DateTimeImmutable('2021-01-02 00:00:00'),
            10
        );

        Assert::assertNull($shortUrl->getId());
        Assert::assertEquals($originalUrl, $shortUrl->getOriginalUrl());
        Assert::assertEquals('123abc', $shortUrl->getShortCode());
        Assert::assertEquals(new \DateTimeImmutable('2021-01-01 00:00:00'), $shortUrl->getCreatedAt());
        Assert::assertEquals(new \DateTimeImmutable('2021-01-01 00:00:00'), $shortUrl->getValidSince());
        Assert::assertEquals(new \DateTimeImmutable('2021-01-02 00:00:00'), $shortUrl->getValidUntil());
        Assert::assertEquals(10, $shortUrl->getMaxVisits());
        Assert::assertEquals(0, $shortUrl->getVisits()->count());
    }
}
