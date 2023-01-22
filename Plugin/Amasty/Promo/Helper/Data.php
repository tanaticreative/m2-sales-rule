<?php

namespace Tan\SpcSalesRule\Plugin\Amasty\Promo\Helper;

use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\LocalizedException;
use Tan\Spc\Model\Category\Service;
use Tan\SpcSalesRule\Model\Rule\FreeGift\Service as FreeGiftService;
use Psr\Log\LoggerInterface;

/**
 * Class Data
 * @package Tan\SpcSalesRule\Plugin\Amasty\Promo\Helper
 */
class Data
{
    /**
     * @var Service
     */
    private $spcCategoryService;

    /**
     * @var FreeGiftService
     */
    private $freeGiftService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Data constructor.
     * @param Service $spcCategoryService
     * @param FreeGiftService $freeGiftService
     * @param LoggerInterface $logger
     */
    public function __construct(
        Service $spcCategoryService,
        FreeGiftService $freeGiftService,
        LoggerInterface $logger
    ) {
        $this->spcCategoryService = $spcCategoryService;
        $this->freeGiftService = $freeGiftService;
        $this->logger = $logger;
    }

    /**
     * Get Rule Ids that should be excluded
     * @return array
     */
    public function getRuleIdsToExclude(): array
    {
        $ruleIdsToExclude = [];
        $sizes = $this->getSizeBoxes();
        foreach ($sizes as $size) {
            try {
                $ruleIdsToExclude[] = $this->freeGiftService->getFreeGiftSalesRule($size)->getRuleId();
            } catch (LocalizedException $e) {
                $this->logger->warning($e->getMessage());
            }
        }

        return $ruleIdsToExclude;
    }

    /**
     * @param $subject
     * @param $result
     * @return array
     */
    public function afterGetPromoItemsDataArray(
        $subject,
        $result
    ) {
        $originalResult = $result;
        $newResult = $originalResult;

        $excludeRuleIds = $this->getRuleIdsToExclude();

        $newQty = 0;

        if (!empty($excludeRuleIds) && !empty($originalResult['triggered_products'])) {
            try {
                $allowedForPopup = array_diff_key($originalResult['triggered_products'], array_flip($excludeRuleIds));

                foreach ($excludeRuleIds as $exRuleId) {
                    if (!empty($originalResult['triggered_products'][$exRuleId])) {
                        foreach ($originalResult['triggered_products'][$exRuleId]['sku'] as $sku => $info) {
                            if (!$this->isSkuExistsInRules($sku, $allowedForPopup)) {
                                unset($newResult['promo_sku'][$sku]);
                            }
                            $newQty += $info['qty'];
                        }
                        unset($newResult['triggered_products'][$exRuleId]);
                    }
                }
            } catch (\Exception $e) {
                $this->logger->warning($e->getMessage());
            }
        }

        $newResult['common_qty'] = $newResult['common_qty'] - $newQty;

        return $newResult;
    }

    /**
     * @return array
     */
    private function getSizeBoxes(): array
    {
        /** @var Category $category */
        $category = $this->spcCategoryService->getSpcCategory();

        $sizes = [];
        $boxSizes = explode(',', $category->getData('box_size'));
        foreach ($boxSizes as $boxSize) {
            $sizes[$boxSize] = $boxSize;
        }

        return $sizes;
    }

    private function isSkuExistsInRules($sku, $allowedForPopup)
    {
        $isExist = false;
        foreach ($allowedForPopup as $data) {
            if (in_array($sku, array_keys($data['sku']))) {
                $isExist = true;
                break;
            }
        }

        return $isExist;
    }
}
