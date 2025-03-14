<?php

declare(strict_types=1);

namespace Tino\Payment\Model;

use Tino\Payment\Api\WebhookRepositoryInterface;
use Tino\Payment\Api\Data\WebhookInterface;
use Tino\Payment\Model\ResourceModel\Webhook as WebhookResource;
use Tino\Payment\Model\ResourceModel\Webhook\CollectionFactory as WebhookCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

class WebhookRepository implements WebhookRepositoryInterface
{
    protected WebhookResource $webhookResource;
    protected WebhookFactory $webhookFactory;
    protected WebhookCollectionFactory $collectionFactory;

    public function __construct(
        WebhookResource $webhookResource,
        WebhookFactory $webhookFactory,
        WebhookCollectionFactory $collectionFactory
    ) {
        $this->webhookResource = $webhookResource;
        $this->webhookFactory = $webhookFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(WebhookInterface $webhook)
    {
        try {
            $this->webhookResource->save($webhook);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__("Could not save webhook"));
        }
        return $webhook;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function delete(WebhookInterface $webhook)
    {
        try {
            $this->webhookResource->delete($webhook);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__("Could not delete webhook"));
        }
        return true;
    }
}

