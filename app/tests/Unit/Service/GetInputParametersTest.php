<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\GetInputParameters;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

class GetInputParametersTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideNotValidRequests
     */
    public function it_will_raise_an_exception_if_it_is_not_valid(
        Request $request,
        string $expectExceptionMessage
    ): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($expectExceptionMessage);

        GetInputParameters::execute($request);
    }

    /** @test */
    public function it_will_not_raise_an_exception_if_it_is_valid(): void
    {
        $content = '{
          "url": "https://www.domain.com/test-url",
          "valid_since": "2021-01-01T00:00:00+01:00",
          "valid_until": "2030-01-01T00:00:00+01:00",
          "max_visits": 10
        }';

        $request = new Request([], [], [], [], [], [], $content);

        $inputParameters = GetInputParameters::execute($request);
        Assert::assertEquals('https://www.domain.com/test-url' ,$inputParameters->getUrl());
        Assert::assertEquals(10 ,$inputParameters->getMaxVisits());
        Assert::assertEquals("2021-01-01T00:00:00+01:00", $inputParameters->getValidSince()->format(\DateTimeImmutable::ATOM));
        Assert::assertEquals("2030-01-01T00:00:00+01:00", $inputParameters->getValidUntil()->format(\DateTimeImmutable::ATOM));
    }

    public function provideNotValidRequests(): iterable
    {
        $noURL = '{
          "valid_since": "2021-01-01T00:00:00+01:00",
          "valid_until": "2030-01-01T00:00:00+01:00",
          "max_visits": 10
        }';

        yield 'Request with no URL' => [
            '$request' => new Request([], [], [], [], [], [], $noURL),
            '$expectExceptionMessage' => 'Error, missing url',
        ];

        $invalidValidSince = '{
          "url": "https://www.domain.com/test-url",
          "valid_since": "2021-01-T00:00+01:00",
          "valid_until": "2030-01-01T00:00:00+01:00",
          "max_visits": 10
        }';

        yield 'Request with invalid "valid_since"' => [
            '$request' => new Request([], [], [], [], [], [], $invalidValidSince),
            '$expectExceptionMessage' => 'Error, invalid valid_since',
        ];

        $invalidValidUntil = '{
          "url": "https://www.domain.com/test-url",
          "valid_since": "2021-01-01T00:00:00+01:00",
          "valid_until": "2030-01-T00:00+01:00",
          "max_visits": 10
        }';

        yield 'Request with invalid "valid_until"' => [
            '$request' => new Request([], [], [], [], [], [], $invalidValidUntil),
            '$expectExceptionMessage' => 'Error, invalid valid_until',
        ];

        $invalidValidMaxVisits = '{
          "url": "https://www.domain.com/test-url",
          "valid_since": "2021-01-01T00:00:00+01:00",
          "valid_until": "2030-01-01T00:00:00+01:00",
          "max_visits": "sdf"
        }';

        yield 'Request with invalid "max_visits"' => [
            '$request' => new Request([], [], [], [], [], [], $invalidValidMaxVisits),
            '$expectExceptionMessage' => 'Error, invalid max_visits',
        ];
    }
}
