<?php

namespace Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item\Option;

use Magento\Catalog\Model\Category;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use Tan\Spc\Model\Category\Service;

/**
 * Class Size
 */
class Size extends AbstractCondition
{
    /** @var string */
    const QUOTE_ITEM_OPTION_SPC_BOX_SIZE = 'spc_box_size';

    /** @var Service */
    private $service;

    /**
     * IsSubscription constructor.
     *
     * @param Context $context
     * @param Service $service
     * @param array $data
     */
    public function __construct(Context $context, Service $service, array $data = [])
    {
        $this->_defaultOperatorOptions = [
            '==' => __('is'),
            '>=' => __('equals or greater than'),
            '<=' => __('equals or less than'),
            '>' => __('greater than'),
            '<' => __('less than')
        ];
        $this->service = $service;

        parent::__construct($context, $data);
    }

    /**
     * @return $this|AbstractCondition
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption([
            static::QUOTE_ITEM_OPTION_SPC_BOX_SIZE => __('Spc box size')
        ]);

        return $this;
    }

    /**
     * @return AbstractCondition
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        return 'numeric';
    }

    /**
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
        /** @var Category $category */
        $category = $this->service->getSpcCategory();

        $options = [];
        $boxSizes = explode(',', $category->getData('box_size'));
        foreach ($boxSizes as $boxSize) {
            $options[$boxSize] = $boxSize;
        }

        $key = 'value_select_options';
        if (!$this->hasData($key)) {
            $this->setData($key, $options);
        }

        return $this->getData($key);
    }

    /**
     * Validate Rule Condition
     *
     * @param AbstractModel $model
     *
     * @return bool
     */
    public function validate(AbstractModel $model)
    {
        if ($model->getData('product_type') != 'box') {
            return false;
        }

        return $this->validateAttribute($model->getData('size'));
    }
}
