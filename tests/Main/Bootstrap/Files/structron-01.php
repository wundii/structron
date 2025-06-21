<?php

declare(strict_types=1);

use Wundii\Structron\Config\StructronConfig;

return static function (StructronConfig $structronConfig): void {
    $structronConfig->phpCgiExecutable('phpUnitTest');
};
