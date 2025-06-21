<?php

declare(strict_types=1);

namespace Wundii\Structron\Resolver\Config;

use Wundii\Structron\Config\StructronConfigParameter;
use Wundii\Structron\Config\OptionEnum;

final class StructronSkipPathsResolver
{
    /**
     * @return string[]
     */
    public function resolve(StructronConfigParameter $structronConfigParameter): array
    {
        $paths = [];
        $skip = $structronConfigParameter->getArrayWithStrings(OptionEnum::SKIP);

        foreach ($skip as $path) {
            if (class_exists($path)) {
                continue;
            }

            if (! str_starts_with($path, (string) getcwd())) {
                if (str_starts_with($path, DIRECTORY_SEPARATOR)) {
                    $path = substr($path, 1);
                }

                $path = getcwd() . DIRECTORY_SEPARATOR . $path;
            }

            if (! is_dir($path)) {
                continue;
            }

            $realPath = realpath($path);

            if ($realPath !== false) {
                $paths[] = $realPath;
            }
        }

        return $paths;
    }
}
