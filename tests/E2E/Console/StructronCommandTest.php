<?php

declare(strict_types=1);

namespace E2E\Console;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Wundii\Structron\Bootstrap\BootstrapConfigInitializer;
use Wundii\Structron\Bootstrap\BootstrapConfigResolver;
use Wundii\Structron\Bootstrap\BootstrapInputResolver;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Console\Commands\StructronCommand;
use Wundii\Structron\Console\StructronApplication;
use Wundii\Structron\Console\Output\StructronSymfonyStyle;
use Wundii\Structron\Finder\StructronFinder;
use Wundii\Structron\Resolver\Config\StructronPathsResolver;
use Wundii\Structron\Resolver\Config\StructronSkipPathsResolver;

class StructronCommandTest extends TestCase
{
    private StreamOutput $consoleOutput;

    public function createStructronCommand(StructronConfig $structronConfig, array $argvInput = []): CommandTester
    {
        if ($argvInput !== []) {
            $first = $argvInput[0] ?? null;
            if ($first !== 'bin/structron') {
                $argvInput = [
                    'bin/structron',
                    ...$argvInput,
                ];
            }
        }

        $consoleInput = new ArgvInput($argvInput);
        $this->consoleOutput = new StreamOutput(fopen('php://memory', 'w', false));
        $structronConsoleOutput = new StructronSymfonyStyle($structronConfig, $consoleInput, $this->consoleOutput);
        $bootstrapConfigInitializer = new BootstrapConfigInitializer(new Filesystem(), $structronConsoleOutput);
        $bootstrapInputResolver = new BootstrapInputResolver($consoleInput);
        $bootstrapConfigResolver = new BootstrapConfigResolver($bootstrapInputResolver);

        $structronCommand = new StructronCommand(
            $bootstrapConfigInitializer,
            $bootstrapConfigResolver,
            $bootstrapInputResolver,
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

    public function testEndToEndFail()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->paths(['tests/FaultyFiles']);

        $structronCommand = $this->createStructronCommand($structronConfig);

        $this->assertSame(1, $structronCommand->execute([]));
    }

    #[Depends('testEndToEndFail')]
    public function testEndToEndFailWithSuccessReturn()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->paths(['tests/FaultyFiles']);
        $structronConfig->disableExitCode();

        $structronCommand = $this->createStructronCommand($structronConfig);

        $this->assertSame(0, $structronCommand->execute([]));
    }

    public function testEndToEndNoConfigWithOptionsFail()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->paths(['tests/FaultyFiles']);

        $structronCommand = $this->createStructronCommand($structronConfig, [
            '--no-progress-bar',
        ]);
        $execute = $structronCommand->execute([]);
        $display = $structronCommand->getDisplay(true);

        $this->assertSame(1, $execute);
        $this->assertStringContainsString('3/3 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%', $display);
    }

    public function testEndToEndNoConfigWithOptionsSuccess()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->paths(['tests/FaultyFiles']);

        $structronCommand = $this->createStructronCommand($structronConfig, [
            '--no-config',
            '--no-progress-bar',
        ]);
        $execute = $structronCommand->execute([]);
        $display = $structronCommand->getDisplay(true);

        $this->assertSame(1, $execute);
        $this->assertStringNotContainsString('3/3 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%', $display);
    }
}
