<?php

namespace Tan\SpcSalesRule\Model\Rule\FreeGift\Validator;

use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Eav\Api\Data\AttributeSetInterface;
use Magento\Eav\Api\Data\AttributeSetSearchResultsInterface;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\SalesRule\Model\Rule;
use Tan\ProductAttributes\Model\Product\AttributeSet;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class AttributeSetIsPodTest
 */
class AttributeSetIsPodTest extends TestCase
{
    /** @var MockObject */
    private $salesRuleMock;

    /** @var AbstractCondition|MockObject */
    private $salesRuleConditionMock;

    /** @var AttributeSetSearchResultsInterface|MockObject */
    private $attributeSetSearchResultsInterfaceMock;

    /** @var AttributeSetRepositoryInterface|MockObject */
    private $attributeSetRepositoryInterfaceMock;

    /** @var SearchCriteriaBuilder|MockObject */
    private $searchCriteriaBuilderMock;

    /** @var SearchCriteria|MockObject */
    private $searchCriteriaMock;

    /** @var AttributeSetInterface|MockObject */
    private $attributeSetInterfaceMock;

    /** @var AttributeSetIsPod */
    private $validator;

    protected function setUp()
    {
        $this->salesRuleMock = $this->createMock(Rule::class);
        $this->salesRuleConditionMock = $this->getMockBuilder(AbstractCondition::class)
            ->disableOriginalConstructor()
            ->setMethods(['getActions', 'getAttribute', 'getValue'])
            ->getMock();

        $this->attributeSetSearchResultsInterfaceMock = $this->createMock(AttributeSetSearchResultsInterface::class);
        $this->attributeSetRepositoryInterfaceMock = $this->createMock(AttributeSetRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $this->attributeSetInterfaceMock = $this->createMock(AttributeSetInterface::class);

        $this->validator = new AttributeSetIsPod(
            $this->attributeSetRepositoryInterfaceMock,
            $this->searchCriteriaBuilderMock
        );
    }

    public function testProcess()
    {
        $attributeSetIdMock = 10;

        $this->salesRuleMock->expects($this->once())
            ->method('getActions')
            ->willReturn($this->salesRuleConditionMock);
        $this->salesRuleConditionMock->expects($this->once())
            ->method('getActions')
            ->willReturn([$this->salesRuleConditionMock]);
        $this->salesRuleConditionMock->expects($this->once())
            ->method('getAttribute')
            ->willReturn(Set::KEY_ATTRIBUTE_SET_ID);
        $this->salesRuleConditionMock->expects($this->once())
            ->method('getValue')
            ->willReturn($attributeSetIdMock);

        $attributeSet = $this->getAttributeSetByNameSetUp();
        $attributeSet->expects($this->once())
            ->method('getAttributeSetId')
            ->willReturn($attributeSetIdMock);

        $this->assertEquals(true, $this->validator->process($this->salesRuleMock));
    }

    private function getAttributeSetByNameSetUp()
    {
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with('attribute_set_name', AttributeSet::POD_ATTRIBUTE_SET, 'eq')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('setPageSize')
            ->with(1)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($this->searchCriteriaMock);

        $this->attributeSetRepositoryInterfaceMock->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteriaMock)
            ->willReturn($this->attributeSetSearchResultsInterfaceMock);

        $this->attributeSetSearchResultsInterfaceMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$this->attributeSetInterfaceMock]);

        return $this->attributeSetInterfaceMock;
    }
}
