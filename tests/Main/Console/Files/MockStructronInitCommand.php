<?php

declare(strict_types=1);

namespace Wundii\Structron\Tests\Main\Console\Files;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wundii\Structron\Console\Commands\StructronInitCommand;

class MockStructronInitCommand extends StructronInitCommand
{
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        return parent::execute($input, $output);
    }
}
