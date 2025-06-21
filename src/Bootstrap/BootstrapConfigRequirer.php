<?php

declare(strict_types=1);

namespace Wundii\Structron\Bootstrap;

use Closure;
use Exception;
use ReflectionFunction;
use ReflectionNamedType;
use Wundii\Structron\Config\StructronConfig;

final class BootstrapConfigRequirer
{
    public function __construct(
        private readonly BootstrapConfig $bootstrapConfig
    ) {
    }

    /**
     * @throws Exception
     */
    public function loadConfigFile(StructronConfig $structronConfig): StructronConfig
    {
        $bootstrapConfigFile = $this->bootstrapConfig->getBootstrapConfigFile();
        if ($bootstrapConfigFile === null) {
            return $structronConfig;
        }

        $fn = require_once $this->bootstrapConfig->getBootstrapConfigFile();
        if (! is_callable($fn)) {
            throw new Exception('BootstrapConfig ' . $this->bootstrapConfig->getBootstrapConfigFile() . ' file is not callable.');
        }

        if (! $fn instanceof Closure) {
            throw new Exception('BootstrapConfig ' . $this->bootstrapConfig->getBootstrapConfigFile() . ' file is not a closure.');
        }

        $reflectionFunction = new ReflectionFunction($fn);
        if ($reflectionFunction->getNumberOfParameters() === 0) {
            throw new Exception('BootstrapConfig ' . $this->bootstrapConfig->getBootstrapConfigFile() . ' file has no parameters.');
        }

        foreach ($reflectionFunction->getParameters() as $reflectionParameter) {
            if (
                $reflectionParameter->hasType()
                && $reflectionParameter->getType() instanceof ReflectionNamedType
                && $reflectionParameter->getType()->getName() === StructronConfig::class
            ) {
                break;
            }

            // structronconfig parameter must be on the first position
            throw new Exception('BootstrapConfig ' . $this->bootstrapConfig->getBootstrapConfigFile() . ' file has no structronconfig parameter.');
        }

        $fn($structronConfig);

        return $structronConfig;
    }
}
