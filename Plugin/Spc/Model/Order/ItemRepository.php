<?php

namespace Tan\SpcSalesRule\Plugin\Spc\Model\Order;

use Magento\Sales\Api\Data\OrderItemExtension;
use Magento\Sales\Api\Data\OrderItemExtensionInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Tan\Spc\Model\Box\Order\Item;
use Tan\Spc\Plugin\Model\Order\ItemRepository as SpcPluginItemRepository;

/**
 * Class ItemRepository
 */
class ItemRepository
{
    /**
     * @param SpcPluginItemRepository $subject
     * @param Item $boxOrderItem
     * @param OrderItemInterface $orderItem
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeGetBoxOrderItem(
        SpcPluginItemRepository $subject,
        Item $boxOrderItem,
        OrderItemInterface $orderItem
    ) {
        $boxOrderItem->setFreeGift($orderItem->getSpcBoxFreeGift());
        return [$boxOrderItem, $orderItem];
    }

    /**
     * @param SpcPluginItemRepository $subject
     * @param OrderItemExtensionInterface $extensionAttributes
     * @param Item $boxOrderItem
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeGetAdditionalExtensionAttributes(
        SpcPluginItemRepository $subject,
        OrderItemExtensionInterface $extensionAttributes,
        Item $boxOrderItem
    ) {
        $extensionAttributes->setSpcBoxFreeGift($boxOrderItem->getFreeGift());
        $extensionAttributes->setSpcBoxFreeGiftQty($boxOrderItem->getSpcBoxFreeGiftQty());
        return [$extensionAttributes, $boxOrderItem];
    }
}
