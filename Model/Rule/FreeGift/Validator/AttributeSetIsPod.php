<?php

namespace Tan\SpcSalesRule\Model\Rule\FreeGift\Validator;

use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Eav\Api\Data\AttributeSetInterface;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\SalesRule\Model\Rule;
use Tan\ProductAttributes\Model\Product\AttributeSet;
use Tan\SpcSalesRule\Model\Rule\FreeGift\ValidatorInterface;

/**
 * Class AttributeSetIsPod
 */
class AttributeSetIsPod implements ValidatorInterface
{
    /** @var string */
    const ATTRIBUTE_SET_IS_POD_FLAG = 'attributeSetIsPod';

    /** @var AttributeSetRepositoryInterface */
    private $attributeSetRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /**
     * BoxSizeActionCondition constructor.
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        AttributeSetRepositoryInterface $attributeSetRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->attributeSetRepository = $attributeSetRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param Rule $salesRule
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function process(Rule $salesRule): bool
    {
        $actionsArea = $salesRule->getActions();
        if ($actionsArea) {
            $actions = $actionsArea->getActions();
            if ($actions) {
                foreach ($actions as $condition) {
                    if ($condition->getAttribute() == Set::KEY_ATTRIBUTE_SET_ID) {
                        $attributeSetId = $this->getAttributeSetByName(AttributeSet::POD_ATTRIBUTE_SET)->getAttributeSetId();
                        return $condition->getValue() === $attributeSetId;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param $attributeSetName
     * @return AttributeSetInterface
     */
    private function getAttributeSetByName($attributeSetName)
    {
        return current($this->attributeSetRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter('attribute_set_name', $attributeSetName, 'eq')
                ->setPageSize(1)
                ->create()
        )->getItems());
    }
}
