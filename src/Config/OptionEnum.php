<?php

declare(strict_types=1);

namespace Wundii\Structron\Config;

enum OptionEnum: string
{
    /**
     * @internal
     */
    case NO_EXIT_CODE = 'no-exit-code';

    /**
     * @internal
     */
    case NO_PROGRESS_BAR = 'no-progress-bar';

    /**
     * @internal
     */
    case PATHS = 'paths';

    /**
     * @internal
     */
    case PHP_EXTENSION = 'php-extension';

    /**
     * @internal
     */
    case SKIP = 'skip';

    /**
     * @internal
     */
    case DOC_PATH = 'doc-path';

    /**
     * @internal
     */
    case INDENT_FILE_ITERATION = 'indent-file-iteration';

    /**
     * @internal only for unit tests
     */
    case TEST = 'test';
}
