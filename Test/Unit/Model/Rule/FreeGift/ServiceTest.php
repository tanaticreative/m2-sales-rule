<?php

namespace Tan\SpcSalesRule\Model\Rule\FreeGift;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Model\Converter\ToModel;
use Magento\SalesRule\Model\Data\Rule as DataModelRule;
use Magento\SalesRule\Model\Rule as ModelRule;
use Tan\Spc\Model\Config;
use Tan\SpcSalesRule\Api\Rule\FreeGiftRepositoryInterface;
use Tan\SpcSalesRule\Model\Rule\FreeGift;
use Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item\Option;
use Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item\Option\Size;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class ServiceTest
 */
class ServiceTest extends TestCase
{
    /** @var FreeGiftRepositoryInterface|MockObject */
    private $freeGiftRuleRepositoryMock;

    /** @var RuleRepositoryInterface|MockObject */
    private $ruleInterfaceRepositoryMock;

    /** @var SearchCriteriaBuilder|MockObject */
    private $searchCriteriaBuilderMock;

    /** @var ModelRule|MockObject */
    private $salesRuleModelMock;

    /** @var Service */
    private $serviceMock;

    /** @var RuleInterface|MockObject */
    private $salesRuleInterfaceMock;

    /** @var SearchCriteria|MockObject */
    private $searchCriteriaMock;

    /** @var SearchResultsInterface|MockObject */
    private $searchResultMock;

    /** @var FreeGift|MockObject */
    private $freeGiftRuleMock;

    /** @var DataModelRule|MockObject */
    private $salesRuleDataModelMock;

    /** @var Config|MockObject */
    private $spcConfigMock;

    /** @var ToModel|MockObject */
    private $toModelConvertorMock;

    /** @var array */
    private $freeGiftRuleValidatorsMock;

    /** @var int */
    private $boxSizeMock;

    /** @var AbstractCondition|MockObject */
    private $salesRuleConditionMock;

    protected function setUp()
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $this->freeGiftRuleRepositoryMock = $this->createMock(FreeGiftRepositoryInterface::class);
        $this->freeGiftRuleMock = $this->createMock(FreeGift::class);
        $this->ruleInterfaceRepositoryMock = $this->createMock(RuleRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->spcConfigMock = $this->createMock(Config::class);
        $this->toModelConvertorMock = $this->createMock(ToModel::class);
        $this->freeGiftRuleValidatorsMock = [];
        $this->boxSizeMock = 50;

        $this->searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $this->searchResultMock = $this->createMock(SearchResultsInterface::class);
        $this->salesRuleInterfaceMock = $this->createMock(RuleInterface::class);
        $this->salesRuleDataModelMock = $this->createMock(DataModelRule::class);
        $this->salesRuleConditionMock = $this->getMockBuilder(AbstractCondition::class)
            ->disableOriginalConstructor()
            ->setMethods(['getActions', 'getAttribute', 'getValue'])
            ->getMock();

        $this->salesRuleModelMock = $this->getMockBuilder(ModelRule::class)
            ->disableOriginalConstructor()
            ->setMethods(['getExtensionAttributes', 'getActions', 'getDiscountAmount'])
            ->getMock();

        $this->serviceMock = new Service(
            $this->freeGiftRuleRepositoryMock,
            $this->ruleInterfaceRepositoryMock,
            $this->searchCriteriaBuilderMock,
            $this->spcConfigMock,
            $this->toModelConvertorMock,
            $loggerMock,
            $this->freeGiftRuleValidatorsMock
        );
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function testGetProductList()
    {
        $skuListMock = ['ampromo_rule' => ['sku' => 'sku1_mock, sku2_mock']];
        $result = ['sku1_mock', 'sku2_mock'];
        $freeGiftRule = $this->testGetFreeGiftSalesRule(false);

        $freeGiftRule->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($skuListMock);

        $this->assertEquals($result, $this->serviceMock->getProductList($this->boxSizeMock, []));
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function testGetNumberOfGiftItems()
    {
        $discountMock = 2;
        $freeGiftRule = $this->testGetFreeGiftSalesRule(false);

        $freeGiftRule->expects($this->once())
            ->method('getDiscountAmount')
            ->willReturn($discountMock);

        $this->assertEquals($discountMock, $this->serviceMock->getNumberOfGiftItems($this->boxSizeMock));
    }

    /**
     * @param $shouldAssert
     *
     * @testWith    [true]
     *
     * @return FreeGift|MockObject
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function testGetFreeGiftSalesRule($shouldAssert)
    {
        $this->spcConfigMock->expects($this->once())
            ->method('isSpcEnable')
            ->willReturn(true);

        $freeGiftRules = $this->getEligibleFreeGiftSalesRulesSetUp();

        $this->toModelConvertorMock->expects($this->once())
            ->method('toModel')
            ->with($freeGiftRules[0])
            ->willReturn($this->salesRuleModelMock);

        $this->salesRuleModelMock->expects($this->once())
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
            ->willReturn($this->boxSizeMock);

        if ($shouldAssert) {
            $this->assertEquals($this->salesRuleModelMock, $this->serviceMock->getFreeGiftSalesRule($this->boxSizeMock));
        } else {
            return $this->salesRuleModelMock;
        }
    }

    /**
     * @return FreeGift[]|MockObject[]
     */
    private function getEligibleFreeGiftSalesRulesSetUp()
    {
        $salesRuleDataModelMock = [$this->salesRuleDataModelMock];
        $freeGiftRulesMock = [$this->freeGiftRuleMock];

        $this->searchCriteriaBuilderMock->expects($this->exactly(2))
            ->method('create')
            ->willReturn($this->searchCriteriaMock);
        $this->freeGiftRuleRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteriaMock)
            ->willReturn($this->searchResultMock);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with('row_id', [0], 'in')
            ->willReturnSelf();
        $this->ruleInterfaceRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteriaMock)
            ->willReturn($this->searchResultMock);

        $this->searchResultMock->expects($this->exactly(2))
            ->method('getItems')
            ->willReturnOnConsecutiveCalls(
                $freeGiftRulesMock,
                $salesRuleDataModelMock
            );

        return $salesRuleDataModelMock;
    }
}
