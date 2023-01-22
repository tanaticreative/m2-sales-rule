<?php

namespace Tan\SpcSalesRule\Model\Rule\FreeGift\Validator;

use Amasty\Promo\Api\Data\GiftRuleInterface;
use Magento\SalesRule\Model\Rule;
use Tan\SpcSalesRule\Model\Rule\FreeGift\ValidatorInterface;

/**
 * Class AmPromoSalesRule
 */
class SalesRuleIsAmPromo implements ValidatorInterface
{
    /** @var string */
    const AM_PROMO_SALES_RULE_FLAG = 'amPromoSalesRule';

    private $amPromoSalesRuleTypes = [
        GiftRuleInterface::PER_PRODUCT
    ];

    /**
     * Validate if the sales rule is an Amasty Free Gift Rule
     *
     * @param Rule $salesRule
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function process(Rule $salesRule): bool
    {
        if ($salesRule->getSimpleAction() && in_array($salesRule->getSimpleAction(), $this->amPromoSalesRuleTypes)) {
            return true;
        }

        return false;
    }
}
