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

/**
 * Class Urls
 *
 * @package Licentia\Panda\Model
 */
class Urls extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_urls';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'urls';

    /**
     * @var ResourceModel\Links\CollectionFactory
     */
    protected $linksCollection;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var LinksFactory
     */
    protected $linksFactory;

    /**
     * @param \Licentia\Panda\Helper\Data                                  $pandaHelper
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param ResourceModel\Links\CollectionFactory                        $linksCollection
     * @param LinksFactory                                                 $linksFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceModel\Links\CollectionFactory $linksCollection,
        LinksFactory $linksFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->linksCollection = $linksCollection;
        $this->linksFactory = $linksFactory;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Urls::class);
    }

    /**
     * @param Campaigns   $campaign
     * @param Subscribers $subscriber
     * @param             $url
     *
     * @return bool|void
     * @throws \Exception
     */
    public function logUrl(
        Campaigns $campaign,
        Subscribers $subscriber,
        $url
    ) {

        $url = rtrim($url, '/');
        $url = rtrim($url, '?');

        if (!$campaign->getId() || !$subscriber->getId()) {
            return;
        }

        $links = $this->linksCollection->create()
                                       ->addFieldToFilter('campaign_id', $campaign->getId())
                                       ->addFieldToFilter('link', $url);

        if ($links->count() > 0) {
            $link = $links->getFirstItem();
            $link->setData('clicks', $link->getData('clicks') + 1)
                 ->save();
        } else {
            $data = [];
            $data['link'] = $url;
            $data['campaign_id'] = $campaign->getId();
            $data['clicks'] = 1;
            $link = $this->linksFactory->create()
                                       ->setData($data)
                                       ->save();
        }

        $data = [];
        $data['campaign_id'] = $campaign->getId();
        $data['subscriber_id'] = $subscriber->getId();
        $data['subscriber_firstname'] = $subscriber->getFirstname();
        $data['subscriber_lastname'] = $subscriber->getLastname();
        $data['subscriber_email'] = $subscriber->getEmail();
        $data['customer_id'] = $subscriber->getCustomerId();
        $data['url'] = $url;
        $data['link_id'] = $link->getId();
        $data['visit_at'] = $this->pandaHelper->gmtDate();
        $this->setData($data)
             ->save();

        return true;
    }

    /**
     * @param $urlId
     *
     * @return $this
     */
    public function setUrlId($urlId)
    {

        return $this->setData('url_id', $urlId);
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
     * @param $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {

        return $this->setData('customer_id', $customerId);
    }

    /**
     * @param $url
     *
     * @return $this
     */
    public function setUrl($url)
    {

        return $this->setData('url', $url);
    }

    /**
     * @param $visitAt
     *
     * @return $this
     */
    public function setVisitAt($visitAt)
    {

        return $this->setData('visit_at', $visitAt);
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
     * @param $subscriberEmail
     *
     * @return $this
     */
    public function setSubscriberEmail($subscriberEmail)
    {

        return $this->setData('subscriber_email', $subscriberEmail);
    }

    /**
     * @param $subscriberCellphone
     *
     * @return $this
     */
    public function setSubscriberCellphone($subscriberCellphone)
    {

        return $this->setData('subscriber_cellphone', $subscriberCellphone);
    }

    /**
     * @return mixed
     */
    public function getUrlId()
    {

        return $this->getData('url_id');
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
    public function getCustomerId()
    {

        return $this->getData('customer_id');
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {

        return $this->getData('url');
    }

    /**
     * @return mixed
     */
    public function getVisitAt()
    {

        return $this->getData('visit_at');
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
    public function getSubscriberEmail()
    {

        return $this->getData('subscriber_email');
    }

    /**
     * @return mixed
     */
    public function getSubscriberCellphone()
    {

        return $this->getData('subscriber_cellphone');
    }
}
