<?php

namespace Tan\SpcSalesRule\Plugin\Sales\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Logger\Monolog;
use Magento\Sales\Model\Order as SourceOrder;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection;

class Order
{

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var State
     */
    private $state;

    /**
     * @var Monolog
     */
    private $logger;

    public function __construct(
        RequestInterface $request,
        State $state,
        Monolog $logger
    ) {
        $this->request = $request;
        $this->state = $state;
        $this->logger = $logger;
    }

    /**
     * @param SourceOrder $subject
     * @param Collection $collection
     *
     * @return Collection
     */
    public function afterGetItemsCollection(SourceOrder $subject, Collection $collection)
    {
        try {
            if ($this->request->getActionName() == 'reorder') {
                /** @var Item $product */
                foreach ($collection->getItems() as $product) {
                    $productOptions = $product->getProductOptions();

                    // fix remove issue after reorder for spc items
                    if (isset($productOptions['info_buyRequest']['spc'])) {
                        $productOptions['info_buyRequest']['spc']['item_id'] = $product->getItemId();
                        $product->setProductOptions($productOptions);
                    }

                    // remove free gift items intime reorder process
                    if (isset($productOptions['info_buyRequest']['spc']['free_gift'])) {
                        $collection->removeItemByKey($product->getId());
                    }
                }
            }
        } catch (LocalizedException $e) {
            $this->logger->warning($e->getMessage());
        }

        return $collection;
    }

    /**
     * @return bool
     * @throws LocalizedException
     */
    public function isAdmin(): bool
    {
        return $this->state->getAreaCode() == Area::AREA_ADMINHTML;
    }
}
