<?php
declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\GenerateRandomShortCode;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class GenerateRandomShortCodeTest extends TestCase
{
    /** @test */
    public function it_will_return_a_predefined_length_of_chars(): void
    {
        $generateRandomShortCode = new GenerateRandomShortCode();
        $shortCode = $generateRandomShortCode->execute(6);

        Assert::assertEquals(6, strlen($shortCode));
        Assert::assertMatchesRegularExpression('/[a-zA-Z0-9]/', $shortCode);
    }
}
