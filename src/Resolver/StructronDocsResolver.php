<?php

declare(strict_types=1);

namespace Wundii\Structron\Resolver;

use Wundii\Structron\Config\OptionEnum;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Dto\StructronCollectionDto;

class StructronDocsResolver
{
    public function __construct(
        private StructronConfig $structronConfig,
    ) {
    }

    public function resolve(StructronCollectionDto $structronCollectionDto): bool
    {
        $columnsMaxLength = 0;

        foreach ($structronCollectionDto->getCollection() as $structronRowDto) {
            $columnsMaxLength = max($columnsMaxLength, strlen($structronRowDto->getName()));
        }

        $path = $this->structronConfig->getString(OptionEnum::DOC_PATH);

        return (bool) file_put_contents($path . '/index.md', '# Structron Documentation');
    }
}
