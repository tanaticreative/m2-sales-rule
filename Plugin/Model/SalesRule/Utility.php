<?php

namespace Tan\SpcSalesRule\Plugin\Model\SalesRule;

use Magento\Bundle\Model\Product\Type;
use Magento\Bundle\Model\ResourceModel\Selection;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Utility as SalesRuleUtility;
use Tan\SpcSalesRule\Api\Data\RuleDataInterface;
use Tan\SpcSalesRule\Api\Rule\FreeGiftRepositoryInterface;

class Utility
{
    /**
     * @var FreeGiftRepositoryInterface
     */
    private $freeGiftRuleRepository;

    public function __construct(FreeGiftRepositoryInterface $freeGiftRuleRepository)
    {
        $this->freeGiftRuleRepository = $freeGiftRuleRepository;
    }

    /**
     * @param SalesRuleUtility $subject
     * @param $result
     * @param AbstractItem $item
     * @param Rule $rule
     * @return float|int|mixed
     */
    public function afterGetItemQty(SalesRuleUtility $subject, $result, $item, $rule)
    {
        $product = $item->getProduct();
        $qty = $item->getTotalQty();

        if (!$this->isNeedToApply($rule, $product)
        ) {
            return $result;
        }

        $typeInstance = $product->getTypeInstance();
        $optionsIds = $typeInstance->getOptionsIds($product);
        $selections = $typeInstance->getSelectionsCollection($optionsIds, $product);

        $childrenQty = 0;

        /** @var  Selection $selection */
        foreach ($selections as $selection) {
            $childrenQty += $selection->getData('selection_qty');
        }

        $qty *= $childrenQty;

        $discountQty = $rule->getData('discount_qty');

        return $discountQty ? min($qty, $discountQty) : $qty;
    }

    /**
     * @param Rule $rule
     * @param Product $product
     * @return bool
     */
    private function isNeedToApply($rule, $product): bool
    {
        if (!$rule->getData(RuleDataInterface::PROMOTION_BUNDLE_PRODUCT_COUNT) || $product->getTypeId() != Type::TYPE_CODE) {
            return false;
        }

        try {
            $this->freeGiftRuleRepository->getById($rule->getData('row_id'));
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return true;
    }
}
