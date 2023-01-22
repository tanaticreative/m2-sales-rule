<?php

namespace Tan\SpcSalesRule\Model\RuleData;

use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\SalesRule\Api\Data\RuleInterface as SalesRuleInterface;
use Tan\SpcSalesRule\Api\Data\RuleDataInterface;
use Tan\SpcSalesRule\Api\Data\RuleDataInterfaceFactory;
use Tan\SpcSalesRule\Model\ResourceModel\RuleData;
use Tan\SpcSalesRule\Model\RuleDataRepository;
use Magento\Framework\Exception\LocalizedException;

class SaveHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var RuleDataRepository
     */
    private $ruleDataRepository;

    /**
     * @var RuleData
     */
    private $ruleDataResource;

    /**
     * @var RuleDataInterfaceFactory
     */
    private $ruleDataFactory;

    /**
     * @var Snapshot
     */
    private $snapshot;

    public function __construct(
        RuleDataInterfaceFactory $ruleFactory,
        RuleData $ruleDataResource,
        MetadataPool $metadataPool,
        RuleDataRepository $ruleDataRepository,
        Snapshot $snapshot
    ) {
        $this->ruleDataResource = $ruleDataResource;
        $this->ruleDataFactory = $ruleFactory;
        $this->metadataPool = $metadataPool;
        $this->ruleDataRepository = $ruleDataRepository;
        $this->snapshot = $snapshot;
    }

    /**
     * @param object $entity
     * @param array $attributes
     * @return bool|object
     * @throws LocalizedException
     */
    public function execute($entity, $attributes = [])
    {
        $linkField = $this->metadataPool->getMetadata(SalesRuleInterface::class)->getLinkField();
        $ruleLinkId = (int)$entity->getDataByKey($linkField);

        try {
            /** @var RuleDataInterface $rule */
            $rule = $this->ruleDataRepository->getBySalesruleId($ruleLinkId);
        } catch (NoSuchEntityException $exception) {
            /** @var RuleDataInterface $rule */
            $rule = $this->ruleDataFactory->create();
        }

        if ($rule->getId()) {
            $this->snapshot->registerSnapshot($rule);
        }

        $data = $entity->getData();
        if (isset($data[RuleDataInterface::PROMOTION_BUNDLE_PRODUCT_COUNT])) {
            $rule->setData(RuleDataInterface::PROMOTION_BUNDLE_PRODUCT_COUNT, $data[RuleDataInterface::PROMOTION_BUNDLE_PRODUCT_COUNT]);
        }

        if ((int)$rule->getSalesruleId() !== $ruleLinkId) {
            $rule->setEntityId(null);
            $rule->setSalesruleId($ruleLinkId);
        }

        if ($this->snapshot->isModified($rule)) {
            $this->ruleDataRepository->save($rule);
        }

        return $entity;
    }
}
