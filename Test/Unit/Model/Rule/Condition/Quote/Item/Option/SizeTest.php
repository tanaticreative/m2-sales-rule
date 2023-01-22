<?php

namespace Tan\SpcSalesRule\Test\Unit\Model\Rule\Condition\Quote\Item\Option;

use Magento\Catalog\Model\Category;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\View\LayoutInterface;
use Magento\Rule\Block\Editable;
use Magento\Rule\Model\Condition\Context;
use Tan\Spc\Model\Category\Service;
use Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item\Option\Size;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class SizeTest
 */
class SizeTest extends TestCase
{
    /** @var MockObject */
    private $layoutMock;

    /** @var MockObject */
    private $serviceMock;

    /** @var Size */
    private $size;

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

        $this->size = new Size($contextMock, $this->serviceMock);
    }

    /**
     * @test
     */
    public function testLoadAttributeOptions(): void
    {
        $this->assertEquals($this->size, $this->size->loadAttributeOptions());
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
        $this->size->setRule($ruleMock);

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

        $this->assertEquals($elementMock, $this->size->getAttributeElement());
    }

    /**
     * @test
     */
    public function testGetInputType(): void
    {
        $this->assertEquals('numeric', $this->size->getInputType());
    }

    /**
     * @test
     */
    public function testGetValueElementType(): void
    {
        $this->assertEquals('select', $this->size->getValueElementType());
    }

    /**
     * @test
     */
    public function testGetValueSelectOptions(): void
    {
        $categoryMock = $this->createMock(Category::class);
        $this->serviceMock->expects($this->once())
            ->method('getSpcCategory')
            ->willReturn($categoryMock);
        $categoryMock->expects($this->once())
            ->method('getData')
            ->with('box_size')
            ->willReturn('50,100');

        $this->assertEquals([50 => '50', 100 => '100'], $this->size->getValueSelectOptions());
    }

    /**
     * @test
     */
    public function testValidate(): void
    {
        $sizeMock = '50';
        $modelMock = $this->createMock(AbstractModel::class);
        $modelMock->expects($this->exactly(2))
            ->method('getData')
            ->withConsecutive(['product_type'], ['size'])
            ->willReturn('box', $sizeMock);

        $this->size->setAttribute(Size::QUOTE_ITEM_OPTION_SPC_BOX_SIZE);
        $this->size->setValueParsed($sizeMock);
        $this->size->setOperator('==');
        $this->assertEquals(true, $this->size->validate($modelMock));
    }
}
