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
 * @modified   03/06/20, 16:18 GMT
 *
 */

namespace Licentia\Panda\Model;

/**
 * Class History
 *
 * @package Licentia\Panda\Model
 */
class History extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_messages_history';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'history';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\History::class);
    }

    /**
     * @param $historyId
     *
     * @return $this
     */
    public function setHistoryId($historyId)
    {

        return $this->setData('history_id', $historyId);
    }

    /**
     * @param $campaignId
     *
     * @return $this
     */
    public function setCampaignId($campaignId)
    {

        return $this->setData('campaign_id', $campaignId);
    }

    /**
     * @param $subscriberId
     *
     * @return $this
     */
    public function setSubscriberId($subscriberId)
    {

        return $this->setData('subscriber_id', $subscriberId);
    }

    /**
     * @return mixed
     */
    public function getHistoryId()
    {

        return $this->getData('history_id');
    }

    /**
     * @return mixed
     */
    public function getCampaignId()
    {

        return $this->getData('campaign_id');
    }

    /**
     * @return mixed
     */
    public function getSubscriberId()
    {

        return $this->getData('subscriber_id');
    }
}
