[![PHP-Tests](https://img.shields.io/github/actions/workflow/status/wundii/structron/code_quality.yml?branch=main&style=for-the-badge)](https://github.com/wundii/structron/actions/workflows/code_quality.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%2010-brightgreen.svg?style=for-the-badge)](https://phpstan.org/)
![VERSION](https://img.shields.io/packagist/v/wundii/structron?style=for-the-badge)
[![PHP](https://img.shields.io/packagist/php-v/wundii/structron?style=for-the-badge)](https://www.php.net/)
[![Rector](https://img.shields.io/badge/Rector-8.2-blue.svg?style=for-the-badge)](https://getrector.com)
[![ECS](https://img.shields.io/badge/ECS-check-blue.svg?style=for-the-badge)](https://tomasvotruba.com/blog/zen-config-in-ecs)
[![PHPUnit](https://img.shields.io/badge/PHP--Unit-check-blue.svg?style=for-the-badge)](https://phpunit.org)
[![codecov](https://img.shields.io/codecov/c/github/wundii/structron/main?token=2P3BCYK88L&style=for-the-badge)](https://codecov.io/github/wundii/structron)
[![Downloads](https://img.shields.io/packagist/dt/wundii/structron.svg?style=for-the-badge)](https://packagist.org/packages/wundii/structron)

A PHP library for generating human-readable documentation from structured data objects like DTOs, Entities, and Value Objects.
This is based on the data mapper.

## Installation
Require the bundle and its dependencies with composer:
> composer require wundii/afterbuy-sdk

Creating the config file
> vendor/bin/structron init

## Configuration

```php
use Wundii\Structron\Config\StructronConfig;

return static function (StructronConfig $structronConfig): void {
    $structronConfig->docPath('your/docs/folder');
    $structronConfig->paths(['your/dto/folder', 'your/other/dto/folder']);
    
    /**
     * Optional: The input in this example is the default value 
     */
    $structronConfig->phpExtension('php');
    $structronConfig->skip([]);
    $structronConfig->setIndentFileIteration();
    
    /**
     * Other possibilities for automated use
     */
    $structronConfig->disableProcessBar();
    $structronConfig->disableExitCode();
};
```

## Usage
> vendor/bin/structron