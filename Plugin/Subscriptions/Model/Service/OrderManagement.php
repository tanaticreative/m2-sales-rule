<?php

namespace Tan\SpcSalesRule\Plugin\Subscriptions\Model\Service;

use Magento\Quote\Model\Quote\Item as QuoteItem;
use Tan\Subscriptions\Api\Data\SubscriptionInterface;

class OrderManagement
{
    /**
     * @param $subject
     * @param $quoteItem
     * @param SubscriptionInterface $subscription
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSaveSubscriptionItem($subject, $quoteItem, SubscriptionInterface $subscription) {

        if ($quoteItem instanceof QuoteItem && $quoteItem->getOptionByCode('spc_box_free_gift')) {
            return [[], $subscription];
        }

        return [$quoteItem, $subscription];
    }
}
