<?php

namespace Tan\SpcSalesRule\Model\Rule;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Tan\SpcSalesRule\Api\Data\Rule\FreeGiftInterface;
use Tan\SpcSalesRule\Model\ResourceModel\FreeGift as FreeGiftResource;

/**
 * Class FreeGift
 */
class FreeGift extends AbstractModel implements IdentityInterface, FreeGiftInterface
{
    /** @const string */
    const CACHE_TAG = 'tan_spc_free_gift_salesrule';

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
    protected function _construct()
    {
        $this->_init(FreeGiftResource::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getSalesRuleRowId(): int
    {
        return $this->_getData(static::FIELD_SALESRULE_ROW_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setSalesRuleRowId(int $value): FreeGiftInterface
    {
        return $this->setData(static::FIELD_SALESRULE_ROW_ID, $value);
    }
}
