<?php

/**
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
