<?php

declare(strict_types=1);

namespace Wundii\Structron\Tests\E2E\Console;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Console\Commands\StructronCommand;
use Wundii\Structron\Console\StructronApplication;
use Wundii\Structron\Finder\StructronFinder;
use Wundii\Structron\Resolver\Config\StructronPathsResolver;
use Wundii\Structron\Resolver\Config\StructronSkipPathsResolver;

class StructronCommandTest extends TestCase
{
    private StreamOutput $consoleOutput;

    public function createStructronCommand(StructronConfig $structronConfig): CommandTester
    {
        $this->consoleOutput = new StreamOutput(fopen('php://memory', 'w', false));

        $structronCommand = new StructronCommand(
            $structronConfig,
            new StructronFinder(
                new StructronSkipPathsResolver(),
                new StructronPathsResolver(),
            ),
        );

        return new CommandTester($structronCommand);
    }

    public function testEndToEndSuccess()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->paths(['src']);

        $structronCommand = $this->createStructronCommand($structronConfig);

        $this->assertSame(0, $structronCommand->execute([]));
    }

    public function testEndToEndFirstDisplayLine()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->paths(['src']);

        $structronCommand = $this->createStructronCommand($structronConfig);
        $structronCommand->execute([]);

        $display = $structronCommand->getDisplay(true);
        $firstDisplayLine = explode("\n", $display)[0];

        preg_match('/>\s(.?)/', $firstDisplayLine, $matches);

        $this->assertCount(2, $matches, 'First line should contain the command (' . $firstDisplayLine . ')');
        $this->assertStringContainsString('Structron ' . StructronApplication::VERSION . ' - current PHP version: ' . PHP_VERSION, $display);
    }
}
