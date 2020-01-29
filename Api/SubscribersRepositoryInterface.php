<?php
/**
 * Copyright (C) 2020 Licentia, Unipessoal LDA
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @title      Licentia Panda - Magento® Sales Automation Extension
 * @package    Licentia
 * @author     Bento Vilas Boas <bento@licentia.pt>
 * @copyright  Copyright (c) Licentia - https://licentia.pt
 * @license    GNU General Public License V3
 * @modified   29/01/20, 15:22 GMT
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
