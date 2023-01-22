<?php

namespace Tan\SpcSalesRule\Api\Data;


interface RuleDataInterface
{
    const SALESRULE_ID = 'salesrule_id';
    const ENTITY_ID = 'entity_id';
    const PROMOTION_BUNDLE_PRODUCT_COUNT = 'promotion_bundle_product_count';

    /**
     * Get salesrule_id
     * @return null|int
     */
    public function getSalesruleId();

    /**
     * @param int $salesrule_id
     * @return RuleDataInterface
     */
    public function setSalesruleId(int $salesrule_id): RuleDataInterface;

    /**
     * @return null|int
     */
    public function getEntityId();

    /**
     * @param int $entity_id
     * @return RuleDataInterface
     */
    public function setEntityId(int $entity_id): RuleDataInterface;


    /**
     * @return null|int
     */
    public function getPromotionBundleProductCount();

    /**
     * @param int $count
     * @return RuleDataInterface
     */
    public function setPromotionBundleProductCount(int $count): RuleDataInterface;

}

