<?php

declare(strict_types=1);

namespace Main\Finder;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Finder\StructronFinder;
use Wundii\Structron\Resolver\Config\StructronPathsResolver;
use Wundii\Structron\Resolver\Config\StructronSkipPathsResolver;

class StructronFinderTest extends TestCase
{
    public function testGetFilesFromStructronConfigWithDirectory()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->paths([__DIR__ . '/Files']);

        $structronFinder = new StructronFinder(
            new StructronSkipPathsResolver(),
            new StructronPathsResolver(),
        );
        $result = $structronFinder->getFilesFromStructronConfig($structronConfig);

        $this->assertInstanceOf(StructronFinder::class, $result);
        $this->assertEquals(3, $result->count());
    }

    public function testGetFilesFromStructronConfigWithDirectoryAndExcludes()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->paths([__DIR__ . '/Files']);
        $structronConfig->skip([__DIR__ . '/Files/Folder']);

        $structronFinder = new StructronFinder(
            new StructronSkipPathsResolver(),
            new StructronPathsResolver(),
        );
        $result = $structronFinder->getFilesFromStructronConfig($structronConfig);

        $this->assertInstanceOf(StructronFinder::class, $result);
        $this->assertEquals(2, $result->count());
    }

    public function testGetFilesFromStructronConfigWithInvalidPath()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->paths(['/path/to/invalid']);
        $structronFinder = new StructronFinder(
            new StructronSkipPathsResolver(),
            new StructronPathsResolver(),
        );
        $result = $structronFinder->getFilesFromStructronConfig($structronConfig);

        $this->assertInstanceOf(StructronFinder::class, $result);
        $this->assertEquals(0, $result->count());
    }

    public function testGetFinderFromPath()
    {
        $structronFinder = new StructronFinder(
            new StructronSkipPathsResolver(),
            new StructronPathsResolver(),
        );
        $result = $structronFinder->getFinderFromPath('php', __DIR__ . '/Files');

        $this->assertInstanceOf(Finder::class, $result);
        $this->assertTrue($result->hasResults());
    }

    public function testCountByFileCount1()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->paths([__DIR__ . '/Files/File3.php']);
        $structronFinder = new StructronFinder(
            new StructronSkipPathsResolver(),
            new StructronPathsResolver(),
        );
        $structronFinder = $structronFinder->getFilesFromStructronConfig($structronConfig);
        $count = $structronFinder->count();

        $this->assertEquals(1, $count);
    }

    public function testCountByFileCount0()
    {
        $structronFinder = new StructronFinder(
            new StructronSkipPathsResolver(),
            new StructronPathsResolver(),
        );
        $count = $structronFinder->count();

        $this->assertEquals(0, $count);
    }

    public function testGetIteratorByFileCount1()
    {
        $structronConfig = new StructronConfig();
        $structronConfig->paths([__DIR__ . '/Files/File4.php']);
        $structronFinder = new StructronFinder(
            new StructronSkipPathsResolver(),
            new StructronPathsResolver(),
        );
        $structronFinder = $structronFinder->getFilesFromStructronConfig($structronConfig);
        $iterator = $structronFinder->getIterator();

        $this->assertInstanceOf(Iterator::class, $iterator);
        $this->assertTrue($iterator->valid());
    }

    public function testGetIteratorByFileCount0()
    {
        $structronFinder = new StructronFinder(
            new StructronSkipPathsResolver(),
            new StructronPathsResolver(),
        );
        $iterator = $structronFinder->getIterator();

        $this->assertInstanceOf(Iterator::class, $iterator);
        $this->assertFalse($iterator->valid());
    }
}
