<?php

namespace Tan\SpcSalesRule\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Tan\SpcSalesRule\Api\Data\RuleDataInterface;
use Tan\SpcSalesRule\Api\Data\RuleDataInterfaceFactory;
use Tan\SpcSalesRule\Api\RuleDataRepositoryInterface;
use Tan\SpcSalesRule\Model\ResourceModel\RuleData as RuleDataResource;
use Tan\SpcSalesRule\Model\ResourceModel\RuleData\CollectionFactory as RuleDataCollectionFactory;

class RuleDataRepository implements RuleDataRepositoryInterface
{
    /** @var RuleDataResource */
    protected $resource;

    /** @var RuleDataFactory */
    protected $ruleDataFactory;

    /** @var RuleDataCollectionFactory */
    protected $ruleDataCollectionFactory;

    /** @var DataObjectHelper */
    protected $dataObjectHelper;

    /** @var DataObjectProcessor */
    protected $dataObjectProcessor;

    /** @var RuleDataInterfaceFactory */
    protected $dataRuleFactory;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var CollectionProcessorInterface */
    private $collectionProcessor;

    /**
     * @param RuleDataResource $resource
     * @param RuleDataFactory $ruleDataFactory
     * @param RuleDataInterfaceFactory $dataRuleFactory
     * @param RuleDataCollectionFactory $ruleDataCollectionFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        RuleDataResource $resource,
        RuleDataFactory $ruleDataFactory,
        RuleDataInterfaceFactory $dataRuleFactory,
        RuleDataCollectionFactory $ruleDataCollectionFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->ruleDataFactory = $ruleDataFactory;
        $this->ruleDataCollectionFactory = $ruleDataCollectionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataRuleFactory = $dataRuleFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(RuleDataInterface $ruleData) : RuleDataInterface
    {
        try {
            $this->resource->save($ruleData);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the rule: %1',
                $exception->getMessage()
            ));
        }
        return $ruleData;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($entityId)
    {
        $ruleData = $this->ruleDataFactory->create();
        $this->resource->load($ruleData, $entityId);
        if (!$ruleData->getId()) {
            throw new NoSuchEntityException(__('Data with id "%1" does not exist.', $entityId));
        }
        return $ruleData;
    }

    /**
     * @inheritdoc
     */
    public function getBySalesruleId($ruleId)
    {
        /** @var RuleData $ruleData */
        $ruleData = $this->ruleDataFactory->create();
        $this->resource->load($ruleData, $ruleId, RuleDataInterface::SALESRULE_ID);
        if (!$ruleData->getEntityId()) {
            throw new NoSuchEntityException(__('Rule data with specified ID "%1" not found.', $ruleId));
        }

        return $ruleData;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(RuleDataInterface $ruleData)
    {
        try {
            $ruleDataModel = $this->ruleDataFactory->create();
            $this->resource->load($ruleDataModel, $ruleData->getEntityId());
            $this->resource->delete($ruleDataModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Data: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($entityId)
    {
        return $this->delete($this->getById($entityId));
    }
}
