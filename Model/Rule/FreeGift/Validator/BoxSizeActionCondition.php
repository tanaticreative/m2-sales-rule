<?php

namespace Tan\SpcSalesRule\Model\Rule\FreeGift\Validator;

use Magento\SalesRule\Model\Rule;
use Tan\SpcSalesRule\Model\Rule\FreeGift\ValidatorInterface;
use Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item\Option;
use Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item\Option\Size;

/**
 * Class BoxSizeActionCondition
 */
class BoxSizeActionCondition implements ValidatorInterface
{
    /** @var string */
    const BOX_SIZE_ACTION_CONDITION_FLAG = 'boxSizeActionCondition';

    /**
     * Validate box size action condition set on the sales rule
     *
     * @param Rule $salesRule
     * @return bool
     */
    public function process(Rule $salesRule): bool
    {
        $actionsArea = $salesRule->getActions();
        if ($actionsArea) {
            $actions = $actionsArea->getActions();
            if ($actions) {
                foreach ($actions as $condition) {
                    if ($condition->getAttribute() == Option::QUOTE_ITEM_OPTION) {
                        foreach ($condition->getActions() as $subCondition) {
                            return $subCondition->getAttribute() == Size::QUOTE_ITEM_OPTION_SPC_BOX_SIZE && $subCondition->getValue();
                        }
                    }
                }
            }
        }

        return false;
    }
}
