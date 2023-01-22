<?php

namespace Tan\SpcSalesRule\Model\RuleData;

use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\SalesRule\Api\Data\RuleInterface as SalesRuleInterface;
use Magento\SalesRule\Model\Data\Rule as BaseDataRule;
use Magento\SalesRule\Model\Rule as BaseRule;
use Tan\SpcSalesRule\Api\Data\RuleDataInterface;
use Tan\SpcSalesRule\Api\Data\RuleDataInterfaceFactory;
use Tan\SpcSalesRule\Model\ResourceModel\RuleData as RuleDataResource;
use Tan\SpcSalesRule\Model\RuleDataRepository;

class ReadHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var RuleDataResource
     */
    private $ruleDataResource;

    /**
     * @var RuleDataInterfaceFactory
     */
    private $ruleDataFactory;

    /**
     * @var RuleDataRepository
     */
    private $ruleDataRepository;

    public function __construct(
        RuleDataInterfaceFactory $ruleDataFactory,
        RuleDataResource $ruleDataResource,
        MetadataPool $metadataPool,
        RuleDataRepository $ruleDataRepository
    ) {
        $this->ruleDataFactory = $ruleDataFactory;
        $this->ruleDataResource = $ruleDataResource;
        $this->metadataPool = $metadataPool;
        $this->ruleDataRepository = $ruleDataRepository;
    }

    /**
     * @param BaseRule|BaseDataRule $entity
     * @param array $arguments
     *
     * @return BaseRule|BaseDataRule
     */
    public function execute($entity, $arguments = [])
    {
        $linkField = $this->metadataPool->getMetadata(SalesRuleInterface::class)->getLinkField();
        $ruleLinkId = $entity->getDataByKey($linkField);

        if ($ruleLinkId) {
            try {
                /** @var RuleDataInterface $rule */
                $ruleData = $this->ruleDataRepository->getBySalesruleId($ruleLinkId);
            } catch (NoSuchEntityException $exception) {
                /** @var RuleDataInterface $rule */
                $ruleData = $this->ruleDataFactory->create();
            }

            $entity->setData(RuleDataInterface::PROMOTION_BUNDLE_PRODUCT_COUNT, $ruleData->getPromotionBundleProductCount());
        }

        return $entity;
    }
}
