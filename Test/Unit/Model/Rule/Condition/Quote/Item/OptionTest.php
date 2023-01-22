<?php

namespace Tan\SpcSalesRule\Test\Unit\Model\Rule\Condition\Quote\Item;

use Magento\Framework\View\LayoutInterface;
use Magento\Quote\Model\Quote;
use Magento\Rule\Model\Condition\Context;
use Magento\Framework\Event\ManagerInterface;
use Magento\SalesRule\Model\Rule\Condition\Address;
use Magento\Framework\Model\AbstractModel;
use Tan\Spc\Model\Box\Service\ProductGrouping;
use Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item\Option;
use Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item\Option\Size;
use Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item\Quantity;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class OptionTest
 */
class OptionTest extends TestCase
{
    /** @var string */
    const QUOTE_ITEM_OPTION = 'quote_item_option';

    /** @var MockObject */
    private $layoutMock;

    /** @var MockObject */
    private $productGroupingMock;

    /** @var Option */
    private $option;

    /**
     * Mock setup for general use
     */
    protected function setUp()
    {
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLayout'])
            ->getMock();
        $this->layoutMock = $this->createMock(LayoutInterface::class);
        $contextMock->expects($this->once())
            ->method('getLayout')
            ->willReturn($this->layoutMock);
        $managerMock = $this->createMock(ManagerInterface::class);
        $conditionAddressMock = $this->createMock(Address::class);

        $this->productGroupingMock = $this->createMock(ProductGrouping::class);

        $this->option = new Option($contextMock, $managerMock, $conditionAddressMock, $this->productGroupingMock);
    }

    /**
     * @test
     */
    public function testLoadArray(): void
    {
        $this->assertEquals(
            $this->option,
            $this->option->loadArray(['attribute' => 'attribute mock', 'operator' => 'operator mock'])
        );
    }

    /**
     * @test
     */
    public function testAsXml(): void
    {
        $this->assertEquals(
            '<attribute></attribute><operator></operator><aggregator>all</aggregator><value></value><conditions></conditions>',
            $this->option->asXml()
        );
    }

    /**
     * @test
     */
    public function testLoadAttributeOptions(): void
    {
        $this->assertEquals($this->option, $this->option->loadAttributeOptions());
    }

    /**
     * @test
     */
    public function testLoadValueOptions(): void
    {
        $this->assertEquals($this->option, $this->option->loadValueOptions());
    }

    /**
     * @test
     */
    public function testLoadOperatorOptions(): void
    {
        $this->assertEquals($this->option, $this->option->loadOperatorOptions());
    }

    /**
     * @test
     */
    public function testGetValueElementType(): void
    {
        $this->assertEquals('select', $this->option->getValueElementType());
    }

    /**
     * @test
     */
    public function testGetValueSelectOptions(): void
    {
        $this->assertEquals(['spc_box_uid' => 'spc_box_uid'], $this->option->getValueSelectOptions());
    }

    /**
     * @test
     */
    public function testGetNewChildSelectOptions(): void
    {
        $this->assertEquals([
            [
                'value' => Quantity::class,
                'label' => __('Box qty'),
            ],
            [
                'value' => Size::class,
                'label' => __('Box size'),
            ]
        ], $this->option->getNewChildSelectOptions());
    }

    /**
     * @test
     */
    public function testValidate(): void
    {
        $modelMock = $this->getMockBuilder(AbstractModel::class)
            ->disableOriginalConstructor()
            ->setMethods(['getQuote'])
            ->getMock();
        $quoteMock = $this->createMock(Quote::class);
        $modelMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);
        $itemMockA = $this->createMock(AbstractModel::class);
        $quoteMock->expects($this->once())
            ->method('getAllVisibleItems')
            ->willReturn([$itemMockA]);
        $itemMockA->expects($this->once())
            ->method('getData')
            ->with('product_type')
            ->willReturn('box');
        $this->productGroupingMock->expects($this->once())
            ->method('groupBoxContentItems')
            ->with([$itemMockA])
            ->willReturn([$itemMockA]);

        $itemOptionMock = 'spc_box_uid';
        $conditionMock = $this->getMockBuilder(AbstractModel::class)
            ->disableOriginalConstructor()
            ->setMethods(['validate'])
            ->getMock();
        $conditionMock->expects($this->once())
            ->method('validate')
            ->with($itemMockA)
            ->willReturn(true);
        $this->option->setConditions([$conditionMock]);
        $this->option->setValue(true);
        $this->option->setValueParsed($itemOptionMock);
        $this->option->setOperator('==');
        $this->assertEquals(true, $this->option->validate($modelMock));
    }
}
