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

    public function testResolveFilesNamespaceFoundSimple(): void
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

    public function testResolveFilesNamespaceFoundComplex(): void
    {
        $config = new StructronConfig();
        $config->setParameter(OptionEnum::TEST, true);
        $config->docPath('/temp/Docs');
        $config->phpExtension('php');
        $config->paths(['tests/E2E/Dto']);
        $reflectionDto = new ReflectionDto(
            getcwd() . '/tests/E2E/Dto/Product.php',
            'Wundii\Structron\Tests\E2E\Dto\Product',
            'Product',
        );

        $resolver = new StructronFileResolver();

        $result = $resolver->resolve($config, $reflectionDto);

        $expected = new StructronFileDto(
            ApproachEnum::CONSTRUCTOR,
            getcwd() . '/tests/E2E/Dto/Product.php',
            'Wundii\Structron\Tests\E2E\Dto\Product',
            [
                new StructronRowDto(
                    StructronRowTypEnum::HEADER,
                    'Product',
                    null,
                    null,
                    null,
                ),
                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'productId',
                    'int',
                    'required',
                    'The unique identifier for the product',
                ),
                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'productName',
                    'string',
                    'required',
                    'The name of the product',
                ),
                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'productNumber',
                    'int',
                    null,
                    'The product number, can be null',
                ),
                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'ean',
                    'string',
                    null,
                    'The EAN (European Article Number) of the product, can be null',
                ),
                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'tags',
                    'string[]',
                    '[]',
                    'An array of tags associated with the product',
                ),
                new StructronRowDto(
                    StructronRowTypEnum::SUBHEADER,
                    'option',
                    'Wundii\Structron\Tests\E2E\Dto\Option',
                    null,
                    'An optional option associated with the product, can be null',
                ),
                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'option.optionId',
                    'int',
                    'required',
                    'The unique identifier for the option',
                ),
                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'option.optionName',
                    'string',
                    'required',
                    'The name of the option',
                ),
                new StructronRowDto(
                    StructronRowTypEnum::SUBHEADER,
                    'options',
                    'Wundii\Structron\Tests\E2E\Dto\Option[]',
                    '[]',
                    'Additional options for the product, can be empty',
                ),

                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'options.optionId',
                    'int',
                    'required',
                    'The unique identifier for the option',
                ),
                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'options.optionName',
                    'string',
                    'required',
                    'The name of the option',
                ),
                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'testEnum',
                    'Wundii\Structron\Tests\E2E\Dto\TestEnum',
                    'TestEnum::A',
                    'An enum representing a test value',
                ),
            ],
            [
                'A new product DTO',
                'This DTO represents a new product with various attributes such as product ID, name, number, EAN, and tags.',
            ],
        );

        $this->assertEquals($expected, $result);
    }
}
