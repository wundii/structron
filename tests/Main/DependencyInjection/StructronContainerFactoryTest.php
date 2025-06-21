<?php

declare(strict_types=1);

namespace Main\DependencyInjection;

use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Config\OptionEnum;
use Wundii\Structron\DependencyInjection\StructronContainerFactory;

class StructronContainerFactoryTest extends TestCase
{
    /**
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testCreateFromArgvInputReturnsContainerInterface()
    {
        $argvInput = $this->createMock(ArgvInput::class);

        $factory = new StructronContainerFactory();
        $container = $factory->createFromArgvInput($argvInput);

        $this->assertInstanceOf(ContainerInterface::class, $container);
        $this->assertEquals('php', $container->get(StructronConfig::class)->getString(OptionEnum::PHP_CGI_EXECUTABLE));
    }

    /**
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testCreateFromArgvInputWithDifferentStructronConfigFile()
    {
        $argvInput = $this->createMock(ArgvInput::class);
        $argvInput->method('hasParameterOption')->willReturn(true);
        $argvInput->method('getParameterOption')->willReturn(__DIR__ . '/Files/structron.php');

        $factory = new StructronContainerFactory();
        $container = $factory->createFromArgvInput($argvInput);

        $this->assertInstanceOf(ContainerInterface::class, $container);
        $this->assertEquals('TimTest', $container->get(StructronConfig::class)->getString(OptionEnum::PHP_CGI_EXECUTABLE));
    }
}
