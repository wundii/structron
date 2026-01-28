<?php

declare(strict_types=1);

namespace Wundii\Structron\Config;

use Webmozart\Assert\Assert;

final class StructronConfig extends StructronConfigParameter
{
    public function __construct()
    {
        $this->setParameter(OptionEnum::NO_EXIT_CODE, false);
        $this->setParameter(OptionEnum::NO_PROGRESS_BAR, false);
        $this->setParameter(OptionEnum::PHP_EXTENSION, 'php');
        $this->setParameter(OptionEnum::DOC_PATH, 'structron-docs');
        $this->setParameter(OptionEnum::INDENT_FILE_ITERATION, false);
        $this->setParameter(OptionEnum::TEST, false);
    }

    public function disableExitCode(): void
    {
        $this->setParameter(OptionEnum::NO_EXIT_CODE, true);
    }

    public function disableProcessBar(): void
    {
        $this->setParameter(OptionEnum::NO_PROGRESS_BAR, true);
    }

    public function phpExtension(string $string): void
    {
        $this->setParameter(OptionEnum::PHP_EXTENSION, $string);
    }

    public function docPath(string $string): void
    {
        $this->setParameter(OptionEnum::DOC_PATH, $string);
    }

    /**
     * @param string[] $paths
     */
    public function paths(array $paths): void
    {
        Assert::allString($paths);

        $this->setParameter(OptionEnum::PATHS, $paths);
    }

    /**
     * @param string[] $skip
     */
    public function skip(array $skip): void
    {
        Assert::allString($skip);

        $this->setParameter(OptionEnum::SKIP, $skip);
    }

    public function setIndentFileIteration(): void
    {
        $this->setParameter(OptionEnum::INDENT_FILE_ITERATION, true);
    }
}
