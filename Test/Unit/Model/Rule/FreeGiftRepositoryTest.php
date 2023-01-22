<?php

namespace Tan\SpcSalesRule\Test\Unit\Model\Rule;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsFactory;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Tan\SpcSalesRule\Api\Data\Rule\FreeGiftInterface;
use Tan\SpcSalesRule\Model\ResourceModel\FreeGift;
use Tan\SpcSalesRule\Model\ResourceModel\Rule\FreeGift\Collection;
use Tan\SpcSalesRule\Model\ResourceModel\Rule\FreeGift\CollectionFactory;
use Tan\SpcSalesRule\Model\Rule\FreeGift as FreeGiftRuleModel;
use Tan\SpcSalesRule\Model\Rule\FreeGiftFactory;
use Tan\SpcSalesRule\Model\Rule\FreeGiftRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class FreeGiftRepositoryTest
 */
class FreeGiftRepositoryTest extends TestCase
{
    /** @var FreeGiftInterface|MockObject */
    private $freeGiftRuleMock;

    /** @var FreeGift|MockObject */
    private $freeGiftRuleResourceMock;

    /** @var CollectionProcessorInterface|MockObject */
    private $collectionProcessorInterfaceMock;

    /** @var CollectionFactory|MockObject */
    private $freeGiftRuleCollectionFactoryMock;

    /** @var FreeGiftFactory|MockObject */
    private $freeGiftRuleFactoryMock;

    /** @var SearchResultsFactory|MockObject */
    private $searchResultsFactoryMock;

    /** @var FreeGiftRepository */
    private $freeGiftRuleRepository;

    /** @var Collection|MockObject */
    private $freeGiftRuleResourceCollectionMock;

    /** @var SearchResultsInterface|MockObject */
    private $searchResultMock;

    /** @var SearchCriteriaInterface|MockObject */
    private $searchCriteriaMock;

    protected function setUp()
    {
        $this->freeGiftRuleMock = $this->createMock(FreeGiftRuleModel::class);
        $this->freeGiftRuleResourceMock = $this->createMock(FreeGift::class);
        $this->collectionProcessorInterfaceMock = $this->createMock(CollectionProcessorInterface::class);
        $this->searchResultMock = $this->createMock(SearchResultsInterface::class);
        $this->searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        $this->freeGiftRuleResourceCollectionMock = $this->createMock(Collection::class);
        $this->freeGiftRuleCollectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->freeGiftRuleFactoryMock = $this->getMockBuilder(FreeGiftFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(SearchResultsFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->freeGiftRuleRepository = new FreeGiftRepository(
            $this->freeGiftRuleCollectionFactoryMock,
            $this->collectionProcessorInterfaceMock,
            $this->searchResultsFactoryMock,
            $this->freeGiftRuleResourceMock,
            $this->freeGiftRuleFactoryMock
        );
    }

    /**
     * @param bool $assert
     * @throws NoSuchEntityException
     */
    public function testGetById($assert = true)
    {
        $freeGiftSalesRuleRowId = 12;

        $this->freeGiftRuleFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->freeGiftRuleMock);

        $this->freeGiftRuleResourceMock->expects($this->once())
            ->method('load')
            ->with($this->freeGiftRuleMock, $freeGiftSalesRuleRowId)
            ->willReturn($this->freeGiftRuleMock);

        $this->freeGiftRuleMock->expects($this->once())
            ->method('getId')
            ->willReturn($freeGiftSalesRuleRowId);

        if ($assert) {
            $this->assertEquals(
                $this->freeGiftRuleMock,
                $this->freeGiftRuleRepository->getById($freeGiftSalesRuleRowId)
            );
        }
    }

    public function testGetList()
    {
        $sizeMock = 100;

        $this->freeGiftRuleCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->freeGiftRuleResourceCollectionMock);
        $this->collectionProcessorInterfaceMock->expects($this->once())
            ->method('process')
            ->with($this->searchCriteriaMock, $this->freeGiftRuleResourceCollectionMock);
        $this->freeGiftRuleResourceCollectionMock->expects($this->once())
            ->method('load')
            ->willReturnSelf();

        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->searchResultMock);
        $this->searchResultMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($this->searchCriteriaMock);
        $this->freeGiftRuleResourceCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$this->freeGiftRuleMock]);
        $this->searchResultMock->expects($this->once())
            ->method('setItems')
            ->with([$this->freeGiftRuleMock]);
        $this->freeGiftRuleResourceCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn($sizeMock);
        $this->searchResultMock->expects($this->once())
            ->method('setTotalCount')
            ->with($sizeMock);

        $this->assertEquals(
            $this->searchResultMock,
            $this->freeGiftRuleRepository->getList($this->searchCriteriaMock)
        );
    }

    /**
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     */
    public function testSave()
    {
        $this->freeGiftRuleResourceMock->expects($this->once())
            ->method('save')
            ->with($this->freeGiftRuleMock);

        $this->assertEquals(
            $this->freeGiftRuleRepository,
            $this->freeGiftRuleRepository->save(($this->freeGiftRuleMock))
        );
    }

    /**
     * @throws CouldNotDeleteException
     */
    public function testDelete()
    {
        $this->freeGiftRuleResourceMock->expects($this->once())
            ->method('delete')
            ->with($this->freeGiftRuleMock);

        $this->assertEquals(
            $this->freeGiftRuleRepository,
            $this->freeGiftRuleRepository->delete(($this->freeGiftRuleMock))
        );
    }

    /**
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function testDeleteById()
    {
        $this->testGetById(false);

        $freeGiftRuleMockId = 12;
        $this->freeGiftRuleResourceMock->expects($this->once())
            ->method('delete')
            ->with($this->freeGiftRuleMock);

        $this->assertEquals(
            $this->freeGiftRuleRepository,
            $this->freeGiftRuleRepository->deleteById($freeGiftRuleMockId)
        );
    }
}
