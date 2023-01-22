<?php

namespace Tan\SpcSalesRule\Api\Rule;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Tan\SpcSalesRule\Api\Data\Rule\FreeGiftInterface;

/**
 * Interface FreeGiftRepositoryInterface
 */
interface FreeGiftRepositoryInterface
{
    /**
     * @param int $freeGiftSalesRuleRowId
     * @return FreeGiftInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $freeGiftSalesRuleRowId): FreeGiftInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * @param FreeGiftInterface $freeGiftRule
     * @return FreeGiftRepositoryInterface
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     */
    public function save(FreeGiftInterface $freeGiftRule): FreeGiftRepositoryInterface;

    /**
     * @param FreeGiftInterface $freeGiftRule
     * @return FreeGiftRepositoryInterface
     * @throws CouldNotDeleteException
     */
    public function delete(FreeGiftInterface $freeGiftRule): FreeGiftRepositoryInterface;

    /**
     * @param int $freeGiftSalesRuleRowId
     * @return FreeGiftRepositoryInterface
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $freeGiftSalesRuleRowId): FreeGiftRepositoryInterface;
}
