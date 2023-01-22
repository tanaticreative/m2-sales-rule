<?php

namespace Tan\SpcSalesRule\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\SalesRule\Model\Rule;
use Tan\SpcSalesRule\Api\Data\Rule\FreeGiftInterfaceFactory;
use Tan\SpcSalesRule\Api\Rule\FreeGiftRepositoryInterface;
use Tan\SpcSalesRule\Model\Rule\FreeGift\Validator\AttributeSetIsPod;
use Tan\SpcSalesRule\Model\Rule\FreeGift\Validator\BoxSizeActionCondition;
use Tan\SpcSalesRule\Model\Rule\FreeGift\Validator\SalesRuleIsAmPromo;
use Psr\Log\LoggerInterface;

/**
 * Class ValidateFreeGiftSalesRule
 */
class ValidateFreeGiftSalesRule implements ObserverInterface
{
    /** @var FreeGiftInterfaceFactory */
    private $freeGiftInterfaceFactory;

    /** @var FreeGiftRepositoryInterface */
    private $freeGiftRepository;

    /** @var AttributeSetIsPod */
    private $attributeSetIsPod;

    /** @var BoxSizeActionCondition */
    private $boxSizeActionCondition;

    /** @var SalesRuleIsAmPromo */
    private $salesRuleIsAmPromo;

    /** @var LoggerInterface */
    private $logger;

    /**
     * ValidateFreeGiftSalesRule constructor.
     * @param FreeGiftInterfaceFactory $freeGiftInterfaceFactory
     * @param FreeGiftRepositoryInterface $freeGiftRepository
     * @param AttributeSetIsPod $attributeSetIsPod
     * @param BoxSizeActionCondition $boxSizeActionCondition
     * @param SalesRuleIsAmPromo $salesRuleIsAmPromo
     * @param LoggerInterface $logger
     */
    public function __construct(
        FreeGiftInterfaceFactory $freeGiftInterfaceFactory,
        FreeGiftRepositoryInterface $freeGiftRepository,
        AttributeSetIsPod $attributeSetIsPod,
        BoxSizeActionCondition $boxSizeActionCondition,
        SalesRuleIsAmPromo $salesRuleIsAmPromo,
        LoggerInterface $logger
    ) {
        $this->freeGiftInterfaceFactory = $freeGiftInterfaceFactory;
        $this->freeGiftRepository = $freeGiftRepository;
        $this->attributeSetIsPod = $attributeSetIsPod;
        $this->boxSizeActionCondition = $boxSizeActionCondition;
        $this->salesRuleIsAmPromo = $salesRuleIsAmPromo;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        /** @var Rule $salesRule */
        $salesRule = $observer->getEvent()->getRule();

        try {
            if (
                $this->attributeSetIsPod->process($salesRule)
                && $this->boxSizeActionCondition->process($salesRule)
                && $this->salesRuleIsAmPromo->process($salesRule)
                && $salesRule->getDiscountAmount()
                && $salesRule->getIsActive()
            ) {
                $freeGiftSalesRule = $this->freeGiftInterfaceFactory->create();
                $freeGiftSalesRule->setSalesRuleRowId($salesRule->getRowId());
                $this->freeGiftRepository->save($freeGiftSalesRule);
            } else {
                $this->freeGiftRepository->deleteById($salesRule->getRowId());
            }
        } catch (Exception $e) {
            $this->logger->warning($e->getMessage());
        }

        return $this;
    }
}
