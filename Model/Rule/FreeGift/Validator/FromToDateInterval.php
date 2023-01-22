<?php

namespace Tan\SpcSalesRule\Model\Rule\FreeGift\Validator;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\SalesRule\Model\Rule;
use Tan\SpcSalesRule\Model\Rule\FreeGift\ValidatorInterface;

/**
 * Class FromToDateInterval
 */
class FromToDateInterval implements ValidatorInterface
{
    /** @var string */
    const FROM_TO_DATE_INTERVAL_FLAG = 'fromToDateInterval';

    /** @var TimezoneInterface */
    private $timezone;

    /**
     * FromToDateInterval constructor.
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        TimezoneInterface $timezone
    ) {
        $this->timezone = $timezone;
    }

    /**
     * Validate the sales rule dates is inside the promotion's date interval
     *
     * @param Rule $salesRule
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function process(Rule $salesRule): bool
    {
        $now = $this->timezone->date();
        $fromDate = $salesRule->getFromDate() ?: null;
        $toDate = $salesRule->getToDate() ?: null;

        if (($fromDate !== null && $now < $fromDate) || ($toDate !== null && $now > $toDate)) {
            return false;
        }

        return true;
    }
}
