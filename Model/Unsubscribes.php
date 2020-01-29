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

namespace Licentia\Panda\Model;

/**
 * Class Unsubscribes
 *
 * @package Licentia\Panda\Model
 */
class Unsubscribes extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_unsubscribes';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'unsubscribes';

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var ResourceModel\Unsubscribes\CollectionFactory
     */
    protected $unsubscribesCollection;

    /**
     * @param \Licentia\Panda\Helper\Data                                  $pandaHelper
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param ResourceModel\Unsubscribes\CollectionFactory                 $collectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceModel\Unsubscribes\CollectionFactory $collectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->pandaHelper = $pandaHelper;
        $this->unsubscribesCollection = $collectionFactory;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(\Licentia\Panda\Model\ResourceModel\Unsubscribes::class);
    }

    /**
     * Logs unsubscribes
     *
     * @param Campaigns   $campaign
     * @param Subscribers $subscriber
     *
     * @return Unsubscribes
     * @throws \Exception
     */
    public function unsubscribe($campaign, $subscriber)
    {

        $unsubs = $this->unsubscribesCollection->create()
                                               ->addFieldToFilter('campaign_id', $campaign->getId())
                                               ->addFieldToFilter('email', $subscriber->getEmail());

        if ($unsubs->count() == 0) {
            $campaign->setUnsubscribes($campaign->getData('unsubscribes') + 1)
                     ->save();

            $data = [];
            $data['campaign_id'] = $campaign->getId();
            $data['email'] = $subscriber->getEmail();
            $data['cellphone'] = $subscriber->getCellphone();
            $data['unsubscribed_at'] = $this->pandaHelper->gmtDate();
            $this->setData($data)
                 ->save();
        }

        return $this;
    }
}
