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

namespace Licentia\Panda\Api;

/**
 * Interface SubscribersRepositoryInterface
 *
 * @package Licentia\Panda\Api
 */
interface SubscribersRepositoryInterface
{

    /**
     * Save Subscribers
     *
     * @param \Licentia\Panda\Api\Data\SubscribersInterface $subscriber
     *
     * @return \Licentia\Panda\Api\Data\SubscribersInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function save(
        \Licentia\Panda\Api\Data\SubscribersInterface $subscriber
    );

    /**
     * Retrieve Subscriber
     *
     * @param string $customerId
     *
     * @return \Licentia\Panda\Api\Data\SubscribersInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function me($customerId);

    /**
     * Retrieve Subscriber Status
     *
     * @param string $customerId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function meStatus($customerId);

    /**
     * Retrieve Subscribers
     *
     * @param string $subscriberId
     *
     * @return \Licentia\Panda\Api\Data\SubscribersInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getById($subscriberId);

    /**
     * Subscribe
     *
     * @param string $email
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function subscribe($email);

    /**
     * Subscribe
     *
     * @param string $email
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function unsubscribe($email);

    /**
     * Possible Subscribers Status
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function status();

    /**
     * Retrieve Subscribers matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Licentia\Panda\Api\Data\SubscribersSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Subscribers
     *
     * @param \Licentia\Panda\Api\Data\SubscribersInterface $subscriber
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function delete(
        \Licentia\Panda\Api\Data\SubscribersInterface $subscriber
    );

    /**
     * Delete Subscribers by ID
     *
     * @param string $subscriberId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function deleteById($subscriberId);
}
