<?php
/*
 * Copyright (C) Licentia, Unipessoal LDA
 *
 * NOTICE OF LICENSE
 *
 *  This source file is subject to the EULA
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  https://www.greenflyingpanda.com/panda-license.txt
 *
 *  @title      Licentia Panda - Magento® Sales Automation Extension
 *  @package    Licentia
 *  @author     Bento Vilas Boas <bento@licentia.pt>
 *  @copyright  Copyright (c) Licentia - https://licentia.pt
 *  @license    https://www.greenflyingpanda.com/panda-license.txt
 *
 */

namespace Licentia\Panda\Model;

use Licentia\Panda\Api\Data\SubscribersInterfaceFactory;
use Licentia\Panda\Api\Data\SubscribersSearchResultsInterfaceFactory;
use Licentia\Panda\Api\SubscribersRepositoryInterface;
use Licentia\Panda\Model\ResourceModel\Subscribers as ResourceSubscribers;
use Licentia\Panda\Model\ResourceModel\Subscribers\CollectionFactory as SubscribersCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class SubscribersRepository
 *
 * @package Licentia\Panda\Model
 */
class SubscribersRepository implements SubscribersRepositoryInterface
{

    /**
     * @var
     */
    protected $subscribersFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var
     */
    protected $subscribersCollectionFactory;

    /**
     * @var SubscribersSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResourceSubscribers
     */
    protected $resource;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var SubscribersInterfaceFactory
     */
    protected $dataSubscribersFactory;

    /**
     * @param ResourceSubscribers                      $resource
     * @param SubscribersFactory                       $subscribersFactory
     * @param SubscribersInterfaceFactory              $dataSubscribersFactory
     * @param SubscribersCollectionFactory             $subscribersCollectionFactory
     * @param SubscribersSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper                         $dataObjectHelper
     * @param DataObjectProcessor                      $dataObjectProcessor
     * @param StoreManagerInterface                    $storeManager
     */
    public function __construct(
        ResourceSubscribers $resource,
        SubscribersFactory $subscribersFactory,
        SubscribersInterfaceFactory $dataSubscribersFactory,
        SubscribersCollectionFactory $subscribersCollectionFactory,
        SubscribersSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {

        $this->resource = $resource;
        $this->subscribersFactory = $subscribersFactory;
        $this->subscribersCollectionFactory = $subscribersCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataSubscribersFactory = $dataSubscribersFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Licentia\Panda\Api\Data\SubscribersInterface $subscriber
    ) {

        if (empty($subscriber->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $subscriber->setStoreId($storeId);
        }
        try {
            $this->resource->save($subscriber);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the subscribers: %1',
                    $exception->getMessage()
                )
            );
        }

        return $subscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function me($customerId)
    {

        $subscribers = $this->subscribersFactory->create();

        return $subscribers->loadByCustomerId($customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function meStatus($customerId)
    {

        $subscriber = $this->subscribersFactory->create()->loadByCustomerId($customerId);

        if (!$subscriber->getId()) {
            return 0;
        }

        return $subscriber->getStatus();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($subscriberId)
    {

        $subscribers = $this->subscribersFactory->create();
        $subscribers->load($subscriberId);
        if (!$subscribers->getId()) {
            throw new NoSuchEntityException(__('Subscribers with id "%1" does not exist.', $subscriberId));
        }

        return $subscribers;
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe($email)
    {

        return $this->subscribersFactory->create()->subscribe($email);
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function unsubscribe($email)
    {

        return $this->subscribersFactory->create()->unsubscribe($email);
    }

    /**
     * @return array
     */
    public function status()
    {

        return [
            [Subscribers::STATUS_SUBSCRIBED => 'Subscribed'],
            [Subscribers::STATUS_NOT_ACTIVE => 'Not Active'],
            [Subscribers::STATUS_UNCONFIRMED => 'Unconfirmed'],
            [Subscribers::STATUS_UNSUBSCRIBED => 'Unsubscribed'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->subscribersCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $items = [];

        foreach ($collection as $subscribersModel) {
            $subscribersData = $this->dataSubscribersFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $subscribersData,
                $subscribersModel->getData(),
                \Licentia\Panda\Api\Data\SubscribersInterface::class
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $subscribersData,
                \Licentia\Panda\Api\Data\SubscribersInterface::class
            );
        }
        $searchResults->setItems($items);

        return $searchResults;
    }

    /**
     * @param \Licentia\Panda\Api\Data\SubscribersInterface $subscriber
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(
        \Licentia\Panda\Api\Data\SubscribersInterface $subscriber
    ) {

        try {
            $this->resource->delete($subscriber);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete the Subscriber: %1', $exception->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($subscriberId)
    {

        return $this->delete($this->getById($subscriberId));
    }
}
