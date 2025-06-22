<?php

declare(strict_types=1);

namespace Wundii\Structron\Structron;

use Exception;
use ReflectionClass;
use ReflectionException;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Resolver\ReflectionObjectResolver;
use Wundii\Structron\Config\OptionEnum;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Console\Output\StructronSymfonyStyle;
use Wundii\Structron\Finder\StructronFinder;

final class Structron
{
    /**
     * @var array<string, string>
     */
    public array $declaredClasses = [];

    public function __construct(
        private readonly StructronSymfonyStyle $structronSymfonyStyle,
        private readonly StructronConfig $structronConfig,
        private readonly StructronFinder $structronFinder,
    ) {
    }

    /**
     * @throws ReflectionException
     * @throws DataMapperException
     */
    public function run(): void
    {
        $count = $this->structronFinder->count();

        $this->structronSymfonyStyle->progressBarStart($count);

        $test = $this->structronConfig->getParameter(OptionEnum::TEST);
        $this->structronConfig->setParameter(OptionEnum::TEST, $test);
        $reflectionObjectResolver = new ReflectionObjectResolver();

        /**
         * Load all files from the structronFinder.
         */
        foreach ($this->structronFinder->files() as $structronFinder) {
            require_once $structronFinder->getPathname();
        }

        /**
         * Collect all declared classes.
         */
        foreach (get_declared_classes() as $className) {
            $ref = new ReflectionClass($className);
            if (! $ref->getFileName()) {
                continue;
            }

            $realpath = (string) realpath($ref->getFileName());
            $name = $ref->getName();
            $this->declaredClasses[$realpath] = $name;
        }

        foreach ($this->structronFinder->files() as $structronFinder) {
            $pathname = $structronFinder->getPathname();

            if (! array_key_exists($pathname, $this->declaredClasses)) {
                throw new Exception(sprintf('File %s does not exist', $pathname));
            }

            $className = $this->declaredClasses[$pathname];

            dump($reflectionObjectResolver->resolve($className));

            // $this->structronSymfonyStyle->generateStructron(
            //     $structronFinder->getPathname(),
            // );
            //
            // $this->structronSymfonyStyle->progressBarAdvance();
        }

        $this->structronSymfonyStyle->progressBarFinish();
        // $this->processResultToConsole($processResult);
    }
}
