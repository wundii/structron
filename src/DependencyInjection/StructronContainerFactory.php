<?php

declare(strict_types=1);

namespace Wundii\Structron\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Wundii\Structron\Bootstrap\BootstrapConfig;
use Wundii\Structron\Bootstrap\BootstrapConfigInitializer;
use Wundii\Structron\Bootstrap\BootstrapConfigRequirer;
use Wundii\Structron\Bootstrap\BootstrapConfigResolver;
use Wundii\Structron\Bootstrap\BootstrapInputResolver;
use Wundii\Structron\Config\StructronConfig;

final class StructronContainerFactory
{
    /**
     * @throws Exception
     */
    public function createFromArgvInput(ArgvInput $argvInput): ContainerBuilder
    {
        $bootstrapInputResolver = new BootstrapInputResolver($argvInput);
        $bootstrapConfigResolver = new BootstrapConfigResolver($bootstrapInputResolver);
        $bootstrapConfig = $bootstrapConfigResolver->getBootstrapConfig();
        $bootstrapConfigRequirer = new BootstrapConfigRequirer($bootstrapConfig);

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->register(BootstrapConfig::class, BootstrapConfig::class)
            ->setPublic(true)
            ->setArgument('$bootstrapConfigFile', $bootstrapConfig->getBootstrapConfigFile());
        $containerBuilder->autowire(BootstrapConfigInitializer::class, BootstrapConfigInitializer::class)
            ->setPublic(true);
        $containerBuilder->autowire(BootstrapConfigResolver::class, BootstrapConfigResolver::class)
            ->setPublic(true);
        $containerBuilder->autowire(BootstrapInputResolver::class, BootstrapInputResolver::class)
            ->setPublic(true);
        $containerBuilder->autowire(StructronConfig::class, StructronConfig::class)
            ->setPublic(true);

        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__));
        $phpFileLoader->load(__DIR__ . '/../../config/config.php');

        $containerBuilder->compile();

        $structronConfig = $containerBuilder->get(StructronConfig::class);
        if ($structronConfig instanceof StructronConfig) {
            $bootstrapConfigRequirer->loadConfigFile($structronConfig);
        }

        return $containerBuilder;
    }
}
