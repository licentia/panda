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
 * Class Conversionstmp
 *
 * @package Licentia\Panda\Model
 */
class Conversionstmp extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_conversionstmp';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'conversionstmp';

    /**
     * @var CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * @var SubscribersFactory
     */
    protected $subscribersFactory;

    /**
     * @var ResourceModel\Links\CollectionFactory
     */
    protected $linksCollection;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $pandaSession;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $dateFactory;

    /**
     * Conversionstmp constructor.
     *
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory           $dateFactory
     * @param \Magento\Framework\Model\Context                             $context
     * @param Session                                                      $session
     * @param \Magento\Framework\Registry                                  $registry
     * @param CampaignsFactory                                             $campaignsFactory
     * @param SubscribersFactory                                           $subscribersFactory
     * @param ResourceModel\Links\CollectionFactory                        $linksCollection
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Magento\Framework\Model\Context $context,
        Session $session,
        \Magento\Framework\Registry $registry,
        CampaignsFactory $campaignsFactory,
        SubscribersFactory $subscribersFactory,
        ResourceModel\Links\CollectionFactory $linksCollection,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->pandaSession = $session;
        $this->campaignsFactory = $campaignsFactory;
        $this->subscribersFactory = $subscribersFactory;
        $this->linksCollection = $linksCollection;
        $this->dateFactory = $dateFactory;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Conversionstmp::class);
    }

    /**
     *
     * @param $event
     *
     * @return boolean
     * @throws \Exception
     */
    public function afterOrder($event)
    {

        $session = $this->pandaSession;

        if (!$session->getPandaConversion()) {
            return false;
        }
        /** @var Campaigns $campaign */
        $campaign = $this->campaignsFactory->create()->load($session->getPandaConversionCampaign());

        /** @var Subscribers $subscriber */
        $subscriber = $this->subscribersFactory->create()->load($session->getPandaConversionSubscriber());

        if (!$campaign->getId() || !$subscriber->getId()) {
            return false;
        }

        /** @var  \Magento\Sales\Model\Order $order */
        $order = $event->getEvent()->getOrder();

        if ($order->getCustomerEmail() != $subscriber->getEmail()) {
            return false;
        }

        $cLinks = $this->linksCollection->create()
                                        ->addFieldToFilter('campaign_id', $campaign->getId())
                                        ->addFieldToFilter('link', $session->getPandaConversionUrl());

        if ($cLinks->count() == 1) {
            $link = $cLinks->getFirstItem();
        } else {
            $link = new \Magento\Framework\DataObject;
        }

        $data = [];
        $data['campaign_id'] = $campaign->getId();
        $data['subscriber_id'] = $subscriber->getId();
        $data['order_id'] = $order->getId();
        $data['link_id'] = $link->getId();

        $subscriber->setLastConversionAt($this->dateFactory->create()->gmtDate())
                   ->setLastConversionCampaignId($campaign->getId())
                   ->save();

        $this->setData($data)
             ->save();

        return true;
    }
}
