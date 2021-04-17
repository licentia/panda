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
 * Class Links
 *
 * @package Licentia\Panda\Model
 */
class Links extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected string $_eventPrefix = 'panda_links';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected string $_eventObject = 'links';

    /**
     * @var ResourceModel\Links\CollectionFactory
     */
    protected ResourceModel\Links\CollectionFactory $linksCollection;

    /**
     * @var \Magento\Cms\Helper\Data
     */
    protected $filterProvider;

    /**
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Cms\Model\Template\FilterProvider                   $filterProvider
     * @param ResourceModel\Links\CollectionFactory                        $linksCollection
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        ResourceModel\Links\CollectionFactory $linksCollection,
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
        $this->linksCollection = $linksCollection;
        $this->filterProvider = $filterProvider;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Links::class);
    }

    /**
     *
     * @param $campaignId
     *
     * @return array
     */
    public function getHashForCampaign($campaignId)
    {

        $return = $this->linksCollection->create()
                                        ->addFieldToFilter('campaign_id', $campaignId)
                                        ->setOrder('link', 'asc');

        $info = [];

        foreach ($return as $item) {
            $info[$item->getId()] = $item->getLink();
        }

        return $info;
    }

    /**
     *
     * @param $campaign
     *
     * @return array
     * @throws \Exception
     */
    public function getLinksInCampaign($campaign)
    {

        $temp = [];

        $message = $this->filterProvider->getBlockFilter()->filter($campaign->getMessage());

        $doc = new \DOMDocument();
        $doc->loadHTML($message);

        foreach ($doc->getElementsByTagName('a') as $link) {
            $temp[] = $link->getAttribute('href');
        }

        return $temp;
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
     * @param $link
     *
     * @return $this
     */
    public function setLink($link)
    {

        return $this->setData('link', $link);
    }

    /**
     * @param $clicks
     *
     * @return $this
     */
    public function setClicks($clicks)
    {

        return $this->setData('clicks', $clicks);
    }

    /**
     * @param $conversionsNumber
     *
     * @return $this
     */
    public function setConversionsNumber($conversionsNumber)
    {

        return $this->setData('conversions_number', $conversionsNumber);
    }

    /**
     * @param $conversionsAmount
     *
     * @return $this
     */
    public function setConversionsAmount($conversionsAmount)
    {

        return $this->setData('conversions_amount', $conversionsAmount);
    }

    /**
     * @param $conversionsAverage
     *
     * @return $this
     */
    public function setConversionsAverage($conversionsAverage)
    {

        return $this->setData('conversions_average', $conversionsAverage);
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
    public function getLink()
    {

        return $this->getData('link');
    }

    /**
     * @return mixed
     */
    public function getClicks()
    {

        return $this->getData('clicks');
    }

    /**
     * @return mixed
     */
    public function getConversionsNumber()
    {

        return $this->getData('conversions_number');
    }

    /**
     * @return mixed
     */
    public function getConversionsAmount()
    {

        return $this->getData('conversions_amount');
    }

    /**
     * @return mixed
     */
    public function getConversionsAverage()
    {

        return $this->getData('conversions_average');
    }
}
