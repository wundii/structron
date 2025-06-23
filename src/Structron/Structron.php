<?php

declare(strict_types=1);

namespace Wundii\Structron\Structron;

use Exception;
use ReflectionClass;
use ReflectionException;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\Structron\Console\Output\StructronSymfonyStyle;
use Wundii\Structron\Dto\ReflectionDto;
use Wundii\Structron\Dto\StructronCollectionDto;
use Wundii\Structron\Dto\StructronFileDto;
use Wundii\Structron\Finder\StructronFinder;
use Wundii\Structron\Resolver\StructronDocsResolver;
use Wundii\Structron\Resolver\StructronFileResolver;

final class Structron
{
    /**
     * @var array<string, ReflectionDto>
     */
    public array $declaredClasses = [];

    public function __construct(
        private readonly StructronSymfonyStyle $structronSymfonyStyle,
        private readonly StructronFinder $structronFinder,
        private readonly StructronFileResolver $structronFileResolver,
        private readonly StructronDocsResolver $structronDocsResolver,
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

        $includedFiles = get_included_files();

        /**
         * Load all files from the structronFinder.
         */
        foreach ($this->structronFinder->files() as $structronFinder) {
            $filePath = $structronFinder->getRealPath();

            if (! in_array($filePath, $includedFiles, true)) {
                require_once $filePath;
            }
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
        $structronFileDtos = [];
        foreach ($this->structronFinder->files() as $structronFinder) {
            $pathname = $structronFinder->getPathname();
            if (! array_key_exists($pathname, $this->declaredClasses)) {
                throw new Exception(sprintf('File %s does not exist', $pathname));
            }

            $this->structronSymfonyStyle->progressBarAdvance();

            $structronFileDto = $this->structronFileResolver->resolve(
                $this->declaredClasses[$pathname],
            );

            if (! $structronFileDto instanceof StructronFileDto) {
                continue;
            }

            $structronFileDtos[] = $structronFileDto;
        }

        $structronCollectionDto = new StructronCollectionDto($structronFileDtos);

        $this->structronDocsResolver->resolve($structronCollectionDto);

        $this->structronSymfonyStyle->progressBarFinish();
        // $this->processResultToConsole($processResult);
    }
}
