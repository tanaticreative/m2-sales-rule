<?php

namespace Tan\SpcSalesRule\Plugin\Model\SalesRule;

use Magento\Framework\DataObject;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\SalesRule\Api\Data\RuleInterface as SalesRuleInterface;
use Magento\SalesRule\Model\Rule as SourceRule;
use Tan\SpcSalesRule\Api\Data\RuleDataInterface;
use Tan\SpcSalesRule\Model\RuleDataRepository as RuleDataRepository;
use Tan\Subscriptions\Model\Error\Exception;

class Rule
{
    /** @var RuleDataRepository */
    private $ruleDataRepository;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    public function __construct(
        RuleDataRepository $ruleDataRepository,
        MetadataPool $metadataPool
    ) {
        $this->ruleDataRepository = $ruleDataRepository;
        $this->metadataPool = $metadataPool;
    }


    /**
     * I'm using this solution instead plugin for SalesRule getList because getList not called on cart page
     * @param SourceRule $rule
     * @param DataObject $object
     * @return DataObject[]
     */
    public function beforeValidate(SourceRule $rule, DataObject $object)
    {
        try {
            $linkField = $this->metadataPool->getMetadata(SalesRuleInterface::class)->getLinkField();
            $ruleLinkId = $rule->getDataByKey($linkField);
            $ruleData = $this->ruleDataRepository->getBySalesruleId($ruleLinkId);
            $rule->setData(RuleDataInterface::PROMOTION_BUNDLE_PRODUCT_COUNT, $ruleData->getPromotionBundleProductCount());
        } catch (\Exception $e) {
            return [$object];
        }

        return [$object];
    }
}
