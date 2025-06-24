<?php

declare(strict_types=1);

namespace Main\Resolver;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Exception\DataMapperInvalidArgumentException;
use Wundii\Structron\Config\OptionEnum;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Dto\ReflectionDto;
use Wundii\Structron\Dto\StructronFileDto;
use Wundii\Structron\Dto\StructronRowDto;
use Wundii\Structron\Enum\StructronRowTypEnum;
use Wundii\Structron\Resolver\StructronFileResolver;

class StructronFileResolverTest extends TestCase
{
    public function testResolveFilesNamespaceNotFound(): void
    {
        $this->expectException(DataMapperInvalidArgumentException::class);

        $config = new StructronConfig();
        $config->setParameter(OptionEnum::TEST, true);
        $config->docPath('/tmp/Docs');
        $config->phpExtension('php');
        $config->paths(['tests/E2E/Dto']);
        $reflectionDto = new ReflectionDto(
            getcwd() . '/tests/E2E/Dto/Fail.php',
            'Wundii\Structron\Tests\E2E\Dto\Fail',
            'Test',
        );

        $resolver = new StructronFileResolver();
        $resolver->resolve($config, $reflectionDto);
    }

    public function testResolveFilesNamespaceFound(): void
    {
        $config = new StructronConfig();
        $config->setParameter(OptionEnum::TEST, true);
        $config->docPath('/temp/Docs');
        $config->phpExtension('php');
        $config->paths(['tests/E2E/Dto']);
        $reflectionDto = new ReflectionDto(
            getcwd() . '/tests/E2E/Dto/Test.php',
            'Wundii\Structron\Tests\E2E\Dto\Test',
            'Test',
        );

        $resolver = new StructronFileResolver();

        $result = $resolver->resolve($config, $reflectionDto);

        $expected = new StructronFileDto(
            ApproachEnum::CONSTRUCTOR,
            getcwd() . '/tests/E2E/Dto/Test.php',
            'Wundii\Structron\Tests\E2E\Dto\Test',
            [
                new StructronRowDto(
                    StructronRowTypEnum::HEADER,
                    'Test',
                    null,
                    null,
                    null,
                ),
                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'optionId',
                    'int',
                    'required',
                    'The unique identifier for the option',
                ),
                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'optionName',
                    'string',
                    'required',
                    'The name of the option',
                ),
            ],
            [
                'A product option DTO',
                'This DTO represents a product option with an identifier and a name.',
                'It is used to define various options that can be associated with a product.',
            ],
        );

        $this->assertEquals($expected, $result);
    }
}
