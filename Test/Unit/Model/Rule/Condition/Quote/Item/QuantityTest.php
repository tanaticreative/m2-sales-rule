<?php

namespace Tan\SpcSalesRule\Test\Unit\Model\Rule\Condition\Quote\Item;

use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\View\LayoutInterface;
use Magento\Rule\Block\Editable;
use Magento\Rule\Model\Condition\Context;
use Tan\Spc\Model\Category\Service;
use Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item\Quantity;
use PHPUnit\Framework\TestCase;

/**
 * Class QuantityTest
 */
class QuantityTest extends TestCase
{
    /** @var MockObject */
    private $layoutMock;

    /** @var MockObject */
    private $serviceMock;

    /** @var Quantity */
    private $quantity;

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

        $this->serviceMock = $this->createMock(Service::class);

        $this->quantity = new Quantity($contextMock, $this->serviceMock);
    }

    /**
     * @test
     */
    public function testLoadAttributeOptions(): void
    {
        $this->assertEquals($this->quantity, $this->quantity->loadAttributeOptions());
    }

    /**
     * @test
     */
    public function testGetAttributeElement(): void
    {
        $editableMock = $this->createMock(Editable::class);
        $this->layoutMock->expects($this->once())
            ->method('getBlockSingleton')
            ->with(Editable::class)
            ->willReturn($editableMock);

        $ruleMock = $this->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->setMethods(['getForm'])
            ->getMock();
        $formMock = $this->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->setMethods(['addField', 'setRenderer'])
            ->getMock();
        $this->quantity->setRule($ruleMock);

        $ruleMock->expects($this->once())
            ->method('getForm')
            ->willReturn($formMock);
        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->setMethods(['setShowAsText'])
            ->getMock();
        $formMock->expects($this->once())
            ->method('addField')
            ->willReturnSelf();
        $formMock->expects($this->once())
            ->method('setRenderer')
            ->with($this->createMock(Editable::class))
            ->willReturn($elementMock);
        $elementMock->expects($this->once())
            ->method('setShowAsText')
            ->with(true);

        $this->assertEquals($elementMock, $this->quantity->getAttributeElement());
    }

    /**
     * @test
     */
    public function testGetInputType(): void
    {
        $this->assertEquals('numeric', $this->quantity->getInputType());
    }

    /**
     * @test
     */
    public function testGetValueElementType(): void
    {
        $this->assertEquals('text', $this->quantity->getValueElementType());
    }

    /**
     * @test
     */
    public function testValidate(): void
    {
        $sizeMock = '2';
        $modelMock = $this->createMock(AbstractModel::class);
        $modelMock->expects($this->exactly(2))
            ->method('getData')
            ->withConsecutive(['product_type'], ['qty'])
            ->willReturn('box', $sizeMock);

        $this->quantity->setAttribute(Quantity::QUOTE_ITEM_SPC_BOX_QUANTITY);
        $this->quantity->setValueParsed('0');
        $this->quantity->setOperator('>');
        $this->assertEquals(true, $this->quantity->validate($modelMock));
    }
}
