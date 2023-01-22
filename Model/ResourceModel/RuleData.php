<?php

namespace Tan\SpcSalesRule\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class RuleData extends AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('tan_spc_salesrule_rule', 'entity_id');
    }
}

