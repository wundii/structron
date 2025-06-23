<?php

declare(strict_types=1);

namespace Wundii\Structron\Console\Output;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Wundii\Structron\Config\OptionEnum;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Console\OutputColorEnum;

class StructronSymfonyStyle extends SymfonyStyle
{
    private bool $isSuccess = true;

    public function __construct(
        private readonly StructronConfig $structronConfig,
        InputInterface $consoleInput,
        OutputInterface $consoleOutput,
    ) {
        parent::__construct($consoleInput, $consoleOutput);
    }

    public function startApplication(string $version): void
    {
        $argv = $_SERVER['argv'] ?? [];
        $argv = array_values((array) $argv);
        $argv = array_map(static fn ($value): string => is_string($value) ? $value : '', $argv);

        $message = sprintf(
            '<fg=blue;options=bold>PHP</><fg=yellow;options=bold>Structron</> %s - current PHP version: %s',
            $version,
            PHP_VERSION,
        );

        $this->writeln('> ' . implode(' ', $argv));
        $this->writeln($message);
        $this->writeln('');
    }

    public function finishApplication(string $executionTime): bool
    {
        $usageMemory = Helper::formatMemory(memory_get_usage(true));

        $this->writeln(sprintf('Memory usage: %s', $usageMemory));

        if (! $this->isSuccess) {
            $this->error(sprintf('Finished in %s', $executionTime));
            return true;
        }

        $this->success(sprintf('Finished in %s', $executionTime));
        return false; // false means success
    }

    public function progressBarStart(int $count): void
    {
        if ($this->structronConfig->getBoolean(OptionEnum::NO_PROGRESS_BAR)) {
            return;
        }

        $this->progressStart($count);
    }

    public function progressBarAdvance(): void
    {
        if ($this->structronConfig->getBoolean(OptionEnum::NO_PROGRESS_BAR)) {
            return;
        }

        $this->progressAdvance();
    }

    public function progressBarFinish(): void
    {
        if ($this->structronConfig->getBoolean(OptionEnum::NO_PROGRESS_BAR)) {
            return;
        }

        $this->progressFinish();
    }

    public function setError(): void
    {
        $this->isSuccess = false;
    }

    // public function generateStructron(string $value): void
    // {
    //     $outputColorEnum = OutputColorEnum::BLUE;
    //     ++$this->countFiles;
    //
    //     // $line01 = sprintf(
    //     //     '<fg=white;options=bold>#%d - line %s </><fg=%s;options=bold>[%s]</>',
    //     //     $this->countFiles,
    //     //     $value,
    //     //     $outputColorEnum->value,
    //     //     'as',
    //     // );
    //     $line01 = sprintf(
    //         '<fg=%s;options=bold>#%d - file: %s </><fg=%s;options=bold>[%s]</>',
    //         $outputColorEnum->getBrightValue(),
    //         $this->countFiles,
    //         $value,
    //         $outputColorEnum->value,
    //         'as',
    //     );
    //
    //     $this->newLine();
    //     $this->writeln($line01);
    //     $this->newLine();
    // }
}
