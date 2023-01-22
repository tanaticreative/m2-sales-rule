<?php

namespace Tan\SpcSalesRule\Model\Rule\FreeGift;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Model\Converter\ToModel;
use Magento\SalesRule\Model\Rule;
use Tan\Spc\Model\Config;
use Tan\SpcSalesRule\Api\Rule\FreeGiftRepositoryInterface;
use Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item\Option;
use Tan\SpcSalesRule\Model\Rule\Condition\Quote\Item\Option\Size;
use Tan\SpcSalesRule\Model\Rule\FreeGift\Validator\FromToDateInterval;
use Psr\Log\LoggerInterface;

/**
 * Class Service
 *
 * General considerations:
 *
 * After a sales rule is validated as a free gift sales rule, it is cached in the $freeGiftRulesDataCache
 * private class property. This is useful for:
 * - avoiding processing the same rule multiple times;
 * - avoiding unexpected behaviour if the service is used in a long running process (queue, cron, etc.).
 * The free gift sales rule data cache unique identifier is an encoded string generated based on the box size and the validation flags.
 * If the case, make sure to add/remove the necessary validation flags using available class methods before attempting
 * to retrieve the free gift sales rule, to make sure the correct data is retrieved form cache.
 */
class Service
{
    /**
     * Free gift sales rule validators
     * @var ValidatorInterface[]
     */
    private $freeGiftRuleValidators;

    /**
     * Free gift sales rule validation flags
     * @var array
     */
    private $freeGiftRuleValidationFlags = [];

    /**
     * Data cache for free gift sales rules which have been qualified as valid
     * @var array
     */
    private $freeGiftRulesDataCache;

    /** @var FreeGiftRepositoryInterface */
    private $freeGiftRepository;

    /** @var RuleRepositoryInterface */
    private $ruleRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var Config */
    private $spcConfig;

    /** @var ToModel */
    private $toModel;

    /** @var LoggerInterface */
    private $logger;

    /**
     * Service constructor.
     * @param FreeGiftRepositoryInterface $freeGiftRuleRepository
     * @param RuleRepositoryInterface $ruleRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Config $spcConfig
     * @param ToModel $toModel
     * @param LoggerInterface $logger
     * @param array $freeGiftRuleValidators
     */
    public function __construct(
        FreeGiftRepositoryInterface $freeGiftRuleRepository,
        RuleRepositoryInterface $ruleRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Config $spcConfig,
        ToModel $toModel,
        LoggerInterface $logger,
        array $freeGiftRuleValidators
    ) {
        $this->freeGiftRepository = $freeGiftRuleRepository;
        $this->ruleRepository = $ruleRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->spcConfig = $spcConfig;
        $this->toModel = $toModel;
        $this->freeGiftRuleValidators = $freeGiftRuleValidators;
        $this->logger = $logger;
    }

    /**
     * Get the free gift sales rule product SKU list.
     * If a current selection is passed then the final free gift SKU list will be filtered by it.
     *
     * $currentSelection = ['SKU1', 'SKU2']
     *
     * @param int $boxSize
     * @param array $currentSelection
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getProductList(int $boxSize, array $currentSelection = [])
    {
        try {
            $freeGiftSalesRule = $this->getFreeGiftSalesRule($boxSize);
            $extensionAttributes = $freeGiftSalesRule->getExtensionAttributes();

            $freeGiftRuleSkuList = [];
            if ($extensionAttributes['ampromo_rule'] ?? false && $extensionAttributes['ampromo_rule']['sku'] ?? false) {
                $freeGiftRuleSkuList = $extensionAttributes['ampromo_rule']['sku'];
            }

            $productList = explode(',', str_replace(' ', '', $freeGiftRuleSkuList));
            $freeGiftDelta = array_diff($productList, $currentSelection);

            return $freeGiftDelta ? $freeGiftDelta : $productList;
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__($e->getMessage()));
        }
    }

    /**
     * @param int $size
     * @param array $boxProductsSku
     * @return bool
     * @throws LocalizedException
     */
    public function hasFreeGift(int $size, array $boxProductsSku): bool
    {
        try {
            return (bool)count($this->getProductList($size, $boxProductsSku));
        } catch (NoSuchEntityException $e) {
            $this->logger->warning($e->getMessage());
            return false;
        }
    }

    /**
     * Get the free gift sales rule number of gift items.
     *
     * @param int $boxSize
     * @return float
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getNumberOfGiftItems(int $boxSize)
    {
        try {
            $freeGiftSalesRule = $this->getFreeGiftSalesRule($boxSize);

            return $freeGiftSalesRule->getDiscountAmount();
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__($e->getMessage()));
        }
    }

    /**
     * Get the free gift sales rule based on spc box size and validation flags.
     *
     * @param int $boxSize
     * @return Rule
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getFreeGiftSalesRule(int $boxSize)
    {
        if (!$this->spcConfig->isSpcEnable()) {
            throw new LocalizedException(__('Sales per Capsule in not enabled.'));
        }

        $uId = $this->generateDataCacheUid($boxSize);
        $freeGiftRule = $this->freeGiftRulesDataCache[$uId] ?? [];

        if ($freeGiftRule) {
            return $freeGiftRule;
        }

        $eligibleFreeGiftSalesRules = $this->getEligibleFreeGiftSalesRules();
        if ($eligibleFreeGiftSalesRules) {
            foreach ($eligibleFreeGiftSalesRules as $eligibleFreeGiftSalesRule) {
                $eligibleFreeGiftSalesRule = $this->toModel->toModel($eligibleFreeGiftSalesRule);
                if ($this->isFreeGiftSalesRule($eligibleFreeGiftSalesRule, $boxSize)) {
                    return $eligibleFreeGiftSalesRule;
                }
            }
        }

        throw new NoSuchEntityException(__('No free gift sales rule found.'));
    }

    /**
     * @param string $flag
     * @return $this
     */
    public function addValidationFlag(string $flag)
    {
        $flagKey = array_search($flag, $this->freeGiftRuleValidationFlags);

        if(!$flagKey) {
            $this->freeGiftRuleValidationFlags[] = $flag;
        }

        return $this;
    }

    /**
     * @param string $flag
     * @return $this
     */
    public function removeValidationFlag(string $flag)
    {
        $flagKey = array_search($flag, $this->freeGiftRuleValidationFlags);

        if ($flagKey) {
            unset($this->freeGiftRuleValidationFlags[$flagKey]);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function resetValidationFlags()
    {
        $this->freeGiftRuleValidationFlags = [];

        return $this;
    }

    /**
     * Process all attached validators to determine if the free gift sales rule is valid.
     *
     * @param Rule $salesRule
     * @param int $boxSize
     * @return bool
     */
    private function isFreeGiftSalesRule(Rule $salesRule, int $boxSize)
    {
        $this->addValidationFlag(FromToDateInterval::FROM_TO_DATE_INTERVAL_FLAG);

        // make sure all validator which have been added specifically are executed last
        array_reverse($this->freeGiftRuleValidationFlags);

        // execute all attached validation flag processors
        foreach ($this->freeGiftRuleValidationFlags as $flag) {
            /** @var ValidatorInterface $flagProcessor */
            $flagProcessor = $this->freeGiftRuleValidators[$flag] ?? null;
            if ($flagProcessor instanceof ValidatorInterface && !$flagProcessor->process($salesRule)) {
                return false;
            }
        }

        $actionsArea = $salesRule->getActions();
        if ($actionsArea) {
            $actions = $actionsArea->getActions();
            if ($actions) {
                foreach ($actions as $condition) {
                    if ($condition->getAttribute() != Option::QUOTE_ITEM_OPTION) {
                        continue;
                    }
                    foreach ($condition->getActions() as $subCondition) {
                        if ($subCondition->getAttribute() == Size::QUOTE_ITEM_OPTION_SPC_BOX_SIZE) {
                            return (int) $subCondition->getValue() === $boxSize;
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Get all the eligible free gift sales rules.
     * In order for a sales rule to be eligible, it has to have a link in the `tan_spc_free_gift_salesrule` table.
     *
     * @return RuleInterface[]
     * @throws NoSuchEntityException
     */
    private function getEligibleFreeGiftSalesRules()
    {
        $freeGiftSalesRules = $this->freeGiftRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        if ($freeGiftSalesRules) {
            try {
                return $this->ruleRepository
                    ->getList($this->searchCriteriaBuilder
                        ->addFilter('row_id', array_keys($freeGiftSalesRules), 'in')
                        ->create())
                    ->getItems();
            } catch (LocalizedException $e) {
                throw new NoSuchEntityException(__('No free gift sales rule found.'));
            }
        }
    }

    /**
     * Generate an unique identifier for internal data caching of valid free gift rules
     *
     * @param int $boxSize
     * @return string
     */
    private function generateDataCacheUid(int $boxSize)
    {
        return sha1($boxSize . serialize($this->freeGiftRuleValidationFlags));
    }
}
