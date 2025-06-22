<?php

declare(strict_types=1);

namespace Wundii\Structron\Structron;

use Wundii\Structron\Config\OptionEnum;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Console\Output\StructronSymfonyStyle;
use Wundii\Structron\Finder\StructronFinder;

final class Structron
{
    public function __construct(
        private readonly StructronSymfonyStyle $structronSymfonyStyle,
        private readonly StructronConfig $structronConfig,
        private readonly StructronFinder $structronFinder,
    ) {
    }

    public function run(): void
    {
        $count = $this->structronFinder->count();

        $this->structronSymfonyStyle->progressBarStart($count);

        $test = $this->structronConfig->getParameter(OptionEnum::TEST);
        $this->structronConfig->setParameter(OptionEnum::TEST, $test);
        // foreach() {
        //
        //     $this->structronSymfonyStyle->progressBarAdvance();
        // }

        $this->structronSymfonyStyle->progressBarFinish();
        // $this->processResultToConsole($processResult);
    }
}
