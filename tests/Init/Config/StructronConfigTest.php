<?php

declare(strict_types=1);

namespace Init\Config;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Config\OptionEnum;

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

    public function testGetDefaultAsyncProcess()
    {
        $structronConfig = new StructronConfig();

        $this->assertEquals(10, $structronConfig->getInteger(OptionEnum::ASYNC_PROCESS));
    }

    public function testSetAsyncProcess()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->asyncProcess(5);

        $this->assertEquals(5, $structronConfig->getInteger(OptionEnum::ASYNC_PROCESS));
    }

    public function testGetDefaultAsyncProcessTimeout()
    {
        $structronConfig = new StructronConfig();

        $this->assertEquals(10, $structronConfig->getInteger(OptionEnum::ASYNC_PROCESS));
    }

    public function testSetAsyncProcessTimeout()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->asyncProcessTimeout(120);

        $this->assertEquals(120, $structronConfig->getInteger(OptionEnum::ASYNC_PROCESS_TIMEOUT));
    }

    public function testGetDefaultConsoleNotice()
    {
        $structronConfig = new StructronConfig();

        $this->assertTrue($structronConfig->getBoolean(OptionEnum::CONSOLE_NOTICE));
    }

    public function testDisableConsoleNotice()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->disableConsoleNotice();

        $this->assertFalse($structronConfig->getBoolean(OptionEnum::CONSOLE_NOTICE));
    }

    public function testGetDefaultConsoleWarning()
    {
        $structronConfig = new StructronConfig();

        $this->assertTrue($structronConfig->getBoolean(OptionEnum::CONSOLE_WARNING));
    }

    public function testDisableConsoleWarning()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->disableWarning();

        $this->assertFalse($structronConfig->getBoolean(OptionEnum::CONSOLE_WARNING));
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

    public function testGetDefaultCache()
    {
        $structronConfig = new StructronConfig();

        $this->assertEquals(FilesystemAdapter::class, $structronConfig->getString(OptionEnum::CACHE_CLASS));
    }

    public function testSetCacheSuccess()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->cacheClass(NullAdapter::class);

        $this->assertEquals(NullAdapter::class, $structronConfig->getString(OptionEnum::CACHE_CLASS));
    }

    public function testSetCacheFail()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->cacheClass(ArrayAdapter::class);

        $this->assertEquals(NullAdapter::class, $structronConfig->getString(OptionEnum::CACHE_CLASS));
    }

    public function testGetDefaultCacheDirectory()
    {
        $structronConfig = new StructronConfig();

        $this->assertEquals('.structron', $structronConfig->getString(OptionEnum::CACHE_DIR));
    }

    public function testSetCacheDirectory()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->cacheDirectory(__DIR__ . '/path/to/cache/folder');

        $this->assertEquals(__DIR__ . '/path/to/cache/folder', $structronConfig->getString(OptionEnum::CACHE_DIR));
    }

    public function testGetDefaultMemoryLimit()
    {
        $structronConfig = new StructronConfig();

        $this->assertEquals('512M', $structronConfig->getString(OptionEnum::MEMORY_LIMIT));
    }

    public function testSetMemoryLimit()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->memoryLimit('1G');

        $this->assertEquals('1G', $structronConfig->getString(OptionEnum::MEMORY_LIMIT));
    }

    public function testGetDefaultPhpCgiExecutable()
    {
        $structronConfig = new StructronConfig();

        $this->assertEquals('php', $structronConfig->getString(OptionEnum::PHP_CGI_EXECUTABLE));
    }

    public function testSetPhpCgiExecutable()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->phpCgiExecutable('php.exe');

        $this->assertEquals('php.exe', $structronConfig->getString(OptionEnum::PHP_CGI_EXECUTABLE));
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
