<?php

use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\SalesRule\Model\Rule;
use Tan\SpcSalesRule\Api\Data\Rule\FreeGiftInterface;
use Tan\SpcSalesRule\Api\Data\Rule\FreeGiftInterfaceFactory;
use Tan\SpcSalesRule\Api\Rule\FreeGiftRepositoryInterface;
use Tan\SpcSalesRule\Model\Rule\FreeGift\Validator\AttributeSetIsPod;
use Tan\SpcSalesRule\Model\Rule\FreeGift\Validator\BoxSizeActionCondition;
use Tan\SpcSalesRule\Model\Rule\FreeGift\Validator\SalesRuleIsAmPromo;
use Tan\SpcSalesRule\Observer\ValidateFreeGiftSalesRule;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class ValidateFreeGiftSalesRuleTest
 */
class ValidateFreeGiftSalesRuleTest extends TestCase
{
    /** @var Observer|MockObject */
    private $observerMock;

    /** @var Event|MockObject */
    private $eventMock;

    /** @var Rule|MockObject */
    private $salesRuleMock;

    /** @var FreeGiftInterfaceFactory|MockObject */
    private $freeGiftInterfaceFactoryMock;

    /** @var FreeGiftRepositoryInterface|MockObject */
    private $freeGiftRepositoryInterfaceMock;

    /** @var MockObject|LoggerInterface */
    private $loggerInterfaceMock;

    /** @var FreeGiftInterface|MockObject */
    private $freeGiftInterfaceMock;

    /** @var AttributeSetIsPod|MockObject */
    private $attributeSetIsPodMock;

    /** @var SalesRuleIsAmPromo|MockObject */
    private $salesRuleIsAmPromoMock;

    /** @var BoxSizeActionCondition|MockObject */
    private $boxSizeActionConditionMock;

    /** @var ValidateFreeGiftSalesRule */
    private $observer;

    /** @var ValidateFreeGiftSalesRule|MockObject */
    private $validateFreeGiftSalesRuleMock;

    protected function setUp()
    {
        $this->observerMock = $this->createMock(Observer::class);
        $this->eventMock = $this->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRule'])
            ->getMock()
        ;
        $this->freeGiftInterfaceFactoryMock = $this->getMockBuilder(FreeGiftInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->freeGiftRepositoryInterfaceMock = $this->createMock(FreeGiftRepositoryInterface::class);
        $this->salesRuleMock = $this->getMockBuilder(Rule::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRowId', 'getDiscountAmount', 'getIsActive'])
            ->getMock();

        $this->freeGiftInterfaceMock = $this->createMock(FreeGiftInterface::class);
        $this->loggerInterfaceMock = $this->createMock(LoggerInterface::class);
        $this->validateFreeGiftSalesRuleMock = $this->createMock(ValidateFreeGiftSalesRule::class);
        $this->attributeSetIsPodMock = $this->createMock(AttributeSetIsPod::class);
        $this->boxSizeActionConditionMock = $this->createMock(BoxSizeActionCondition::class);
        $this->salesRuleIsAmPromoMock = $this->createMock(SalesRuleIsAmPromo::class);

        $this->observer = new ValidateFreeGiftSalesRule(
            $this->freeGiftInterfaceFactoryMock,
            $this->freeGiftRepositoryInterfaceMock,
            $this->attributeSetIsPodMock,
            $this->boxSizeActionConditionMock,
            $this->salesRuleIsAmPromoMock,
            $this->loggerInterfaceMock
        );
    }

    /**
     * @param $valid
     * @testWith    [true]
     *              [false]
     */
    public function testExecute($valid)
    {
        $rowIdMock = 12;
        $this->observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($this->eventMock);
        $this->eventMock->expects($this->once())
            ->method('getRule')
            ->willReturn($this->salesRuleMock);
        $this->attributeSetIsPodMock->expects($this->once())
            ->method('process')
            ->with($this->salesRuleMock)
            ->willReturn(true);
        $this->boxSizeActionConditionMock->expects($this->once())
            ->method('process')
            ->with($this->salesRuleMock)
            ->willReturn(true);
        $this->salesRuleIsAmPromoMock->expects($this->once())
            ->method('process')
            ->with($this->salesRuleMock)
            ->willReturn(true);
        $this->salesRuleMock->expects($this->once())
            ->method('getDiscountAmount')
            ->willReturn(true);
        $this->salesRuleMock->expects($this->once())
            ->method('getIsActive')
            ->willReturn($valid);
        $this->salesRuleMock->expects($this->once())
            ->method('getRowId')
            ->willReturn($rowIdMock);

        if ($valid) {
            $this->freeGiftInterfaceFactoryMock->expects($this->once())
                ->method('create')
                ->willReturn($this->freeGiftInterfaceMock);
            $this->freeGiftInterfaceMock->expects($this->once())
                ->method('setSalesRuleRowId')
                ->with($rowIdMock)
                ->willReturnSelf();
            $this->freeGiftRepositoryInterfaceMock->expects($this->once())
                ->method('save')
                ->with($this->freeGiftInterfaceMock)
                ->willReturnSelf();
        } else {
            $this->freeGiftRepositoryInterfaceMock->expects($this->once())
                ->method('deleteById')
                ->with($rowIdMock)
                ->willReturnSelf();
        }

        $this->assertEquals($this->observer, $this->observer->execute($this->observerMock));
    }
}
