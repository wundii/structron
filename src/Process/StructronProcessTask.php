<?php

declare(strict_types=1);

namespace Wundii\Structron\Process;

use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Config\OptionEnum;

final class StructronProcessTask
{
    /**
     * @var string
     */
    public const REGEX_ERROR = '/^(PHP\s+)?(Parse|Fatal) error:\s*?(?<error>.*?)(?: in .+? line (?<line>\d+))?$/';

    /**
     * @var string
     */
    public const REGEX_WARNING = '/^(PHP\s+)?(Warning|Deprecated|Notice):\s*?(?<error>.+?)(?: in .+? line (?<line>\d+))?$/';

    public function __construct(
        private readonly StructronConfig $structronConfig,
        private readonly Process $process,
        private readonly SplFileInfo $splFileInfo,
    ) {
    }

    public function getProcessResult(): StructronProcessResult
    {
        $fileRealPath = $this->splFileInfo->getRealPath();

        if ($this->isRunning()) {
            return new StructronProcessResult(StatusEnum::RUNNING, $fileRealPath, 'Process is still running');
        }

        $output = trim($this->process->getOutput());
        $outputExplode = explode("\n", $output);
        $result = array_shift($outputExplode);

        $matchedError = ! str_contains($result, 'No syntax errors detected');
        $matchedWarning = preg_match('#(Warning:|Deprecated:)#', $result);
        $matchedInfo = str_contains($result, 'Notice:');
        $isConsoleNotice = $this->structronConfig->getBoolean(OptionEnum::CONSOLE_NOTICE);
        $isConsoleWarning = $this->structronConfig->getBoolean(OptionEnum::CONSOLE_WARNING);

        if ($matchedError && ! $matchedWarning && ! $matchedInfo) {
            return $this->createStructronProcessResult(StatusEnum::ERROR, $fileRealPath, self::REGEX_ERROR, $result);
        }

        if ($isConsoleWarning && $matchedWarning) {
            return $this->createStructronProcessResult(StatusEnum::WARNING, $fileRealPath, self::REGEX_WARNING, $result);
        }

        if ($isConsoleNotice && $matchedInfo) {
            return $this->createStructronProcessResult(StatusEnum::NOTICE, $fileRealPath, self::REGEX_WARNING, $result);
        }

        return new StructronProcessResult(StatusEnum::OK, $fileRealPath);
    }

    public function isRunning(): bool
    {
        return $this->process->isRunning();
    }

    private function createStructronProcessResult(StatusEnum $statusEnum, string $filename, string $pattern, string $result): StructronProcessResult
    {
        $message = '';
        $line = null;

        $matched = preg_match($pattern, $result, $match);
        if ($matched !== false && $matched > 0) {
            $message = trim($match['error']);
            $line = (int) $match['line'];
        }

        return new StructronProcessResult($statusEnum, $filename, $message, $line);
    }
}
