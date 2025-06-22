<?php

declare(strict_types=1);

namespace Main\Bootstrap;

use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wundii\Structron\Bootstrap\BootstrapConfig;
use Wundii\Structron\Bootstrap\BootstrapConfigRequirer;
use Wundii\Structron\Config\OptionEnum;
use Wundii\Structron\Config\StructronConfig;

class BootstrapConfigRequirerTest extends TestCase
{
    public function getMockContainerBuilder(): ContainerBuilder
    {
        return new ContainerBuilder();
    }

    /**
     * @throws Exception
     */
    public function testGetStructronConfigWithValidConfig()
    {
        $configFile = __DIR__ . '/Files/structron-01.php';

        $bootstrapConfig = new BootstrapConfig($configFile);
        $requirer = new BootstrapConfigRequirer($bootstrapConfig);

        $structronConfig = new StructronConfig();
        $structronConfig = $requirer->loadConfigFile($structronConfig);

        $this->assertInstanceOf(StructronConfig::class, $structronConfig);
        $this->assertEquals('phpUnitTest', $structronConfig->getString(OptionEnum::PHP_EXTENSION));
    }

    public function testGetStructronConfigWithInvalidConfigReturnString()
    {
        $configFile = __DIR__ . '/Files/structron-02.php';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('BootstrapConfig ' . $configFile . ' file is not callable.');

        $bootstrapConfig = new BootstrapConfig($configFile);
        $requirer = new BootstrapConfigRequirer($bootstrapConfig);

        $structronConfig = new StructronConfig();
        $requirer->loadConfigFile($structronConfig);
    }

    public function testGetStructronConfigWithInvalidConfigReturnWithoutParameter()
    {
        $configFile = __DIR__ . '/Files/structron-03.php';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('BootstrapConfig ' . $configFile . ' file has no parameters.');

        $bootstrapConfig = new BootstrapConfig($configFile);
        $requirer = new BootstrapConfigRequirer($bootstrapConfig);

        $structronConfig = new StructronConfig();
        $requirer->loadConfigFile($structronConfig);
    }

    public function testGetStructronConfigWithInvalidConfigReturnWithWrongParameter()
    {
        $configFile = __DIR__ . '/Files/structron-04.php';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('BootstrapConfig ' . $configFile . ' file has no structronconfig parameter.');

        $bootstrapConfig = new BootstrapConfig($configFile);
        $requirer = new BootstrapConfigRequirer($bootstrapConfig);

        $structronConfig = new StructronConfig();
        $requirer->loadConfigFile($structronConfig);
    }
}
