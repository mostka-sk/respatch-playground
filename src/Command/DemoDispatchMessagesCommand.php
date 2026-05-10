<?php

namespace App\Command;

use App\Message\TestMessageOne;
use App\Message\TestMessageTwo;
use App\Message\TestMessageThree;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:demo:dispatch-messages',
    description: 'Dispatches demo messages for the live demo server (to be run via cron)',
)]
class DemoDispatchMessagesCommand extends Command
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Generujeme niekoľko úspešných správ
        $successCount = rand(2, 5);
        for ($i = 0; $i < $successCount; $i++) {
            $this->messageBus->dispatch(new TestMessageOne('Demo success message ' . rand(100, 999)));
            $this->messageBus->dispatch(new TestMessageTwo('Another demo success ' . rand(100, 999)));
        }

        // Sem-tam generujeme správu, ktorá môže zlyhať
        // (TestMessageThreeHandler má 10% šancu na výnimku, ale pre demo zvýšime pravdepodobnosť tým, že ich odošleme viac)
        if (rand(1, 100) <= 50) {
            $this->messageBus->dispatch(new TestMessageThree('Demo potential failure ' . rand(100, 999)));
            $this->messageBus->dispatch(new TestMessageThree('Demo potential failure ' . rand(100, 999)));
        }

        $output->writeln(sprintf('<info>Dispatched %d normal messages and some potential failure messages.</info>', $successCount * 2));

        return Command::SUCCESS;
    }
}
