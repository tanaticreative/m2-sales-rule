<?php

namespace Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item;

use Magento\Framework\Event\ManagerInterface;
use Magento\Rule\Model\Condition\Context;
use Magento\Framework\Model\AbstractModel;
use Magento\SalesRule\Model\Rule\Condition\Address;
use Magento\SalesRule\Model\Rule\Condition\Combine;
use Tan\Spc\Model\Box\Service\ProductGrouping;
use Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item\Option\Size;

/**
 * Class Option
 */
class Option extends Combine
{
    /** @var string */
    const QUOTE_ITEM_OPTION = 'quote_item_option';

    /** @var ProductGrouping */
    private $productGrouping;

    /**
     * Option constructor.
     *
     * @param Context $context
     * @param ManagerInterface $eventManager
     * @param Address $conditionAddress
     * @param ProductGrouping $productGrouping
     * @param array $data
     */
    public function __construct(
        Context $context,
        ManagerInterface $eventManager,
        Address $conditionAddress,
        ProductGrouping $productGrouping,
        array $data = []
    ) {
        parent::__construct($context, $eventManager, $conditionAddress, $data);
        $this->productGrouping = $productGrouping;
        $this->setType(Option::class)->setValue(null);
    }

    /**
     * Load array
     *
     * @param array $arr
     * @param string $key
     * @return $this
     */
    public function loadArray($arr, $key = 'conditions')
    {
        $this->setAttribute($arr['attribute']);
        $this->setOperator($arr['operator']);
        parent::loadArray($arr, $key);
        return $this;
    }

    /**
     * Return as xml
     *
     * @param string $containerKey
     * @param string $itemKey
     * @return string
     */
    public function asXml($containerKey = 'conditions', $itemKey = 'condition')
    {
        $xml = '<attribute>' .
            $this->getAttribute() .
            '</attribute>' .
            '<operator>' .
            $this->getOperator() .
            '</operator>' .
            parent::asXml(
                $containerKey,
                $itemKey
            );
        return $xml;
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(['quote_item_option' => __('quote item option')]);
        return $this;
    }

    /**
     * Load value options
     *
     * @return $this
     */
    public function loadValueOptions()
    {
        return $this;
    }

    /**
     * Load operator options
     *
     * @return $this
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(
            [
                '==' => __('is'),
                '!=' => __('is not'),
                '>=' => __('equals or greater than'),
                '<=' => __('equals or less than'),
                '>' => __('greater than'),
                '<' => __('less than'),
                '()' => __('is one of'),
                '!()' => __('is not one of'),
            ]
        );
        return $this;
    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        $options = ['spc_box_uid' => 'spc_box_uid'];

        $key = 'value_select_options';
        if (!$this->hasData($key)) {
            $this->setData($key, $options);
        }

        return $this->getData($key);
    }

    /**
     * Return as html
     *
     * @return string
     */
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . __(
                "If %1 %2 %3 for a subselection of items in cart matching %4 of these conditions:",
                $this->getAttributeElement()->getHtml(),
                $this->getOperatorElement()->getHtml(),
                $this->getValueElement()->getHtml(),
                $this->getAggregatorElement()->getHtml()
            );
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return [
            [
                'value' => Quantity::class,
                'label' => __('Box qty'),
            ],
            [
                'value' => Size::class,
                'label' => __('Box size'),
            ]
        ];
    }

    /**
     * Validate
     *
     * @param AbstractModel $model
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function validate(AbstractModel $model)
    {
        if (!$this->getConditions()) {
            return false;
        }

        $itemOption = null;
        foreach ($this->productGrouping->groupBoxContentItems($model->getQuote()->getAllVisibleItems(), true) as $item) {
            if ($item->getData('product_type') == 'box' && parent::validate($item)) {
                $itemOption = 'spc_box_uid';
            }
        }

        return $this->validateAttribute($itemOption);
    }
}
