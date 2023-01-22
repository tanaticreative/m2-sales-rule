<?php

namespace Tan\SpcSalesRule\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Tan\SpcSalesRule\Api\Data\RuleDataInterface;
use Tan\SpcSalesRule\Model\ResourceModel\RuleData as RuleDataResource;

class RuleData extends AbstractModel implements IdentityInterface, RuleDataInterface
{
    /** @const string */
    const CACHE_TAG = 'tan_spc_salesrule_rule';

    protected $_eventPrefix = 'tan_spc_salesrule_rule';

    protected function _construct()
    {
        $this->_init(RuleDataResource::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentities()
    {
        return [static::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * {@inheritDoc}
     */
    public function getSalesruleId()
    {
        return $this->_getData(self::SALESRULE_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setSalesruleId($salesruleId): RuleDataInterface
    {
        return $this->setData(self::SALESRULE_ID, $salesruleId);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntityId()
    {
        return $this->_getData(self::ENTITY_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setEntityId($entityId): RuleDataInterface
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * {@inheritDoc}
     */
    public function getPromotionBundleProductCount()
    {
        return $this->_getData(self::PROMOTION_BUNDLE_PRODUCT_COUNT);
    }

    /**
     * {@inheritDoc}
     */
    public function setPromotionBundleProductCount(int $count): RuleDataInterface
    {
        return $this->setData(self::PROMOTION_BUNDLE_PRODUCT_COUNT, $count);
    }
}
