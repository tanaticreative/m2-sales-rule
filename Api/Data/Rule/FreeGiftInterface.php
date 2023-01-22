<?php

namespace Tan\SpcSalesRule\Api\Data\Rule;

/**
 * Interface FreeGiftRuleInterface
 */
interface FreeGiftInterface
{
    /** @var string */
    const FIELD_SALESRULE_ROW_ID = 'salesrule_row_id';

    /**
     * @return int
     */
    public function getSalesRuleRowId(): int;

    /**
     * @param int $value
     * @return FreeGiftInterface
     */
    public function setSalesRuleRowId(int $value): FreeGiftInterface;
}
