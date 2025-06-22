<?php

declare(strict_types=1);

namespace Main\Structron;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Process\Process;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Console\Output\StructronSymfonyStyle;
use Wundii\Structron\Finder\StructronFinder;
use Wundii\Structron\Resolver\Config\StructronPathsResolver;
use Wundii\Structron\Resolver\Config\StructronSkipPathsResolver;
use Wundii\Structron\Structron\Structron;

class StructronTest extends TestCase
{
    public function testTest(): void
    {
        $this->assertTrue(true);
    }

    // public function testCreateStructronProcess()
    // {
    //     $structronConfig = new StructronConfig();
    //     $consoleInput = new ArgvInput();
    //     $consoleOutput = new StreamOutput(fopen('php://memory', 'w', false));
    //     $structronConsoleOutput = new StructronSymfonyStyle($structronConfig, $consoleInput, $consoleOutput);
    //
    //     $structron = new Structron(
    //         $structronConsoleOutput,
    //         $structronConfig,
    //         new StructronFinder(
    //             new StructronSkipPathsResolver(),
    //             new StructronPathsResolver(),
    //         ),
    //     );
    //
    //     $filename = 'path/to/file.php';
    //     $process = $structron->createStructronProcess($filename, 60);
    //
    //     $this->assertInstanceOf(Process::class, $process);
    //
    //     $phpBinary = PHP_BINARY;
    //     $expectedCommand = sprintf("'%s' '-d display_errors=1' '-d error_reporting=E_ALL' '-d memory_limit=256M' '-n' '-l' 'path/to/file.php'", $phpBinary);
    //
    //     $this->assertEquals($expectedCommand, $process->getCommandLine());
    // }

    // public function testProcessResultToConsoleOutputIsEmpty()
    // {
    //     $structronProcessResult = new StructronProcessResult(
    //         StatusEnum::OK,
    //         __DIR__ . '/example.php',
    //         'Some structron result',
    //         10,
    //     );
    //
    //     $this->processResultToConsole($structronProcessResult, true);
    // }
    //
    // public function testProcessResultToConsoleOutputIsNotEmpty()
    // {
    //     $structronProcessResult = new StructronProcessResult(
    //         StatusEnum::NOTICE,
    //         __DIR__ . '/Files/File1.php',
    //         'Some structron result',
    //         1,
    //     );
    //
    //     $this->processResultToConsole($structronProcessResult, false);
    // }
    //
    // public function processResultToConsole(StructronProcessResult $structronProcessResult, bool $assertEmpty): void
    // {
    //     $structronConfig = new StructronConfig();
    //     $consoleInput = new ArgvInput();
    //     $consoleOutput = new StreamOutput(fopen('php://memory', 'w', false));
    //     $structronConsoleOutput = new StructronSymfonyStyle($structronConfig, $consoleInput, $consoleOutput);
    //
    //     $structron = new Structron(
    //         $structronConsoleOutput,
    //         $structronConfig,
    //         new StructronFinder(
    //             new StructronSkipPathsResolver(),
    //             new StructronPathsResolver(),
    //         ),
    //     );
    //     $structron->processResultToConsole($structronProcessResult);
    //
    //     rewind($consoleOutput->getStream());
    //     $display = stream_get_contents($consoleOutput->getStream());
    //     $display = str_replace(\PHP_EOL, "\n", $display);
    //
    //     if ($assertEmpty) {
    //         $this->assertEmpty($display);
    //     } else {
    //         $this->assertNotEmpty($display);
    //     }
    // }
}
