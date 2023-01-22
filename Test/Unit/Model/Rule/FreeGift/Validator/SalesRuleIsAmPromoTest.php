<?php

namespace Tan\SpcSalesRule\Model\Rule\FreeGift\Validator;

use Magento\SalesRule\Model\Rule;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class SalesRuleIsAmPromoTest
 */
class SalesRuleIsAmPromoTest extends TestCase
{
    /** @var Rule|MockObject */
    private $salesRuleMock;

    /** @var SalesRuleIsAmPromo */
    private $validator;

    protected function setUp()
    {
        $this->salesRuleMock = $this->getMockBuilder(Rule::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSimpleAction'])
            ->getMock();
        $this->validator = new SalesRuleIsAmPromo();
    }

    public function testProcess()
    {
        $simpleActionMock = 'ampromo_simple_action_mock';

        $this->salesRuleMock->expects($this->exactly(2))
            ->method('getSimpleAction')
            ->willReturn($simpleActionMock);

        $this->assertEquals(false, $this->validator->process($this->salesRuleMock));
    }
}
