<?php

declare(strict_types=1);

namespace Init\Config;

use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;
use Wundii\Structron\Config\OptionEnum;
use Wundii\Structron\Config\StructronConfigParameter;

class StructronConfigParameterTest extends TestCase
{
    public function testHasSetAndGetAParameter()
    {
        $structronConfigParameter = new StructronConfigParameter();

        $this->assertFalse($structronConfigParameter->has(OptionEnum::TEST));
        $this->assertNull($structronConfigParameter->getParameter(OptionEnum::TEST));
        $this->assertSame('', $structronConfigParameter->getParameter(OptionEnum::TEST, ''));
        $this->assertSame([], $structronConfigParameter->getParameter(OptionEnum::TEST, []));

        $structronConfigParameter->setParameter(OptionEnum::TEST, 'php');
        $this->assertTrue($structronConfigParameter->has(OptionEnum::TEST));
        $this->assertEquals('php', $structronConfigParameter->getParameter(OptionEnum::TEST));
    }

    public function testGetBoolean()
    {
        $structronConfigParameter = new StructronConfigParameter();
        $structronConfigParameter->setParameter(OptionEnum::TEST, true);
        $this->assertTrue($structronConfigParameter->getBoolean(OptionEnum::TEST));

        $structronConfigParameter->setParameter(OptionEnum::TEST, false);
        $this->assertFalse($structronConfigParameter->getBoolean(OptionEnum::TEST));

        $this->expectException(InvalidArgumentException::class);
        $structronConfigParameter->setParameter(OptionEnum::TEST, 'fail');
        $structronConfigParameter->getBoolean(OptionEnum::TEST);
    }

    public function testGetInteger()
    {
        $structronConfigParameter = new StructronConfigParameter();
        $structronConfigParameter->setParameter(OptionEnum::TEST, 1234);
        $this->assertEquals(1234, $structronConfigParameter->getInteger(OptionEnum::TEST));

        $this->expectException(InvalidArgumentException::class);
        $structronConfigParameter->setParameter(OptionEnum::TEST, 'fail');
        $structronConfigParameter->getInteger(OptionEnum::TEST);
    }

    public function testGetString()
    {
        $structronConfigParameter = new StructronConfigParameter();
        $structronConfigParameter->setParameter(OptionEnum::TEST, 'abcd');

        $this->assertEquals('abcd', $structronConfigParameter->getString(OptionEnum::TEST));

        $this->expectException(InvalidArgumentException::class);
        $structronConfigParameter->setParameter(OptionEnum::TEST, 1234);
        $structronConfigParameter->getString(OptionEnum::TEST);
    }

    public function testGetArrayWithStrings()
    {
        $structronConfigParameter = new StructronConfigParameter();
        $structronConfigParameter->setParameter(OptionEnum::TEST, ['abcd', 'efgh']);
        $this->assertEquals(['abcd', 'efgh'], $structronConfigParameter->getArrayWithStrings(OptionEnum::TEST));

        $this->expectException(InvalidArgumentException::class);
        $structronConfigParameter->setParameter(OptionEnum::TEST, 'fail');
        $structronConfigParameter->getArrayWithStrings(OptionEnum::TEST);
    }

    public function testGetArrayWithStringsWithWrongValue()
    {
        $structronConfigParameter = new StructronConfigParameter();
        $structronConfigParameter->setParameter(OptionEnum::TEST, ['abcd', 1234]);

        $this->expectException(InvalidArgumentException::class);
        $structronConfigParameter->getArrayWithStrings(OptionEnum::TEST);
    }
}
