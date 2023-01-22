<?php

namespace Tan\PromotionBundleProductCount\Test\Unit\Plugin\Model\SalesRule;

use Magento\Bundle\Model\Product\Type;
use Magento\Bundle\Model\ResourceModel\Selection\Collection;
use Magento\Bundle\Model\Selection;
use Magento\Catalog\Model\Product;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Utility as SalesRuleUtility;
use Tan\SpcSalesRule\Api\Data\Rule\FreeGiftInterface;
use Tan\SpcSalesRule\Api\Rule\FreeGiftRepositoryInterface;
use Tan\SpcSalesRule\Plugin\Model\SalesRule\Utility;

use PHPUnit\Framework\TestCase;

class UtilitiTest extends TestCase
{
    public function testAfterGetItemQty()
    {
        $subjectMock = $this->createMock(SalesRuleUtility::class);
        $resultMock = 25;
        $ruleMock = $this->createMock(Rule::class);
        $itemMock = $this->createMock(AbstractItem::class);
        $productMock = $this->createMock(Product::class);
        $freeGiftRepositoryMock = $this->createMock(FreeGiftRepositoryInterface::class);
        $freeGiftMock = $this->createMock(FreeGiftInterface::class);
        $bundleTypeInstanceMock = $this->createMock(Type::class);
        $selectionCollectionMock = $this->createMock(Collection::class);

        $expectedResult = 50;
        $freeGiftSaleRuleID = 175;

        $freeGiftRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($freeGiftSaleRuleID)
            ->willReturn($freeGiftMock);

        $plugin = new Utility($freeGiftRepositoryMock);

        $itemMock->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock);

        $itemMock->expects($this->once())
            ->method('getTotalQty')
            ->willReturn(25);

        $ruleMock->expects($this->at(0))
            ->method('getData')
            ->with('promotion_bundle_product_count')
            ->willReturn(1);

        $ruleMock->expects($this->at(1))
            ->method('getData')
            ->with('row_id')
            ->willReturn($freeGiftSaleRuleID);

        $ruleMock->expects($this->at(2))
            ->method('getData')
            ->with('discount_qty')
            ->willReturn(0);

        $productMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn(Type::TYPE_CODE);

        $productMock->expects($this->once())
            ->method('getTypeInstance')
            ->willReturn($bundleTypeInstanceMock);

        $bundleTypeInstanceMock->expects($this->once())
            ->method('getOptionsIds')
            ->with($productMock)
            ->willReturn(['12511', '12512']);

        $collection = new \ArrayObject();
        for ($i = 0; $i < 2; $i++) {
            $selectionMock = $this->createMock(Selection::class);
            $selectionMock->expects($this->once())
                ->method('getData')
                ->with('selection_qty')
                ->willReturn(1);

            $collection->append($selectionMock);
        }

        $bundleTypeInstanceMock->expects($this->once())
            ->method('getSelectionsCollection')
            ->with(['12511', '12512'], $productMock)
            ->willReturn($collection);

        $this->assertEquals(
           $expectedResult,
           $plugin->afterGetItemQty($subjectMock, $resultMock, $itemMock, $ruleMock)
       );
    }
}
