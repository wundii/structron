<?php

declare(strict_types=1);

namespace Wundii\Structron\Console\Commands;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wundii\Structron\Config\OptionEnum as ConfigOptionEnum;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Console\Output\StructronSymfonyStyle;
use Wundii\Structron\Console\StructronApplication;
use Wundii\Structron\Finder\StructronFinder;
use Wundii\Structron\Structron\Structron;

class StructronCommand extends Command
{
    public function __construct(
        private readonly StructronConfig $structronConfig,
        private readonly StructronFinder $structronFinder,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('structron');
        $this->setDescription('Generate documentation from structured PHP classes.');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $structronConfig = $this->structronConfig;
        $startExecuteTime = microtime(true);

        $output = new StructronSymfonyStyle($structronConfig, $input, $output);
        $output->startApplication(StructronApplication::vendorVersion());

        $structronFinder = $this->structronFinder->getFilesFromStructronConfig($structronConfig);

        $structron = new Structron($output, $structronConfig, $structronFinder);
        $structron->run();

        $usageExecuteTime = Helper::formatTime(microtime(true) - $startExecuteTime);

        $exitCode = (int) $output->finishApplication($usageExecuteTime);

        if ($structronConfig->getBoolean(ConfigOptionEnum::NO_EXIT_CODE)) {
            return self::SUCCESS;
        }

        return $exitCode;
    }
}
