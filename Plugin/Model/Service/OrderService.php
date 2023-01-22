<?php
namespace Tan\SpcSalesRule\Plugin\Model\Service;

use Magento\Bundle\Model\Product\Type;
use Magento\Bundle\Model\ResourceModel\Selection;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Service\OrderService as SalesOrderService;
use Tan\Spc\Model\Box\Order\Item\Repository;

class OrderService
{
    /** @var Repository */
    private $spcItemRepository;

    public function __construct(Repository $spcItemRepository)
    {
        $this->spcItemRepository = $spcItemRepository;
    }

    public function afterPlace(SalesOrderService $orderService, OrderInterface $order): OrderInterface
    {
        $boxesFreeGifts = $this->getFreeGifts($order);
        if (!empty($boxesFreeGifts)) {
            foreach ($boxesFreeGifts as $box) {
                foreach ($box['items'] as $boxItemId) {
                    $boxItem = $this->spcItemRepository->getByItemId($boxItemId);
                    if ($boxItem->getEntityId()) {
                        $boxItem->setData('spc_box_free_gift_qty', $box['free_gift_qty']);
                        //phpcs:ignore
                        $boxItem->save();
                    }
                }
            }
        }
        return $order;
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    private function getFreeGifts(OrderInterface $order): array
    {
        $items = $order->getAllItems();

        $boxesFreeGifts = [];
        foreach ($items as $item) {
            $productOptions = $item->getProductOptions();
            if (empty($productOptions['spc'])) {
                continue;
            }
            $boxId = $productOptions['spc']['uid'];
            if (!isset($boxesFreeGifts[$boxId])) {
                $boxesFreeGifts[$boxId] = [];
                $boxesFreeGifts[$boxId]['free_gift_qty'] = 0;
                $boxesFreeGifts[$boxId]['items'] = [];
            }

            if (!empty($productOptions['spc']['free_gift']) && !empty($productOptions['info_buyRequest']['qty'])) {
                if (!$item->getParentItemId()) {
                    $product = $item->getProduct();
                    $qty = $item->getQtyOrdered();
                    if ($product->getTypeId() == Type::TYPE_CODE) {
                        $qty = $this->getBundleQty($product, $qty);
                    }
                    $boxesFreeGifts[$boxId]['free_gift_qty'] += $qty;
                }
            }
            $boxesFreeGifts[$boxId]['items'][] = $item->getItemId();
        }
        return $boxesFreeGifts;
    }


    /**
     * @param $product
     * @param $qty
     * @return int
     */
    private function getBundleQty($product, $qty)
    {
        $typeInstance = $product->getTypeInstance();
        $optionsIds = $typeInstance->getOptionsIds($product);
        $selections = $typeInstance->getSelectionsCollection($optionsIds, $product);

        $childrenQty = 0;

        /** @var  Selection $selection */
        foreach ($selections as $selection) {
            $childrenQty += $selection->getData('selection_qty');
        }

        $qty *= $childrenQty;

        return $qty;
    }
}
