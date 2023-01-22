<?php

namespace Tan\SpcSalesRule\Plugin\Amasty\Promo\Block;

use Amasty\Promo\Block\Items as SourceItems;
use Amasty\Promo\Helper\Data;
use Tan\SpcSalesRule\Model\Rule\FreeGift\Service;
use Psr\Log\LoggerInterface;

class Items
{

    /**
     * @var Service
     */
    private $freeGiftService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $popupHelper;

    public function __construct(
        Service $freeGiftService,
        LoggerInterface $logger,
        Data $popupHelper
    ) {
        $this->freeGiftService = $freeGiftService;
        $this->logger = $logger;
        $this->popupHelper = $popupHelper;
    }

    public function afterGetItems(SourceItems $subject, $result)
    {
        if ($result) {
            try {
                $availableProducts = $this->popupHelper->getPromoItemsDataArray();
                if (!empty($availableProducts['promo_sku'])) {
                    $availableSku = array_keys($availableProducts['promo_sku']);
                    foreach ($result as $key => $promo) {
                        if (!in_array($promo->getSku(), $availableSku)) {
                            $result->removeItemByKey($key);
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->logger->warning($e->getMessage());
            }
        }

        return $result;
    }
}
