<?php

declare(strict_types=1);

namespace Wundii\Structron\Tests\Main\Resolver\Config;

use PHPUnit\Framework\TestCase;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Resolver\Config\StructronSkipPathsResolver;

class StructronSkipPathsResolverTest extends TestCase
{
    public function testResolve()
    {
        $structronConfig = new StructronConfig();
        $skipPathsResolver = new StructronSkipPathsResolver();

        // Test case 1: No skip paths
        $this->assertEquals([], $skipPathsResolver->resolve($structronConfig));

        // Test case 2: Skip existing class
        $structronConfig->skip([StructronConfig::class]);
        $this->assertEquals([], $skipPathsResolver->resolve($structronConfig));

        // Test case 3: One skip path
        $structronConfig->skip(['tests/Init/Config']);
        $this->assertEquals([getcwd() . '/tests/Init/Config'], $skipPathsResolver->resolve($structronConfig));

        // Test case 4: Multiple skip paths
        $structronConfig->skip(['tests/Main/Bootstrap', 'tests/Main/Console']);
        $this->assertEquals([
            getcwd() . '/tests/Main/Bootstrap',
            getcwd() . '/tests/Main/Console',
        ], $skipPathsResolver->resolve($structronConfig));

        // Test case 5: Invalid skip path
        $structronConfig->skip(['/nonexistent/path']);
        $this->assertEquals([], $skipPathsResolver->resolve($structronConfig));

        // Test case 6: Skip path starting with DIRECTORY_SEPARATOR
        $structronConfig->skip([DIRECTORY_SEPARATOR . 'tests/Main/Bootstrap']);
        $this->assertEquals([getcwd() . '/tests/Main/Bootstrap'], $skipPathsResolver->resolve($structronConfig));

        // Test case 7: One skip path from root directory
        $structronConfig->skip([__DIR__]);
        $this->assertEquals([getcwd() . '/tests/Main/Resolver/Config'], $skipPathsResolver->resolve($structronConfig));
    }
}
