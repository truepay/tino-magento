<?php

namespace Tino\Payment\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Tino\Payment\Api\Data\WebhookInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

interface WebhookRepositoryInterface
{
    /**
     * @param WebhookInterface $webhook
     * @return WebhookInterface
     * @throws CouldNotSaveException
     */
    public function save(WebhookInterface $webhook);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param WebhookInterface $webhook
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(WebhookInterface $webhook);
}
