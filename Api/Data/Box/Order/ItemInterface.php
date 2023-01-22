<?php

namespace Tan\SpcSalesRule\Api\Data\Box\Order;

/**
 * Interface ItemInterface
 */
interface ItemInterface
{
    /** @var string */
    const FREE_GIFT = 'free_gift';

    /**
     * @return int
     */
    public function getFreeGift(): ?int;

    /**
     * @param int $value
     *
     * @return ItemInterface
     */
    public function setFreeGift(int $value): ItemInterface;

}
