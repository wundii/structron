<?php

declare(strict_types=1);

namespace Init\Bootstrap;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Wundii\Structron\Bootstrap\BootstrapInputResolver;
use Wundii\Structron\Console\OptionEnum;

class BootstrapInputResolverTest extends TestCase
{
    public function testHasOptionModeNone()
    {
        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/structron', '--fail']));
        $this->assertFalse($inputResolver->hasOption(OptionEnum::HELP));

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/structron', '-f']));
        $this->assertFalse($inputResolver->hasOption(OptionEnum::HELP));

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/structron', '--help']));
        $this->assertTrue($inputResolver->hasOption(OptionEnum::HELP));

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/structron', '-h']));
        $this->assertTrue($inputResolver->hasOption(OptionEnum::HELP));
    }

    public function testHasOptionModeRequire()
    {
        $configFile = __DIR__ . '/Files/structron-01.php';

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/structron', '--fail', $configFile]));
        $this->assertFalse($inputResolver->hasOption(OptionEnum::CONFIG));

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/structron', '-f', $configFile]));
        $this->assertFalse($inputResolver->hasOption(OptionEnum::CONFIG));

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/structron', '--config', $configFile]));
        $this->assertTrue($inputResolver->hasOption(OptionEnum::CONFIG));

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/structron', '-c', $configFile]));
        $this->assertTrue($inputResolver->hasOption(OptionEnum::CONFIG));
    }

    public function testGetOptionValueModeRequire()
    {
        $configFile = __DIR__ . '/Files/structron-01.php';

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/structron', '--fail', $configFile]));
        $this->assertNull($inputResolver->getOptionValue(OptionEnum::CONFIG));

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/structron', '-f', $configFile]));
        $this->assertNull($inputResolver->getOptionValue(OptionEnum::CONFIG));

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/structron', '--config', $configFile]));
        $this->assertEquals($configFile, $inputResolver->getOptionValue(OptionEnum::CONFIG));

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/structron', '-c', $configFile]));
        $this->assertEquals($configFile, $inputResolver->getOptionValue(OptionEnum::CONFIG));
    }
}
