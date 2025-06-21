<?php

declare(strict_types=1);

namespace Main\Bootstrap;

use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Wundii\Structron\Bootstrap\BootstrapConfig;
use Wundii\Structron\Bootstrap\BootstrapConfigResolver;
use Wundii\Structron\Bootstrap\BootstrapInputResolver;

class BootstrapConfigResolverTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGetBootstrapConfigWithValidConfig()
    {
        $configFile = __DIR__ . '/Files/structron-01.php';

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/structron', '--config', $configFile]));
        $resolver = new BootstrapConfigResolver($inputResolver);

        $bootstrapConfig = $resolver->getBootstrapConfig();

        $this->assertInstanceOf(BootstrapConfig::class, $bootstrapConfig);
        $this->assertEquals($configFile, $bootstrapConfig->getBootstrapConfigFile());
    }

    /**
     * @throws Exception
     */
    public function testGetBootstrapConfigWithConfigFilePathEmpty()
    {
        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/structron', '--config']));
        $resolver = new BootstrapConfigResolver($inputResolver);

        $bootstrapConfig = $resolver->getBootstrapConfig();

        $this->assertInstanceOf(BootstrapConfig::class, $bootstrapConfig);
        $this->assertEquals(getcwd() . '/structron.php', $bootstrapConfig->getBootstrapConfigFile());
    }

    /**
     * @throws Exception
     */
    public function testGetBootstrapConfigWithFileDoesNotExist()
    {
        $configFile = __DIR__ . '/Files/structron-no-exist.php';

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/structron', '--config', $configFile]));
        $resolver = new BootstrapConfigResolver($inputResolver);

        $bootstrapConfig = $resolver->getBootstrapConfig();

        $this->assertInstanceOf(BootstrapConfig::class, $bootstrapConfig);
        $this->assertNull($bootstrapConfig->getBootstrapConfigFile());
    }
}
