<?php

declare(strict_types=1);

namespace Main\Console;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\StreamOutput;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Console\Output\StructronSymfonyStyle;
use Wundii\Structron\Process\StructronProcessResult;
use Wundii\Structron\Process\StatusEnum;

class StructronConsoleOutputTest extends TestCase
{
    public function testStructronProcessResultToConsoleWithOk()
    {
        $filename = __DIR__ . '/fileNotExists.php';
        $structronProcessResult = new StructronProcessResult(
            StatusEnum::OK,
            $filename,
            'Some structron result',
            10,
        );

        $expected = <<<EOT
#1 - line 10 [{$filename}]
Ok: Some structron result


EOT;

        $this->structronProcessResultToConsole($structronProcessResult, $expected);
    }

    public function testStructronProcessResultToConsoleWithNotice()
    {
        $filename = __DIR__ . '/Files/File1.php';
        $structronProcessResult = new StructronProcessResult(
            StatusEnum::NOTICE,
            $filename,
            'Some structron result',
            1,
        );

        $expected = <<<EOT
#1 - line 1 [{$filename}]
Notice: Some structron result
00001| <?php
00002| 
00003| declare(strict_types=1);
00004| 
00005| echo 'Hello, world!';


EOT;

        $this->structronProcessResultToConsole($structronProcessResult, $expected);
    }

    public function testStructronProcessResultToConsoleWithWarning()
    {
        $filename = __DIR__ . '/Files/File1.php';
        $structronProcessResult = new StructronProcessResult(
            StatusEnum::WARNING,
            $filename,
            'Some structron result',
            2,
        );

        $expected = <<<EOT
#1 - line 2 [{$filename}]
Warning: Some structron result
00001| <?php
00002| 
00003| declare(strict_types=1);
00004| 
00005| echo 'Hello, world!';
00006| 


EOT;

        $this->structronProcessResultToConsole($structronProcessResult, $expected);
    }

    public function testStructronProcessResultToConsoleWithError()
    {
        $filename = __DIR__ . '/Files/File1.php';
        $structronProcessResult = new StructronProcessResult(
            StatusEnum::ERROR,
            $filename,
            'Some structron result',
            46,
        );

        $expected = <<<EOT
#1 - line 46 [{$filename}]
Error: Some structron result
00042| 
00043| \$person = new Person('Bob');
00044| 
00045| echo \$person->getName();
00046| 


EOT;

        $this->structronProcessResultToConsole($structronProcessResult, $expected);
    }

    public function testStructronProcessResultToConsoleWithRunning()
    {
        $filename = __DIR__ . '/Files/File1.php';
        $structronProcessResult = new StructronProcessResult(
            StatusEnum::RUNNING,
            $filename,
            'Some structron result',
            10,
        );

        $expected = <<<EOT
#1 - line 10 [{$filename}]
Running: Some structron result
00006| 
00007| \$variable = 42;
00008| 
00009| if (\$variable > 30) {
00010|     echo 'Variable is greater than 30.';
00011| } else {
00012|     echo 'Variable is not greater than 30.';
00013| }
00014| 


EOT;

        $this->structronProcessResultToConsole($structronProcessResult, $expected);
    }

    public function structronProcessResultToConsole(StructronProcessResult $structronProcessResult, string $expected): void
    {
        $structronConfig = new StructronConfig();
        $consoleInput = new ArgvInput();
        $consoleOutput = new StreamOutput(fopen('php://memory', 'w', false));

        $structronConsoleOutput = new StructronSymfonyStyle($structronConfig, $consoleInput, $consoleOutput);
        $structronConsoleOutput->messageByProcessResult($structronProcessResult);

        rewind($consoleOutput->getStream());
        $display = stream_get_contents($consoleOutput->getStream());
        $display = str_replace(\PHP_EOL, "\n", $display);

        $this->assertEquals($expected, $display);
    }
}
