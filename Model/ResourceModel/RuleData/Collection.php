<?php

namespace Tan\SpcSalesRule\Model\ResourceModel\Rule;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Tan\SpcSalesRule\Model\RuleData;
use Tan\SpcSalesRule\Model\ResourceModel\RuleData as RuleDataResource;

class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            RuleData::class,
            RuleDataResource::class
        );
    }
}

