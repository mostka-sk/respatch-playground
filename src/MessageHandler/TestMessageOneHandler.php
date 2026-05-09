<?php

namespace App\MessageHandler;

use App\Message\TestMessageOne;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TestMessageOneHandler
{
    public function __invoke(TestMessageOne $message): void
    {
        sleep(5);
        if (random_int(1, 100) <= 10) {
            throw new \Exception('Random failure (5% chance) for TestMessageOne.');
        }

        // Processing success...
    }
}
