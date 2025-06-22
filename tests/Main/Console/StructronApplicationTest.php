<?php

declare(strict_types=1);

namespace Main\Console;

use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Wundii\Structron\Bootstrap\BootstrapConfigInitializer;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Console\Commands\StructronCommand;
use Wundii\Structron\Console\Commands\StructronInitCommand;
use Wundii\Structron\Console\Output\StructronSymfonyStyle;
use Wundii\Structron\Console\StructronApplication;
use Wundii\Structron\Finder\StructronFinder;
use Wundii\Structron\Resolver\Config\StructronPathsResolver;
use Wundii\Structron\Resolver\Config\StructronSkipPathsResolver;

class StructronApplicationTest extends TestCase
{
    public function getMockContainerBuilder(): ContainerBuilder
    {
        return new ContainerBuilder();
    }

    public function testRun()
    {
        $structronConfig = new StructronConfig();
        $consoleInput = new ArgvInput();
        $consoleOutput = new StreamOutput(fopen('php://memory', 'w', false));
        $structronConsoleOutput = new StructronSymfonyStyle($structronConfig, $consoleInput, $consoleOutput);
        $bootstrapConfigInitializer = new BootstrapConfigInitializer(new Filesystem(), $structronConsoleOutput);
        $structronConfig->paths(['src']);
        $structronFinder = new StructronFinder(
            new StructronSkipPathsResolver(),
            new StructronPathsResolver(),
        );
        $structronCommand = new StructronCommand(
            $structronConfig,
            $structronFinder
        );
        $structronInitCommand = new StructronInitCommand($bootstrapConfigInitializer);

        // Create Application instance
        $application = new StructronApplication(
            $structronCommand,
            $structronInitCommand,
        );
        $application->setAutoExit(false);

        // Create ApplicationTester
        $tester = new ApplicationTester($application);

        // Simulate running the application
        $statusCode = $tester->run([]);

        // Assertions
        $this->assertEquals(Command::SUCCESS, $statusCode);

        // Prepare the ConsoleOutput for reading
        $display = $tester->getDisplay(true);

        $this->assertStringContainsString('Structron', $display);
        $this->assertStringContainsString('Finished', $display);
    }

    public function testRunExceptionally()
    {
        // Mock Exception
        $exceptionMock = new Exception('Test exception');

        // Create ConsoleOutput instance
        $consoleOutput = new StreamOutput(fopen('php://memory', 'w', false));

        // Simulate running the application with an exception
        $statusCode = StructronApplication::runExceptionally($exceptionMock, $consoleOutput);

        // Prepare the ConsoleOutput for reading
        rewind($consoleOutput->getStream());
        $display = stream_get_contents($consoleOutput->getStream());
        $display = str_replace(\PHP_EOL, "\n", $display);

        // Assertions
        $this->assertEquals(Command::FAILURE, $statusCode);
        $this->assertStringContainsString('Structron', $display);
        $this->assertStringContainsString('Test exception', $display);
    }
}
