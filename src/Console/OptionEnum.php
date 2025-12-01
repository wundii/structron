<?php

declare(strict_types=1);

namespace Wundii\Structron\Console;

use Symfony\Component\Console\Input\InputOption;
use Wundii\Structron\Bootstrap\BootstrapInputResolver;
use Wundii\Structron\Config\StructronConfig;

enum OptionEnum: string
{
    case ANSI = 'ansi';
    case CONFIG = 'config';
    case HELP = 'help';
    case INIT = 'init';
    case NO_EXIT_CODE = 'no-exit-code';
    case NO_PROGRESS_BAR = 'no-progress-bar';
    case PATHS = 'paths';
    case SKIP = 'skip';
    case VERBOSE = 'verbose';
    case VERSION = 'version';

    /**
     * @var string
     */
    private const PRE_NAME = '--';

    /**
     * @var string
     */
    private const PRE_SHORTCUT = '-';

    public function getName(): string
    {
        return self::PRE_NAME . $this->value;
    }

    public function getShortcut(): string
    {
        return match ($this) {
            self::CONFIG => self::PRE_SHORTCUT . 'c',
            self::HELP => self::PRE_SHORTCUT . 'h',
            self::INIT => self::PRE_SHORTCUT . 'i',
            self::VERBOSE => self::PRE_SHORTCUT . 'v|vv|vvv',
            self::VERSION => self::PRE_SHORTCUT . 'V',
            default => '',
        };
    }

    /**
     * @return InputOption[]
     */
    public static function getInputDefinition(string $defaultConfigPath): array
    {
        return [
            new InputOption(self::ANSI->getName(), self::ANSI->getShortcut(), InputOption::VALUE_NEGATABLE, 'Force (or disable --no-ansi) ANSI output'),
            new InputOption(self::CONFIG->getName(), self::CONFIG->getShortcut(), InputOption::VALUE_REQUIRED, 'Path to config file', $defaultConfigPath),
            new InputOption(self::HELP->getName(), self::HELP->getShortcut(), InputOption::VALUE_NONE, 'Display help for the given command.'),
            new InputOption(self::NO_EXIT_CODE->getName(), null, InputOption::VALUE_NONE, 'Do not exit with a non-zero code on structron errors'),
            new InputOption(self::NO_PROGRESS_BAR->getName(), null, InputOption::VALUE_NONE, 'No progress bar output'),
            new InputOption(self::VERBOSE->getName(), self::VERBOSE->getShortcut(), InputOption::VALUE_NONE, 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug'),
            new InputOption(self::VERSION->getName(), self::VERSION->getShortcut(), InputOption::VALUE_NONE, 'Display this application version'),
        ];
    }

    public static function createStructronConfigFromInput(BootstrapInputResolver $bootstrapInputResolver): StructronConfig
    {
        $structronConfig = new StructronConfig();

        if ($bootstrapInputResolver->hasOption(self::NO_EXIT_CODE)) {
            $structronConfig->disableExitCode();
        }

        if ($bootstrapInputResolver->hasOption(self::NO_PROGRESS_BAR)) {
            $structronConfig->disableProcessBar();
        }

        if ($bootstrapInputResolver->hasOption(self::PATHS)) {
            $structronConfig->paths($bootstrapInputResolver->getOptionArray(self::PATHS));
        }

        if ($bootstrapInputResolver->hasOption(self::SKIP)) {
            $structronConfig->skip($bootstrapInputResolver->getOptionArray(self::SKIP));
        }

        return $structronConfig;
    }
}
