<?php

namespace Tan\SpcSalesRule\Model\Rule;

use Exception;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchResultsFactory;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Tan\SpcSalesRule\Api\Data\Rule\FreeGiftInterface;
use Tan\SpcSalesRule\Api\Rule\FreeGiftRepositoryInterface;
use Tan\SpcSalesRule\Model\ResourceModel\FreeGift as FreeGiftRuleResource;
use Tan\SpcSalesRule\Model\ResourceModel\Rule\FreeGift\CollectionFactory;

/**
 * Class FreeGiftRepository
 */
class FreeGiftRepository implements FreeGiftRepositoryInterface
{
    /** @var CollectionFactory */
    private $freeGiftRuleCollectionFactory;

    /** @var CollectionProcessorInterface */
    private $collectionProcessor;

    /** @var SearchResultsFactory */
    private $searchResultsFactory;

    /** @var FreeGiftRuleResource */
    private $freeGiftRuleResource;

    /** @var FreeGiftFactory */
    private $freeGiftRuleFactory;

    /**
     * FreeGiftRepository constructor.
     * @param CollectionFactory $freeGiftRuleCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchResultsFactory $searchResultsFactory
     * @param FreeGiftRuleResource $freeGiftRuleResource
     * @param FreeGiftFactory $freeGiftRuleFactory
     */
    public function __construct(
        CollectionFactory $freeGiftRuleCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchResultsFactory $searchResultsFactory,
        FreeGiftRuleResource $freeGiftRuleResource,
        FreeGiftFactory $freeGiftRuleFactory
    ) {
        $this->freeGiftRuleCollectionFactory = $freeGiftRuleCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->freeGiftRuleResource = $freeGiftRuleResource;
        $this->freeGiftRuleFactory = $freeGiftRuleFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getById(int $freeGiftSalesRuleRowId): FreeGiftInterface
    {
        $freeGiftRule = $this->freeGiftRuleFactory->create();
        $this->freeGiftRuleResource->load($freeGiftRule, $freeGiftSalesRuleRowId);

        if (!$freeGiftRule->getId()) {
            throw new NoSuchEntityException(__('The rule with id %1 does not exist in the free gift rules list.', $freeGiftSalesRuleRowId));
        }

        return $freeGiftRule;
    }

    /**
     * {@inheritDoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        $collection = $this->freeGiftRuleCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $collection->load();

        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * {@inheritDoc}
     */
    public function save(FreeGiftInterface $freeGiftRule): FreeGiftRepositoryInterface
    {
        try {
            $this->freeGiftRuleResource->save($freeGiftRule);
        } catch (AlreadyExistsException $e) {
            throw new AlreadyExistsException(__('The rule with id %1 was already added to the free gift rules list.', $freeGiftRule->getSalesRuleRowId()));
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Could not save rule with id %1 to the free gift rules list.', $freeGiftRule->getSalesRuleRowId()));
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(FreeGiftInterface $freeGiftRule): FreeGiftRepositoryInterface
    {
        try {
            $this->freeGiftRuleResource->delete($freeGiftRule);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete rule with id %1 from the free gift rules list.', $freeGiftRule->getSalesRuleRowId()));
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteById(int $freeGiftSalesRuleRowId): FreeGiftRepositoryInterface
    {
        $freeGiftRule = $this->getById($freeGiftSalesRuleRowId);

        try {
            $this->freeGiftRuleResource->delete($freeGiftRule);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete rule with id %1 from the free gift rules list.', $freeGiftSalesRuleRowId));
        }

        return $this;
    }
}
