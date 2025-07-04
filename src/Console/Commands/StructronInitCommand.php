<?php

declare(strict_types=1);

namespace Wundii\Structron\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wundii\Structron\Bootstrap\BootstrapConfigInitializer;

class StructronInitCommand extends Command
{
    public function __construct(
        private readonly BootstrapConfigInitializer $bootstrapConfigInitializer
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('init');
        $this->setDescription('Create a new Structron configuration file if it does not exist');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bootstrapConfigInitializer->createConfig((string) getcwd());
        return self::SUCCESS;
    }
}
