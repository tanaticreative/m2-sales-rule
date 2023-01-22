<?php

namespace Tan\SpcSalesRule\Model\Box\Order;

use Tan\SpcSalesRule\Api\Data\Box\Order\ItemInterface;

/**
 * Class Item
 */
class Item extends \Tan\Spc\Model\Box\Order\Item implements ItemInterface
{
    /**
     * {@inheritDoc}
     */
    public function getFreeGift(): int
    {
        return $this->getData(ItemInterface::FREE_GIFT);
    }

    /**
     * {@inheritDoc}
     */
    public function setFreeGift(int $value): ItemInterface
    {
        return $this->setData(ItemInterface::FREE_GIFT, $value);
    }
}
