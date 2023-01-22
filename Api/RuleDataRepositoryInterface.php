<?php

namespace Tan\SpcSalesRule\Api;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Tan\SpcSalesRule\Api\Data\RuleDataInterface;

interface RuleDataRepositoryInterface
{

    /**
     * @param RuleDataInterface $ruleData
     * @return RuleDataInterface
     * @throws LocalizedException
     */
    public function save(RuleDataInterface $ruleData): RuleDataInterface;

    /**
     * @param int $entityId
     * @return RuleDataInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId);

    /**
     * Get by RuleId
     *
     * @param int $ruleId
     *
     * @return RuleDataInterface
     * @throws NoSuchEntityException
     */
    public function getBySalesruleId($ruleId);


    /**
     * @param RuleDataInterface $ruleData
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(RuleDataInterface $ruleData);

    /**
     * @param int $entityId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $entityId);
}
