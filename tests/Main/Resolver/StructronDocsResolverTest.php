<?php

declare(strict_types=1);

namespace Wundii\Structron\Tests\Main\Resolver;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Wundii\Structron\Config\OptionEnum;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Dto\StructronCollectionDto;
use Wundii\Structron\Dto\StructronFileDto;
use Wundii\Structron\Dto\StructronRowDto;
use Wundii\Structron\Enum\StructronRowTypEnum;
use Wundii\Structron\Resolver\StructronDocsResolver;

class StructronDocsResolverTest extends TestCase
{
    public function testResolveGeneratesDocsWithoutData(): void
    {
        $filesystem = $this->createMock(Filesystem::class);
        $collectionDto = new StructronCollectionDto([]);
        $config = new StructronConfig();
        $config->setParameter(OptionEnum::TEST, true);
        $config->docPath('Docs');
        $config->phpExtension('php');
        $config->paths(['tests/E2E/Dto']);

        $resolver = new StructronDocsResolver($config, $filesystem);

        $result = $resolver->resolve($collectionDto);

        $this->assertFalse($result);
    }

    public function testResolveGeneratesDocsWithData(): void
    {
        $filesystem = $this->createMock(Filesystem::class);
        $fileDto = new StructronFileDto(
            getcwd() . '/tests/E2E/Dto/ExampleDto.php',
            'ExampleDto',
            [
                new StructronRowDto(
                    StructronRowTypEnum::HEADER,
                    'ExampleDto',
                    null,
                    null,
                    null,
                ),
                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'ExampleDto-Bool',
                    'bool',
                    false,
                    'isExample',
                ),
                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'ExampleDto-Int',
                    'int',
                    0,
                    'exampleInt',
                ),
                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'ExampleDto-Float',
                    'float',
                    0.0,
                    'exampleFloat',
                ),
                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'ExampleDto-String',
                    'string',
                    'default',
                    'exampleString',
                ),
                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'ExampleDto-Array',
                    'string[]',
                    '[]',
                    'exampleArray',
                ),
                new StructronRowDto(
                    StructronRowTypEnum::SUBHEADER,
                    'ExampleDto-Object',
                    'Object',
                    null,
                    'exampleObject',
                ),
            ],
            [
                'Description of ExampleDto',
            ]
        );
        $collectionDto = new StructronCollectionDto([$fileDto]);
        $config = new StructronConfig();
        $config->setParameter(OptionEnum::TEST, true);
        $config->docPath('Docs');
        $config->phpExtension('php');
        $config->paths(['tests/E2E/Dto']);

        $filesystem->expects($this->atLeastOnce())->method('exists')->willReturn(false);
        $filesystem->expects($this->atLeastOnce())->method('mkdir');
        $filesystem->expects($this->atLeastOnce())->method('chown');
        $filesystem->expects($this->atLeastOnce())->method('chmod');

        $resolver = new StructronDocsResolver($config, $filesystem);

        $result = $resolver->resolve($collectionDto);

        $this->assertTrue($result);
        $this->assertFileExists('/tmp/Docs/ExampleDto.md');
        $this->assertFileExists('/tmp/Docs/_Structron.md');
    }
}
