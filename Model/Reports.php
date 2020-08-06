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
 * Class Reports
 *
 * @package Licentia\Panda\Model
 */
class Reports extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_reports';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'reports';

    /**
     * @var ResourceModel\Reports\CollectionFactory
     */
    protected $reportsCollection;

    /**
     * @var Campaigns
     */
    protected $campaignsFactory;

    /**
     * @var ResourceModel\Campaigns\CollectionFactory
     */
    protected $campaignsCollection;

    /**
     * @var ResourceModel\Subscribers\CollectionFactory
     */
    protected $subscriberCollection;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * Reports constructor.
     *
     * @param \Licentia\Panda\Helper\Data                                  $pandaHelper
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param CampaignsFactory                                             $campaignsFactory
     * @param ResourceModel\Campaigns\CollectionFactory                    $campaignsCollection
     * @param ResourceModel\Subscribers\CollectionFactory                  $subscriberCollection
     * @param ResourceModel\Reports\CollectionFactory                      $reportsCollection
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CampaignsFactory $campaignsFactory,
        ResourceModel\Campaigns\CollectionFactory $campaignsCollection,
        ResourceModel\Subscribers\CollectionFactory $subscriberCollection,
        ResourceModel\Reports\CollectionFactory $reportsCollection,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->pandaHelper = $pandaHelper;
        $this->campaignsCollection = $campaignsCollection;
        $this->campaignsFactory = $campaignsFactory;
        $this->reportsCollection = $reportsCollection;
        $this->subscriberCollection = $subscriberCollection;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Reports::class);
    }

    /**
     * @return bool|void
     */
    public function cron()
    {

        $yesterday = new \DateTime($this->pandaHelper->gmtDate());
        $yesterday->sub(new \DateInterval('P1D'));
        $date = $yesterday->format('Y-m-d');

        $alreadyRun = $this->reportsCollection->create()->addFieldToFilter('created_at', $date);

        if ($alreadyRun->getSize() > 0) {
            return;
        }

        $prev = $this->reportsCollection->create()
                                        ->addFieldToFilter('created_at', ['lt' => $date])
                                        ->setOrder('created_at', 'DESC')
                                        ->setPageSize(1);

        if ($prev->count() == 1) {
            /** @var Reports $previous */
            $previous = $prev->getFirstItem();
        } else {
            /** @var Reports $previous */
            $previous = new \Magento\Framework\DataObject;
        }

        $campaigns = $this->campaignsCollection->create()
                                               ->addFieldToSelect('campaign_id')
                                               ->getSize();

        $collection = $this->campaignsCollection->create();
        $collection->getSelect()
                   ->reset('columns')
                   ->columns(['total' => new \Zend_Db_Expr('SUM(main_table.conversions_number)')]);
        $conversionsNumber = $collection->getFirstItem()->getData('total');

        $collection = $this->campaignsCollection->create();
        $collection->getSelect()
                   ->reset('columns')
                   ->columns(['total' => new \Zend_Db_Expr('SUM(main_table.conversions_amount)')]);
        $conversionsAmount = $collection->getFirstItem()->getData('total');

        $collection = $this->campaignsCollection->create();
        $collection->getSelect()
                   ->reset('columns')
                   ->columns(['total' => new \Zend_Db_Expr('SUM(views)')]);
        $views = $collection->getFirstItem()->getData('total');

        $collection = $this->campaignsCollection->create();
        $collection->getSelect()
                   ->reset('columns')
                   ->columns(['total' => new \Zend_Db_Expr('SUM(clicks)')]);
        $clicks = $collection->getFirstItem()->getData('total');

        $collection = $this->subscriberCollection->create();
        $collection->getSelect()
                   ->reset('columns')
                   ->columns(['total' => new \Zend_Db_Expr('COUNT(subscriber_id)')]);
        $subscribers = $collection->getFirstItem()->getData('total');

        $data = [];
        $data['created_at'] = $date;
        $data['subscribers'] = (int) $subscribers;
        $data['subscribers_variation'] = (int) $subscribers - $previous->getData('subscribers');
        $data['clicks'] = (int) $clicks;
        $data['clicks_variation'] = (int) $clicks - $previous->getData('clicks');
        $data['views'] = (int) $views;
        $data['views_variation'] = (int) $views - $previous->getData('views');
        $data['conversions_number'] = (int) $conversionsNumber;
        $data['conversions_number_variation'] = (int) $conversionsNumber - $previous->getData('conversions_number');
        $data['conversions_amount'] = (int) $conversionsAmount;
        $data['conversions_amount_variation'] = (int) $conversionsAmount - $previous->getData('conversions_amount');
        $data['campaigns'] = (int) $campaigns;
        $data['campaigns_variation'] = (int) $campaigns - $previous->getData('campaigns');
        try {
            $this->setData($data)->save();
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }

        return true;
    }
}
