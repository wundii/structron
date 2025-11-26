<?php

declare(strict_types=1);

namespace Wundii\Structron\Tests\Main\Console;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wundii\Structron\Bootstrap\BootstrapConfigInitializer;
use Wundii\Structron\Console\Commands\StructronInitCommand;
use Wundii\Structron\Tests\Main\Console\Files\MockStructronInitCommand;

final class StructronInitCommandTest extends TestCase
{
    public function testConfigureSetsNameAndDescription(): void
    {
        $initializer = $this->createMock(BootstrapConfigInitializer::class);
        $command = new StructronInitCommand($initializer);

        self::assertSame('init', $command->getName());
        self::assertSame(
            'Create a new Structron configuration file if it does not exist',
            $command->getDescription()
        );
    }

    public function testExecuteCallsCreateConfigAndReturnsSuccess(): void
    {
        $initializer = $this->createMock(BootstrapConfigInitializer::class);
        $initializer->expects(self::once())
            ->method('createConfig')
            ->with((string) getcwd());

        $command = new MockStructronInitCommand($initializer);

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $result = $command->execute($input, $output);

        self::assertSame(StructronInitCommand::SUCCESS, $result);
    }
}
