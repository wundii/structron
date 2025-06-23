<?php

declare(strict_types=1);

namespace Wundii\Structron\Tests\Main\Resolver\Config;

use PHPUnit\Framework\TestCase;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Resolver\Config\StructronPathsResolver;

class StructronPathsResolverTest extends TestCase
{
    public function testResolve()
    {
        $structronConfig = new StructronConfig();
        $structronPathsResolver = new StructronPathsResolver();

        $this->assertEquals([getcwd() . DIRECTORY_SEPARATOR], $structronPathsResolver->resolve($structronConfig));

        $structronConfig->paths(['nonexistent/path', 'nonexistent/file.php']);
        $this->assertEquals([], $structronPathsResolver->resolve($structronConfig));

        $structronConfig->paths([__DIR__, __FILE__, 'nonexistent/path']);
        $this->assertEquals([__DIR__, __FILE__], $structronPathsResolver->resolve($structronConfig));
    }
}
