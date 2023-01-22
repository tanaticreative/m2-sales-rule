<?php

namespace Tan\SpcSalesRule\Plugin\SalesRule\Model\Rule\Condition\Product;

use Magento\SalesRule\Model\Rule\Condition\Product\Combine;
use Tan\Spc\Model\Config;
use Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item\Option;

/**
 * Class SpcActionConditionGroup
 */
class CombinePlugin
{
    /** @var Config */
    private $config;

    /**
     * RuleConditionCombine constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param Combine $subject
     * @param array $result
     * @return array
     */
    public function afterGetNewChildSelectOptions(Combine $subject, array $result)
    {
        if (!$this->config->isSpcEnable()) {
            return $result;
        }

        $result = array_merge_recursive(
            $result,
            [
                [
                    'label' => __('Spc'),
                    'value' => [
                        [
                            'value' => 'Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item\Option|' . Option::QUOTE_ITEM_OPTION,
                            'label' => __('Quote item option')
                        ]
                    ]
                ]
            ]
        );

        return $result;
    }
}
