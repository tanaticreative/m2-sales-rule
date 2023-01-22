<?php

namespace Tan\SpcSalesRule\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Tan\Spc\Model\Config;
use Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item\Option;


/**
 * Class RuleConditionCombine
 */
class RuleConditionCombine implements ObserverInterface
{
    /** @var Config */
    protected $config;

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
     * @param Observer $observer
     *
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        if (!$this->config->isSpcEnable()) {
            return $this;
        }

        $transport = $observer->getAdditional();
        $conditions = $transport->getConditions();

        // any other spc specific conditions should be added from here, as identifying
        // them within the conditions array would not be a straightforward implementation
        $conditions = array_merge_recursive(
            $conditions,
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

        $transport->setConditions($conditions);

        return $this;
    }
}
