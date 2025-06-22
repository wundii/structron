<?php

declare(strict_types=1);

namespace Init\Config;

use PHPUnit\Framework\TestCase;
use Wundii\Structron\Config\OptionEnum;
use Wundii\Structron\Config\StructronConfig;

class StructronConfigTest extends TestCase
{
    public function testGetDefaultPathsDefault()
    {
        $structronConfig = new StructronConfig();

        $this->assertEquals([], $structronConfig->getArrayWithStrings(OptionEnum::PATHS));
    }

    public function testSetPaths()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->paths(['path/to/dir1', 'path/to/dir2']);

        $this->assertEquals(['path/to/dir1', 'path/to/dir2'], $structronConfig->getArrayWithStrings(OptionEnum::PATHS));
    }

    public function testGetDefaultSkip()
    {
        $structronConfig = new StructronConfig();

        $this->assertEquals([], $structronConfig->getArrayWithStrings(OptionEnum::SKIP));
    }

    public function testSetSkip()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->skip(['className', 'file1.php', 'file2.php']);

        $this->assertEquals(['className', 'file1.php', 'file2.php'], $structronConfig->getArrayWithStrings(OptionEnum::SKIP));
    }

    public function testGetDefaultNoExitCode()
    {
        $structronConfig = new StructronConfig();

        $this->assertFalse($structronConfig->getBoolean(OptionEnum::NO_EXIT_CODE));
    }

    public function testSetNoExitCode()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->disableExitCode();

        $this->assertTrue($structronConfig->getBoolean(OptionEnum::NO_EXIT_CODE));
    }

    public function testGetDefaultNoProcessBar()
    {
        $structronConfig = new StructronConfig();

        $this->assertFalse($structronConfig->getBoolean(OptionEnum::NO_PROGRESS_BAR));
    }

    public function testSetNoProcessBar()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->disableProcessBar();

        $this->assertTrue($structronConfig->getBoolean(OptionEnum::NO_PROGRESS_BAR));
    }

    public function testGetDefaultPhpExtension()
    {
        $structronConfig = new StructronConfig();

        $this->assertEquals('php', $structronConfig->getString(OptionEnum::PHP_EXTENSION));
    }

    public function testSetPhpExtension()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->phpExtension('php8');

        $this->assertEquals('php8', $structronConfig->getString(OptionEnum::PHP_EXTENSION));
    }
}
