<?php

namespace Tan\SpcSalesRule\Plugin\Spc\Model\Service;

use Magento\Quote\Model\Quote\Item as QuoteItem;
use Tan\Spc\Api\Data\Box\Order\ItemInterface;
use Tan\Spc\Plugin\Model\Service\OrderService as SpcPluginOrderService;

/**
 * Class OrderService
 */
class OrderService
{
    /**
     * @param SpcPluginOrderService $subject
     * @param ItemInterface $result
     * @param QuoteItem $item
     * @param $orderItem
     * @param $prefix
     * @param $boxUidQtys
     * @param $option
     * @return ItemInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetBoxOrderItem(
        SpcPluginOrderService $subject,
        ItemInterface $result,
        QuoteItem $item,
        $orderItem,
        $prefix,
        $boxUidQtys,
        $option
    ) {
        $option = $item->getOptionByCode('spc_box_free_gift');
        if ($option) {
            $result->setFreeGift($option->getValue());
        }

        return $result;
    }
}
