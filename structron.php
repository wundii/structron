<?php

declare(strict_types=1);

use Wundii\Structron\Config\StructronConfig;

return static function (StructronConfig $structronConfig): void {
    $structronConfig->docPath('tests/E2E/Docs');
    $structronConfig->paths(['tests/E2E/Dto']);
    $structronConfig->setIndentFileIteration();
};