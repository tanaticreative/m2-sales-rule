<?php

namespace Tan\SpcSalesRule\Model\Rule\FreeGift;

use Magento\SalesRule\Model\Rule;

/**
 * Interface ProcessorInterface
 */
interface ValidatorInterface
{
    /**
     * Process validation
     *
     * @param Rule $salesRule
     * @return bool
     */
    public function process(Rule $salesRule): bool;
}
