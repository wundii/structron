<?php

declare(strict_types=1);

namespace Wundii\Structron\Structron;

use Exception;
use ReflectionClass;
use ReflectionException;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\Structron\Config\OptionEnum;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Console\Output\StructronSymfonyStyle;
use Wundii\Structron\Dto\ReflectionDto;
use Wundii\Structron\Dto\StructronCollectionDto;
use Wundii\Structron\Finder\StructronFinder;
use Wundii\Structron\Resolver\StructronCollectionResolver;
use Wundii\Structron\Resolver\StructronDocsResolver;

final class Structron
{
    /**
     * @var array<string, ReflectionDto>
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
     * @throws Exception
     */
    public function run(): void
    {
        $count = $this->structronFinder->count();

        $this->structronSymfonyStyle->progressBarStart($count);

        $test = $this->structronConfig->getParameter(OptionEnum::TEST);
        $this->structronConfig->setParameter(OptionEnum::TEST, $test);

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
            $this->declaredClasses[$realpath] = new ReflectionDto(
                $realpath,
                $ref->getName(),
                $ref->getShortName(),
            );
        }

        /**
         * Iterate through all files and resolve the attributes.
         */
        foreach ($this->structronFinder->files() as $structronFinder) {
            $pathname = $structronFinder->getPathname();
            if (! array_key_exists($pathname, $this->declaredClasses)) {
                throw new Exception(sprintf('File %s does not exist', $pathname));
            }

            $this->structronSymfonyStyle->progressBarAdvance();

            $structronCollectionResolver = new StructronCollectionResolver();
            $structronCollectionDto = $structronCollectionResolver->resolve(
                $this->declaredClasses[$pathname],
            );

            if (! $structronCollectionDto instanceof StructronCollectionDto) {
                continue;
            }

            dd($structronCollectionDto);

            $structronDocsResolver = new StructronDocsResolver($this->structronConfig);
            $structronDocsResolver->resolve($structronCollectionDto);
        }

        $this->structronSymfonyStyle->progressBarFinish();
        // $this->processResultToConsole($processResult);
    }
}
