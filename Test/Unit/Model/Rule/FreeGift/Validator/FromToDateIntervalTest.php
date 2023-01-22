<?php

namespace Tan\SpcSalesRule\Model\Rule\FreeGift\Validator;

use DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\SalesRule\Model\Rule;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class FromToDateIntervalTest
 */
class FromToDateIntervalTest extends TestCase
{
    /** @var Rule|MockObject */
    private $salesRuleMock;

    /** @var TimezoneInterface|MockObject */
    private $timezoneMock;

    /** @var FromToDateInterval */
    private $validator;

    /** @var DateTime|MockObject */
    private $dateTimeMock;

    protected function setUp()
    {
        $this->salesRuleMock = $this->createMock(Rule::class);
        $this->timezoneMock = $this->createMock(TimezoneInterface::class);
        $this->dateTimeMock = $this->createMock(DateTime::class);

        $this->validator = new FromToDateInterval(
            $this->timezoneMock
        );
    }

    public function testProcess()
    {
        $this->timezoneMock->expects($this->once())
            ->method('date')
            ->willReturn($this->dateTimeMock);

        $this->salesRuleMock->expects($this->once())
            ->method('getFromDate')
            ->willReturn('FROM_DATE_MOCK');

        $this->salesRuleMock->expects($this->once())
            ->method('getToDate')
            ->willReturn('TO_DATE_MOCK');

        $this->assertEquals(false, $this->validator->process($this->salesRuleMock));
    }
}
