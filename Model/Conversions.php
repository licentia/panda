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
 * Class Conversions
 *
 * @package Licentia\Panda\Model
 */
class Conversions extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_conversions';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'conversions';

    /**
     * @var ConversionstmpFactory
     */
    protected $conversionstmpCollection;

    /**
     * @var CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * @var SubscribersFactory
     */
    protected $subscribersFactory;

    /**
     * @var LinksFactory
     */
    protected $linksFactory;

    /**
     * @var AutorespondersFactory
     */
    protected $autorespondersFactory;

    /**
     * @var SplitsFactory
     */
    protected $splitsFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $dateFactory;

    /**
     * @var TagsFactory
     */
    protected $tagsFactory;

    /**
     * Conversions constructor.
     *
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory           $dateFactory
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param ResourceModel\Conversionstmp\CollectionFactory               $conversionstmpCollection
     * @param LinksFactory                                                 $linksFactory
     * @param TagsFactory                                                  $tagsFactory
     * @param SubscribersFactory                                           $subscribersFactory
     * @param CampaignsFactory                                             $campaignsFactory
     * @param AutorespondersFactory                                        $autorespondersFactory
     * @param SplitsFactory                                                $splitsFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceModel\Conversionstmp\CollectionFactory $conversionstmpCollection,
        LinksFactory $linksFactory,
        TagsFactory $tagsFactory,
        SubscribersFactory $subscribersFactory,
        CampaignsFactory $campaignsFactory,
        AutorespondersFactory $autorespondersFactory,
        SplitsFactory $splitsFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );

        $this->tagsFactory = $tagsFactory;
        $this->conversionstmpCollection = $conversionstmpCollection;
        $this->subscribersFactory = $subscribersFactory;
        $this->splitsFactory = $splitsFactory;
        $this->autorespondersFactory = $autorespondersFactory;
        $this->linksFactory = $linksFactory;
        $this->campaignsFactory = $campaignsFactory;
        $this->dateFactory = $dateFactory;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Conversions::class);
    }

    /**
     *
     * @param $event
     *
     * @return boolean
     * @throws \Exception
     */
    public function convertOrder($event)
    {

        /** @var  \Magento\Sales\Model\Order $order */
        $order = $event->getEvent()->getOrder();

        $orderId = $order->getId();

        $convTemp = $this->conversionstmpCollection->create()->addFieldToFilter('order_id', $orderId);

        if ($convTemp->count() != 1) {
            return false;
        }

        $conversion = $convTemp->getFirstItem();
        /** @var Campaigns $campaign */
        $campaign = $this->campaignsFactory->create()->load($conversion->getCampaignId());

        /** @var Subscribers $subscriber */
        $subscriber = $this->subscribersFactory->create()->load($conversion->getSubscriberId());

        /** @var Links $link */
        $link = $this->linksFactory->create()->load($conversion->getLinkId());

        $conversion->delete();

        if (!$campaign->getId() || !$subscriber->getId()) {
            return false;
        }

        $data = [];
        $data['campaign_id'] = $campaign->getId();
        $data['campaign_name'] = $campaign->getInternalName();
        $data['subscriber_id'] = $subscriber->getId();
        $data['subscriber_email'] = $subscriber->getEmail();
        $data['subscriber_firstname'] = $subscriber->getFirstname();
        $data['subscriber_lastname'] = $subscriber->getLastname();
        $data['order_date'] = $order->getCreatedAt();
        $data['order_id'] = $order->getId();
        $data['order_amount'] = $order->getBaseGrandTotal();
        $data['customer_id'] = $order->getCustomerId();
        $data['link_id'] = $link->getId();
        $data['created_at'] = $this->dateFactory->create()->gmtDate();

        $this->setData($data)
             ->save();

        $campaign->setConversionsNumber($campaign->getConversionsNumber() + 1);
        $campaign->setConversionsAmount($campaign->getConversionsAmount() + $data['order_amount']);
        $campaign->setConversionsAverage($campaign->getConversionsAmount() / $campaign->getConversionsNumber());
        $campaign->save();

        if ($campaign->getParentId()) {
            $parent = $this->campaignsFactory->create()->load($campaign->getParentId());

            if ($parent->getId()) {
                $parent->setConversionsNumber($parent->getConversionsNumber() + 1);
                $parent->setConversionsAmount($parent->getConversionsAmount() + $data['order_amount']);
                $parent->setConversionsAverage($parent->getConversionsAmount() / $parent->getConversionsNumber());
                $parent->save();
            }
        }

        if ($campaign->getAutoresponderId()) {

            /** @var Autoresponders $autoresponder */
            $autoresponder = $this->autorespondersFactory->create()->load($campaign->getAutoresponderId());

            if ($autoresponder->getId()) {
                $autoresponder->setConversionsNumber($autoresponder->getConversionsNumber() + 1);
                $autoresponder->setConversionsAmount(
                    $autoresponder->getConversionsAmount() + $data['order_amount']
                );
                $autoresponder->setConversionsAverage(
                    round($autoresponder->getConversionsAmount() / $autoresponder->getConversionsNumber(), 2)
                );
                $autoresponder->save();
            }
        }

        if ($campaign->getTags() && is_array($campaign->getTags())) {

            /** @var Tags $tag */
            foreach ($campaign->getTags() as $tag) {
                $tagModel = $this->tagsFactory->create()->load($tag['value']);

                if ($tagModel->getId()) {
                    $tagModel->setConversionsNumber($tagModel->getConversionsNumber() + 1);
                    $tagModel->setConversionsNumber($tagModel->getConversionsNumber() + $data['order_amount']);
                    $tagModel->setConversionsAverage(
                        round($tagModel->getConversionsAmount() / $tagModel->getConversionsNumber(), 2)
                    );
                    $tagModel->save();
                }
            }
        }

        if ($campaign->getSplitId()) {

            /** @var Splits $split */
            $split = $this->splitsFactory->create()->load($campaign->getSplitId());

            if ($split->getId()) {
                $split->setData(
                    'conversions_' . $campaign->getSplitVersion(),
                    $split->getData('conversions_' . $campaign->getSplitVersion()) + 1
                );
                $split->save();
            }
        }

        $subscriber->setLastConversionAt($this->dateFactory->create()->gmtDate());
        $subscriber->setLastConversionCampaignId($campaign->getId());
        $subscriber->setConversionsNumber($subscriber->getConversionsNumber() + 1);
        $subscriber->setConversionsAmount($subscriber->getConversionsAmount() + $data['order_amount']);
        $subscriber->setConversionsAverage(
            round($subscriber->getConversionsAmount() / $subscriber->getConversionsNumber(), 2)
        );
        $subscriber->save();

        if ($link->getId()) {
            $link->setConversionsNumber($link->getConversionsNumber() + 1);
            $link->setConversionsAmount($link->getConversionsAmount() + $data['order_amount']);
            $link->setConversionsAverage(
                round($link->getConversionsAmount() / $link->getConversionsNumber(), 2)
            );

            $link->save();
        }

        return true;
    }

    /**
     * @param $conversionId
     *
     * @return $this
     */
    public function setConversionId($conversionId)
    {

        return $this->setData('conversion_id', $conversionId);
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
     * @param $linkId
     *
     * @return $this
     */
    public function setLinkId($linkId)
    {

        return $this->setData('link_id', $linkId);
    }

    /**
     * @param $orderId
     *
     * @return $this
     */
    public function setOrderId($orderId)
    {

        return $this->setData('order_id', $orderId);
    }

    /**
     * @param $orderDate
     *
     * @return $this
     */
    public function setOrderDate($orderDate)
    {

        return $this->setData('order_date', $orderDate);
    }

    /**
     * @param $orderAmount
     *
     * @return $this
     */
    public function setOrderAmount($orderAmount)
    {

        return $this->setData('order_amount', $orderAmount);
    }

    /**
     * @param $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {

        return $this->setData('customer_id', $customerId);
    }

    /**
     * @param $subscriberEmail
     *
     * @return $this
     */
    public function setSubscriberEmail($subscriberEmail)
    {

        return $this->setData('subscriber_email', $subscriberEmail);
    }

    /**
     * @param $subscriberFirstname
     *
     * @return $this
     */
    public function setSubscriberFirstname($subscriberFirstname)
    {

        return $this->setData('subscriber_firstname', $subscriberFirstname);
    }

    /**
     * @param $subscriberLastname
     *
     * @return $this
     */
    public function setSubscriberLastname($subscriberLastname)
    {

        return $this->setData('subscriber_lastname', $subscriberLastname);
    }

    /**
     * @param $campaignName
     *
     * @return $this
     */
    public function setCampaignName($campaignName)
    {

        return $this->setData('campaign_name', $campaignName);
    }

    /**
     * @param $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {

        return $this->setData('created_at', $createdAt);
    }

    /**
     * @return mixed
     */
    public function getConversionId()
    {

        return $this->getData('conversion_id');
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

    /**
     * @return mixed
     */
    public function getLinkId()
    {

        return $this->getData('link_id');
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {

        return $this->getData('order_id');
    }

    /**
     * @return mixed
     */
    public function getOrderDate()
    {

        return $this->getData('order_date');
    }

    /**
     * @return mixed
     */
    public function getOrderAmount()
    {

        return $this->getData('order_amount');
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {

        return $this->getData('customer_id');
    }

    /**
     * @return mixed
     */
    public function getSubscriberEmail()
    {

        return $this->getData('subscriber_email');
    }

    /**
     * @return mixed
     */
    public function getSubscriberFirstname()
    {

        return $this->getData('subscriber_firstname');
    }

    /**
     * @return mixed
     */
    public function getSubscriberLastname()
    {

        return $this->getData('subscriber_lastname');
    }

    /**
     * @return mixed
     */
    public function getCampaignName()
    {

        return $this->getData('campaign_name');
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {

        return $this->getData('created_at');
    }
}
