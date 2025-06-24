# Wundii\Structron\Tests\E2E\Dto\Product
- [Back to Structron Documentation](./_Structron.md)
- [Go to Product.php](./../tests/E2E/Dto/Product.php)

A new product DTO

This DTO represents a new product with various attributes such as product ID, name, number, EAN, and tags.

## Class glossary
| FullObjectName | Object |
| -------------- | ------ |
| Wundii\Structron\Tests\E2E\Dto\Option | Option |
| Wundii\Structron\Tests\E2E\Dto\TestEnum | TestEnum |

## Properties
| Product                   | Type     | Default     | Description                                                   |
| ------------------------- | -------- | ----------- | ------------------------------------------------------------- |
| productId                 | int      | required    | The unique identifier for the product                         |
| productName               | string   | required    | The name of the product                                       |
| productNumber             | int      | null        | The product number, can be null                               |
| ean                       | string   | null        | The EAN (European Article Number) of the product, can be null |
| tags                      | string[] | []          | An array of tags associated with the product                  |
| **option**                | Option   | null        | An optional option associated with the product, can be null   |
| &nbsp; option.optionId    | int      | required    | The unique identifier for the option                          |
| &nbsp; option.optionName  | string   | required    | The name of the option                                        |
| **options**               | Option[] | []          | Additional options for the product, can be empty              |
| &nbsp; options.optionId   | int      | required    | The unique identifier for the option                          |
| &nbsp; options.optionName | string   | required    | The name of the option                                        |
| testEnum                  | TestEnum | TestEnum::A | An enum representing a test value                             |
