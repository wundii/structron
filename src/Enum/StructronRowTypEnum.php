<?php

declare(strict_types=1);

namespace Wundii\Structron\Enum;

enum StructronRowTypEnum: string
{
    case HEADER = 'header';
    case ROW = 'row';
    case SUBHEADER = 'subheader';
}
