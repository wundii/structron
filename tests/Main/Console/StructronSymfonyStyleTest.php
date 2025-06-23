<?php

declare(strict_types=1);

namespace Wundii\Structron\Tests\Console;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Console\Output\StructronSymfonyStyle;

final class StructronSymfonyStyleTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testStartApplicationPrintsVersionAndCommand(): void
    {
        $config = new StructronConfig();
        $input = $this->createMock(InputInterface::class);
        $output = $this->getMockBuilder(OutputInterface::class)
            ->getMock();

        $output->expects(self::atLeastOnce())
            ->method('writeln');

        $style = new StructronSymfonyStyle($config, $input, $output);
        $style->startApplication('1.2.3');
    }

    /**
     * @throws Exception
     */
    public function testFinishApplicationSuccess(): void
    {
        $config = new StructronConfig();
        $input = $this->createMock(InputInterface::class);
        $output = $this->getMockBuilder(OutputInterface::class)
            ->getMock();

        $output->expects(self::exactly(2))
            ->method('writeln');

        $style = new StructronSymfonyStyle($config, $input, $output);

        $result = $style->finishApplication('2.5s');
        $this->assertFalse($result);
    }

    /**
     * @throws Exception
     */
    public function testFinishApplicationFailure(): void
    {
        $config = new StructronConfig();
        $input = $this->createMock(InputInterface::class);
        $output = $this->getMockBuilder(OutputInterface::class)
            ->getMock();

        $output->expects(self::atLeastOnce())->method('writeln');

        $style = new StructronSymfonyStyle($config, $input, $output);
        $style->setError();

        $result = $style->finishApplication('2.5s');
        $this->assertTrue($result);
    }

    /**
     * @throws Exception
     */
    public function testProgressBarMethodsRespectNoProgressBarOption(): void
    {
        $config = new StructronConfig();
        $config->disableProcessBar();

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $style = $this->getMockBuilder(StructronSymfonyStyle::class)
            ->setConstructorArgs([$config, $input, $output])
            ->onlyMethods(['progressStart', 'progressAdvance', 'progressFinish'])
            ->getMock();

        $style->expects($this->never())->method('progressStart');
        $style->progressBarStart(10);

        $style->expects($this->never())->method('progressAdvance');
        $style->progressBarAdvance();

        $style->expects($this->never())->method('progressFinish');
        $style->progressBarFinish();
    }

    /**
     * @throws Exception
     */
    public function testProgressBarMethodsStartWhenProgressbarEnabled(): void
    {
        $config = new StructronConfig();

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $style = $this->getMockBuilder(StructronSymfonyStyle::class)
            ->setConstructorArgs([$config, $input, $output])
            ->onlyMethods(['progressStart', 'progressAdvance', 'progressFinish'])
            ->getMock();

        $style->expects($this->once())->method('progressStart')->with(10);
        $style->progressBarStart(10);

        $style->expects($this->once())->method('progressAdvance');
        $style->progressBarAdvance();

        $style->expects($this->once())->method('progressFinish');
        $style->progressBarFinish();
    }
}
