<?php

declare(strict_types=1);

namespace App\Service;

use PUGX\Shortid\Factory;

class GenerateRandomShortCode
{
    public function execute(int $length): string
    {
        $shortIdFactory = new Factory();

        $alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return $shortIdFactory->generate($length, $alphabet)->serialize();
    }
}
