<?php

declare(strict_types=1);

namespace Wundii\Structron\Finder;

use ArrayIterator;
use Iterator;
use LogicException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Config\OptionEnum;
use Wundii\Structron\Resolver\Config\StructronPathsResolver;
use Wundii\Structron\Resolver\Config\StructronSkipPathsResolver;

final class StructronFinder extends Finder
{
    public function __construct(
        private readonly StructronSkipPathsResolver $structronSkipPathsResolver,
        private readonly StructronPathsResolver $structronPathsResolver,
    ) {
        parent::__construct();
    }

    public function getFilesFromStructronConfig(StructronConfig $structronConfig): self
    {
        $excludes = $this->structronSkipPathsResolver->resolve($structronConfig);
        $extension = $structronConfig->getString(OptionEnum::PHP_CGI_EXECUTABLE);

        foreach ($this->structronPathsResolver->resolve($structronConfig) as $path) {
            if (! is_dir($path) && ! is_file($path)) {
                continue;
            }

            if (is_dir($path)) {
                $this->append($this->getFinderFromPath($extension, $path, $excludes));
                continue;
            }

            if (is_file($path)) {
                $this->append([new SplFileInfo($path, $path, $path)]);
            }
        }

        return $this;
    }

    /**
     * @param string[] $excludes
     */
    public function getFinderFromPath(string $extension, string $path, array $excludes = []): Finder
    {
        $finder = new Finder();

        $path = realpath($path);
        if ($path === false) {
            return $finder;
        }

        $finder->files();
        $finder->name('*.' . $extension);
        $finder->in($path);

        foreach ($excludes as $exclude) {
            if (! str_starts_with($exclude, $path)) {
                continue;
            }

            $finder->exclude(substr($exclude, strlen($path) + 1));
        }

        return $finder;
    }

    public function count(): int
    {
        try {
            return parent::count();
        } catch (LogicException) {
            return 0;
        }
    }

    public function getIterator(): Iterator
    {
        try {
            return parent::getIterator();
        } catch (LogicException) {
            return new ArrayIterator();
        }
    }
}
