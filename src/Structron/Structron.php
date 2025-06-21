<?php

declare(strict_types=1);

namespace Wundii\Structron\Structron;

use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Process\Process;
use Wundii\Structron\Cache\StructronCache;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Config\OptionEnum;
use Wundii\Structron\Console\Output\StructronSymfonyStyle;
use Wundii\Structron\Finder\StructronFinder;
use Wundii\Structron\Process\StructronProcessResult;
use Wundii\Structron\Process\StructronProcessTask;
use Wundii\Structron\Process\StatusEnum;

final class Structron
{
    private readonly StructronCache $structronCache;

    public function __construct(
        private readonly StructronSymfonyStyle $structronSymfonyStyle,
        private readonly StructronConfig $structronConfig,
        private readonly StructronFinder $structronFinder,
    ) {
        $adapter = new (StructronConfig::DEFAULT_CACHE_CLASS)();
        $cacheClass = $this->structronConfig->getString(OptionEnum::CACHE_CLASS);
        $cacheDir = $this->structronConfig->getString(OptionEnum::CACHE_DIR);

        if (is_a($cacheClass, AbstractAdapter::class, true)) {
            $adapter = new $cacheClass('cache', 0, $cacheDir);
        }

        $this->structronCache = new StructronCache($adapter);
    }

    public function run(): void
    {
        $processes = [];
        $processResults = [];
        $count = $this->structronFinder->count();
        $iterator = $this->structronFinder->getIterator();
        $asyncProcess = $this->structronConfig->getInteger(OptionEnum::ASYNC_PROCESS);
        $asyncProcessTimeout = $this->structronConfig->getInteger(OptionEnum::ASYNC_PROCESS_TIMEOUT);

        $this->structronSymfonyStyle->progressBarStart($count);

        while ($iterator->valid() || $processes !== []) {
            for ($i = count($processes); $iterator->valid() && $i < $asyncProcess; ++$i) {
                $currentFile = $iterator->current();
                $filename = $currentFile->getRealPath();

                if (! $this->structronCache->isMd5FileValid($filename)) {
                    $structronProcess = $this->createStructronProcess($filename, $asyncProcessTimeout);
                    $structronProcess->start();

                    $this->structronSymfonyStyle->progressBarAdvance();

                    $processes[] = new StructronProcessTask($this->structronConfig, $structronProcess, $currentFile);
                }

                $iterator->next();
            }

            foreach ($processes as $pid => $runningProcess) {
                /** @var StructronProcessTask $runningProcess */
                if ($runningProcess->isRunning()) {
                    continue;
                }

                $processResult = $runningProcess->getProcessResult();
                $processResults[] = $processResult;

                unset($processes[$pid]);

                if ($processResult->getStatus() === StatusEnum::OK) {
                    $this->structronCache->setMd5File($processResult->getFilename());
                }
            }
        }

        $this->structronSymfonyStyle->progressBarFinish();

        krsort($processResults);
        foreach ($processResults as $processResult) {
            $this->processResultToConsole($processResult);
        }
    }

    public function createStructronProcess(string $filename, int $timeout): Process
    {
        $command = [PHP_BINARY];

        if (PHP_SAPI !== 'cli') {
            $command = [PHP_BINARY . DIRECTORY_SEPARATOR . $this->structronConfig->getString(OptionEnum::PHP_CGI_EXECUTABLE)];
        }

        $command[] = '-d display_errors=1';
        $command[] = '-d error_reporting=E_ALL';
        $command[] = '-d memory_limit=' . $this->structronConfig->getString(OptionEnum::MEMORY_LIMIT);
        $command[] = '-n';
        $command[] = '-l';
        $command[] = $filename;

        return new Process($command, timeout: $timeout);
    }

    public function processResultToConsole(StructronProcessResult $structronProcessResult): void
    {
        if ($structronProcessResult->getStatus() === StatusEnum::OK) {
            return;
        }

        $this->structronSymfonyStyle->messageByProcessResult($structronProcessResult);
    }
}
