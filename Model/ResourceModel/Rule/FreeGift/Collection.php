<?php

namespace Tan\SpcSalesRule\Model\ResourceModel\Rule\FreeGift;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Tan\SpcSalesRule\Api\Data\Rule\FreeGiftInterface;
use Tan\SpcSalesRule\Model\ResourceModel\FreeGift as FreeGiftResource;
use Tan\SpcSalesRule\Model\Rule\FreeGift;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritDoc}
     */
    protected $_idFieldName = FreeGiftInterface::FIELD_SALESRULE_ROW_ID;

    /**
     * {@inheritDoc}
     */
    protected $_eventPrefix = 'tan_spc_free_gift_salesrule';

    /**
     * {@inheritDoc}
     */
    protected $_eventObject = 'tan_spc_free_gift_salesrule_collection';

    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init(FreeGift::class, FreeGiftResource::class);
    }
}
