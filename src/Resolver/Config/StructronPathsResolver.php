<?php

declare(strict_types=1);

namespace Wundii\Structron\Resolver\Config;

use Wundii\Structron\Config\StructronConfigParameter;
use Wundii\Structron\Config\OptionEnum;

final class StructronPathsResolver
{
    /**
     * @var string[]
     */
    private array $paths = [];

    /**
     * @return string[]
     */
    public function resolve(StructronConfigParameter $structronConfigParameter): array
    {
        $this->paths = $structronConfigParameter->getArrayWithStrings(OptionEnum::PATHS);

        if ($this->paths === []) {
            return [getcwd() . DIRECTORY_SEPARATOR];
        }

        foreach ($this->paths as $key => $path) {
            if (is_dir($path)) {
                continue;
            }

            if (is_file($path)) {
                continue;
            }

            unset($this->paths[$key]);
        }

        return array_values($this->paths);
    }
}
