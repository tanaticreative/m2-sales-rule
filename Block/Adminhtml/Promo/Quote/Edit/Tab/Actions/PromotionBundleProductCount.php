<?php

namespace Tan\SpcSalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Actions;

use Magento\Backend\Block\Template;
use Magento\Framework\Phrase;

class PromotionBundleProductCount extends Template
{

    protected $_template = 'promotion_bundle_product_count.phtml';

    /**
     * @return Phrase
     */
    public function getNoticeMessage()
    {
        return __(
            "This option can switch bundle product count in promotion as the number of options or as single product"
        );
    }
}
