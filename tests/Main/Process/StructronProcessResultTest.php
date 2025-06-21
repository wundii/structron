<?php

declare(strict_types=1);

namespace Main\Process;

use PHPUnit\Framework\TestCase;
use Wundii\Structron\Process\StructronProcessResult;
use Wundii\Structron\Process\StatusEnum;

class StructronProcessResultTest extends TestCase
{
    public function testGetters()
    {
        $status = StatusEnum::OK;
        $filename = __DIR__ . '/example.php';
        $result = 'Some structron result';
        $line = 10;

        $structronResult = new StructronProcessResult($status, $filename, $result, $line);

        $this->assertSame($status, $structronResult->getStatus());
        $this->assertSame($filename, $structronResult->getFilename());
        $this->assertSame($result, $structronResult->getResult());
        $this->assertSame($line, $structronResult->getLine());
    }
}
