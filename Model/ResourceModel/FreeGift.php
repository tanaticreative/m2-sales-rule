<?php

namespace Tan\SpcSalesRule\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Tan\SpcSalesRule\Api\Data\Rule\FreeGiftInterface;

/**
 * Class FreeGift
 */
class FreeGift extends AbstractDb
{
    /**
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    /**
     * @var string
     */
    protected $_uniqueFields = FreeGiftInterface::FIELD_SALESRULE_ROW_ID;

    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init('tan_spc_free_gift_salesrule', FreeGiftInterface::FIELD_SALESRULE_ROW_ID);
    }
}
