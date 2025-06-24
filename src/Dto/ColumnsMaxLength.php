<?php

declare(strict_types=1);

namespace Wundii\Structron\Dto;

final class ColumnsMaxLength
{
    public function __construct(
        public int $name = 0,
        public int $type = 0,
        public int $default = 7,
        public int $description = 0,
    ) {
    }
}
