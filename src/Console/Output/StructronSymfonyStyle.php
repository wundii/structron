<?php

declare(strict_types=1);

namespace Wundii\Structron\Console\Output;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Config\OptionEnum;
use Wundii\Structron\Console\OutputColorEnum;
use Wundii\Structron\Process\StructronProcessResult;
use Wundii\Structron\Process\StatusEnum;

final class StructronSymfonyStyle extends SymfonyStyle
{
    /**
     * @var int
     */
    private const SNIPPED_LINE = 5;

    /**
     * @var int
     */
    private const LINE_LENGTH = 5;

    private bool $isSuccess = true;

    private int $countFiles = 0;

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

    public function messageByProcessResult(StructronProcessResult $structronProcessResult): void
    {
        $outputColorEnum = match ($structronProcessResult->getStatus()) {
            StatusEnum::OK => OutputColorEnum::GREEN,
            StatusEnum::NOTICE => OutputColorEnum::BLUE,
            StatusEnum::WARNING => OutputColorEnum::YELLOW,
            default => OutputColorEnum::RED,
        };

        ++$this->countFiles;

        $line01 = sprintf(
            '<fg=white;options=bold>#%d - line %s </><fg=gray;options=bold>[%s]</>',
            $this->countFiles,
            $structronProcessResult->getLine(),
            $structronProcessResult->getFilename(),
        );
        $line02 = sprintf(
            '<fg=%s;options=bold>%s</>: <fg=%s>%s</>',
            $outputColorEnum->getBrightValue(),
            ucfirst($structronProcessResult->getStatus()->value),
            $outputColorEnum->value,
            $structronProcessResult->getResult(),
        );

        $this->writeln($line01);
        $this->writeln($line02);
        $this->loadCodeSnippet($structronProcessResult->getFilename(), (int) $structronProcessResult->getLine(), $outputColorEnum);
        $this->newLine();

        $this->isSuccess = false;
    }

    private function loadCodeSnippet(string $filename, int $line, OutputColorEnum $outputColorEnum): void
    {
        $lineStart = $line - self::SNIPPED_LINE;
        $lineEnd = $line + (self::SNIPPED_LINE - 1);

        if (! file_exists($filename)) {
            return;
        }

        $content = file_get_contents($filename);
        if ($content === false) {
            return;
        }

        $contentArray = explode("\n", $content);

        $lineCnt = 0;
        foreach ($contentArray as $contentLine) {
            if ($lineCnt >= $lineStart && $lineCnt < $lineEnd) {
                $lineNumber = $lineCnt + 1;
                $tmp = str_pad((string) $lineNumber, self::LINE_LENGTH, '0', STR_PAD_LEFT);
                $lineNumberPrefix = substr($tmp, 0, self::LINE_LENGTH - strlen((string) $lineNumber));

                if ($lineCnt + 1 === $line) {
                    $result = sprintf(
                        '<fg=%s>%s</><fg=%s;options=bold>%s</><fg=gray>|</> <fg=%s>%s</>',
                        $outputColorEnum->getBrightValue(),
                        $lineNumberPrefix,
                        $outputColorEnum->value,
                        $lineNumber,
                        $outputColorEnum->value,
                        $contentLine,
                    );
                } else {
                    $result = sprintf(
                        '<fg=gray>%s</><fg=white;options=bold>%s</><fg=gray>|</> <fg=white>%s</>',
                        $lineNumberPrefix,
                        $lineNumber,
                        $contentLine,
                    );
                }

                $this->writeln($result);
            }

            ++$lineCnt;
        }
    }
}
