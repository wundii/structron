<?php

declare(strict_types=1);

namespace Wundii\Structron\Tests\E2E\Console;

use Wundii\Structron\Config\StructronConfig;

return static function (StructronConfig $structronConfig): void {
    $structronConfig->paths(['src']);
};
