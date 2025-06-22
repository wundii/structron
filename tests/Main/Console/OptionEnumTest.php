<?php

declare(strict_types=1);

namespace Main\Console;

use PHPUnit\Framework\TestCase;
use ReflectionEnum;
use Symfony\Component\Console\Input\ArgvInput;
use Wundii\Structron\Bootstrap\BootstrapInputResolver;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Console\OptionEnum;

class OptionEnumTest extends TestCase
{
    public static function enumValues(): array
    {
        $reflectionEnum = new ReflectionEnum(OptionEnum::class);

        return array_map(static fn ($enum) => $enum->getValue(), $reflectionEnum->getCases());
    }

    public function testGetName()
    {
        $this->assertEquals('--ansi', OptionEnum::ANSI->getName());
        $this->assertEquals('--config', OptionEnum::CONFIG->getName());
        $this->assertEquals('--help', OptionEnum::HELP->getName());
        $this->assertEquals('--verbose', OptionEnum::VERBOSE->getName());
        $this->assertEquals('--version', OptionEnum::VERSION->getName());
    }

    public function testGetShortcut()
    {
        $this->assertEquals('', OptionEnum::ANSI->getShortcut());
        $this->assertEquals('-c', OptionEnum::CONFIG->getShortcut());
        $this->assertEquals('-h', OptionEnum::HELP->getShortcut());
        $this->assertEquals('-v|vv|vvv', OptionEnum::VERBOSE->getShortcut());
        $this->assertEquals('-V', OptionEnum::VERSION->getShortcut());
    }

    public function testAllOptionNamesAreUnique()
    {
        $optionNames = [];

        foreach (self::enumValues() as $option) {
            $name = $option->getName();
            $this->assertFalse(in_array($name, $optionNames, true), "Duplicate option name: {$name}");
            $optionNames[] = $name;
        }

        $this->assertCount(count(self::enumValues()), $optionNames, 'Missing option names');
    }

    public function testAllShortcutsAreUnique()
    {
        $emptyShortcuts = 0;
        $shortcuts = [];

        foreach (self::enumValues() as $option) {
            $shortcut = $option->getShortcut();
            if ($shortcut === '') {
                ++$emptyShortcuts;
                continue;
            }

            $this->assertFalse(in_array($shortcut, $shortcuts, true), "Duplicate shortcut: {$shortcut}");
            $shortcuts[] = $shortcut;
        }

        $this->assertCount(count(self::enumValues()) - $emptyShortcuts, $shortcuts, 'Missing shortcuts');
    }

    public function testCreateStructronConfigFromInputDefault()
    {
        unset($_SERVER['argv']);
        $bootstrapInputResolver = new BootstrapInputResolver(
            new ArgvInput()
        );

        $expected = new StructronConfig();
        $structronConfig = OptionEnum::createStructronConfigFromInput($bootstrapInputResolver);

        $this->assertEquals($expected, $structronConfig);
    }

    public function testCreateStructronConfigFromInputNoExitCode()
    {
        unset($_SERVER['argv']);
        $bootstrapInputResolver = new BootstrapInputResolver(
            new ArgvInput(['bin/structron', OptionEnum::NO_EXIT_CODE->getName()])
        );

        $expected = new StructronConfig();
        $expected->disableExitCode();
        $structronConfig = OptionEnum::createStructronConfigFromInput($bootstrapInputResolver);

        $this->assertEquals($expected, $structronConfig);
    }

    public function testCreateStructronConfigFromInputNoProgressBar()
    {
        unset($_SERVER['argv']);
        $bootstrapInputResolver = new BootstrapInputResolver(
            new ArgvInput(['bin/structron', OptionEnum::NO_PROGRESS_BAR->getName()])
        );

        $expected = new StructronConfig();
        $expected->disableProcessBar();
        $structronConfig = OptionEnum::createStructronConfigFromInput($bootstrapInputResolver);

        $this->assertEquals($expected, $structronConfig);
    }

    public function testCreateStructronConfigFromInputPaths()
    {
        $paths = [
            '--paths=src/',
            '--paths=vendor/',
        ];
        $_SERVER['argv'] = $paths;
        $bootstrapInputResolver = new BootstrapInputResolver(
            new ArgvInput(['bin/structron', ...$paths])
        );

        $expected = new StructronConfig();
        $expected->paths([
            'src/',
            'vendor/',
        ]);
        $structronConfig = OptionEnum::createStructronConfigFromInput($bootstrapInputResolver);

        $this->assertEquals($expected, $structronConfig);

        unset($_SERVER['argv']);
    }

    public function testCreateStructronConfigFromInputSkip()
    {
        $skip = [
            '--skip=test/',
            '--skip=var/',
        ];
        $_SERVER['argv'] = $skip;
        $bootstrapInputResolver = new BootstrapInputResolver(
            new ArgvInput(['bin/structron', ...$skip])
        );

        $expected = new StructronConfig();
        $expected->skip([
            'test/',
            'var/',
        ]);
        $structronConfig = OptionEnum::createStructronConfigFromInput($bootstrapInputResolver);

        $this->assertEquals($expected, $structronConfig);

        unset($_SERVER['argv']);
    }

    public function testCreateStructronConfigFromInputMixed()
    {
        $bootstrapInputResolver = new BootstrapInputResolver(
            new ArgvInput(['bin/structron',
                OptionEnum::NO_PROGRESS_BAR->getName(),
            ])
        );

        $expected = new StructronConfig();
        $expected->disableProcessBar();
        $structronConfig = OptionEnum::createStructronConfigFromInput($bootstrapInputResolver);

        $this->assertEquals($expected, $structronConfig);
    }
}
