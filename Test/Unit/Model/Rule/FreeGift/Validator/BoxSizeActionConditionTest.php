<?php

namespace Tan\SpcSalesRule\Model\Rule\FreeGift\Validator;

use Exception;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\SalesRule\Model\Rule;
use Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item\Option;
use Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item\Option\Size;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class BoxSizeActionConditionTest
 */
class BoxSizeActionConditionTest extends TestCase
{
    /** @var Rule|MockObject */
    private $salesRuleMock;

    /** @var AbstractCondition|MockObject */
    private $salesRuleConditionMock;

    /** @var BoxSizeActionCondition */
    private $validator;

    protected function setUp()
    {
        $this->salesRuleMock = $this->createMock(Rule::class);
        $this->salesRuleConditionMock = $this->getMockBuilder(AbstractCondition::class)
            ->disableOriginalConstructor()
            ->setMethods(['getActions', 'getAttribute', 'getValue'])
            ->getMock();

        $this->validator = new BoxSizeActionCondition();
    }

    /**
     * @throws Exception
     */
    public function testProcess()
    {
        $conditionValueMock = '50';
        $resultMock = true;

        $this->salesRuleMock->expects($this->once())
            ->method('getActions')
            ->willReturn($this->salesRuleConditionMock);
        $this->salesRuleConditionMock->expects($this->exactly(2))
            ->method('getActions')
            ->willReturn([$this->salesRuleConditionMock]);
        $this->salesRuleConditionMock->expects($this->exactly(2))
            ->method('getAttribute')
            ->willReturnOnConsecutiveCalls(
                Option::QUOTE_ITEM_OPTION,
                Size::QUOTE_ITEM_OPTION_SPC_BOX_SIZE
            );
        $this->salesRuleConditionMock->expects($this->once())
            ->method('getValue')
            ->willReturn($conditionValueMock);

        $this->assertEquals($resultMock, $this->validator->process($this->salesRuleMock));
    }
}
