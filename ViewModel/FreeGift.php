<?php
namespace Tan\SpcSalesRule\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Tan\SpcSalesRule\Model\Rule\FreeGift\Service;

class FreeGift implements ArgumentInterface
{
    /** @var Service $service */
    private $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function hasFreeGift($box)
    {
        $boxProductsSku = [];
        foreach ($box->getData('box_products') as $boxTplProduct) {
            $product = $boxTplProduct->getProduct();
            if ('bundle' === $product->getTypeId()) {
                $typeInstance = $product->getTypeInstance();
                $optionIds = $typeInstance->getOptionsIds($product);
                $selectionsCollection = $typeInstance->getSelectionsCollection($optionIds, $product);
                foreach ($selectionsCollection as $selectionProduct) {
                    $boxProductsSku[] = $selectionProduct->getSku();
                }
            } else {
                $boxProductsSku[] = $product->getSku();
            }
        }

        return $this->service->hasFreeGift($box->getSize(), $boxProductsSku);
    }
}
