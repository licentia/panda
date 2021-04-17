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

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Stats
 *
 * @package Licentia\Panda\Model
 */
class Stats extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * @var AutorespondersFactory
     */
    protected AutorespondersFactory $autorespondersFactory;

    /**
     * @var StatsFactory
     */
    protected StatsFactory $statsFactory;

    /**
     * @var SplitsFactory
     */
    protected SplitsFactory $splitsFactory;

    /**
     * @var CampaignsFactory
     */
    protected CampaignsFactory $campaignsFactory;

    /**
     * @var ResourceModel\Stats\CollectionFactory
     */
    protected ResourceModel\Stats\CollectionFactory $statsCollection;

    /**
     * @var ResourceModel\Campaigns\CollectionFactory
     */
    protected ResourceModel\Campaigns\CollectionFactory $campaignsCollection;

    /**
     * @var ResourceModel\Subscribers\CollectionFactory
     */
    protected ResourceModel\Subscribers\CollectionFactory $subscribersCollection;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected \Magento\Customer\Model\Session $customerSession;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected \Licentia\Panda\Helper\Data $pandaHelper;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected \Magento\Framework\Filesystem $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected \Magento\Framework\Filesystem\Directory\WriteInterface $tmpDir;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Stats::class);
    }

    /**
     * @param \Licentia\Panda\Helper\Data                                  $pandaHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scope
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\Filesystem                                $filesystem
     * @param AutorespondersFactory                                        $autorespondersFactory
     * @param StatsFactory                                                 $statsFactory
     * @param SplitsFactory                                                $splitsFactory
     * @param CampaignsFactory                                             $campaignsFactory
     * @param \Magento\Customer\Model\Session                              $customerSession
     * @param ResourceModel\Stats\CollectionFactory                        $statsCollection
     * @param ResourceModel\Campaigns\CollectionFactory                    $campaignsCollection
     * @param ResourceModel\Subscribers\CollectionFactory                  $subscribersCollection
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scope,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Filesystem $filesystem,
        AutorespondersFactory $autorespondersFactory,
        StatsFactory $statsFactory,
        SplitsFactory $splitsFactory,
        CampaignsFactory $campaignsFactory,
        \Magento\Customer\Model\Session $customerSession,
        ResourceModel\Stats\CollectionFactory $statsCollection,
        ResourceModel\Campaigns\CollectionFactory $campaignsCollection,
        ResourceModel\Subscribers\CollectionFactory $subscribersCollection,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->scopeConfig = $scope;
        $this->autorespondersFactory = $autorespondersFactory;
        $this->statsFactory = $statsFactory;
        $this->splitsFactory = $splitsFactory;
        $this->campaignsFactory = $campaignsFactory;
        $this->statsCollection = $statsCollection;
        $this->subscribersCollection = $subscribersCollection;
        $this->campaignsCollection = $campaignsCollection;
        $this->customerSession = $customerSession;
        $this->pandaHelper = $pandaHelper;
        $this->filesystem = $filesystem;

        $this->tmpDir = $this->filesystem->getDirectoryWrite(DirectoryList::TMP);
    }

    /**
     *
     * @param mixed $campaign
     * @param mixed $subscriber
     *
     * @return bool|void
     * @throws \Exception
     */
    public function logViews($campaign, $subscriber)
    {

        if (!$campaign->getId() || !$subscriber->getId()) {
            return;
        }

        $first = $this->statsCollection->create()
                                       ->addFieldToFilter('type', 'views')
                                       ->addFieldToFilter('campaign_id', $campaign->getId())
                                       ->addFieldToFilter('subscriber_id', $subscriber->getId());

        if ($first->getSize() == 0) {
            $this->autorespondersFactory->create()->newView($subscriber, $campaign);
        }

        return $this->logData('views', $campaign, $subscriber);
    }

    /**
     * @param Campaigns   $campaign
     * @param Subscribers $subscriber
     *
     * @return bool
     * @throws \Exception
     */
    public function logClicks(Campaigns $campaign, Subscribers $subscriber)
    {

        if (!$campaign->getId() || !$subscriber->getId()) {
            return false;
        }

        $session = $this->customerSession;

        if ($session->getData('panda_' . $campaign->getId() . '_click') == true) {
            return false;
        }

        $this->autorespondersFactory->create()->newClick($subscriber, $campaign);

        $isUniqueCollection = $this->statsCollection->create()
                                                    ->addFieldToSelect('subscriber_id')
                                                    ->addFieldToFilter('subscriber_id', $subscriber->getId())
                                                    ->addFieldToFilter('campaign_id', $campaign->getId())
                                                    ->addFieldToFilter('type', 'views')
                                                    ->setPageSize(1);

        if ($isUniqueCollection->count() != 0) {
            $isUnique = false;
        } else {
            $isUnique = true;
            $subscriber->setLastMessageClickAt($this->pandaHelper->gmtDate());
        }

        if ($isUnique) {
            $this->logViews($campaign, $subscriber);
        }

        $subscriber->setLastClickCampaignId($campaign->getId())
                   ->save();

        return $this->logData('clicks', $campaign, $subscriber);
    }

    /**
     * @param                                   $type
     * @param Campaigns                         $campaign
     * @param Subscribers                       $subscriber
     *
     * @return bool
     * @throws \Exception
     */
    protected function logData(
        $type,
        Campaigns $campaign,
        Subscribers $subscriber
    ) {

        $isUniqueCollection = $this->statsCollection->create()
                                                    ->addFieldToSelect('subscriber_id')
                                                    ->addFieldToFilter('subscriber_id', $subscriber->getId())
                                                    ->addFieldToFilter('campaign_id', $campaign->getId())
                                                    ->addFieldToFilter('type', $type)
                                                    ->setPageSize(1);

        if ($isUniqueCollection->getSize() != 0) {
            $isUnique = false;
        } else {
            $isUnique = true;
        }

        $bResult = \Licentia\Panda\Helper\Data::parseUserAgent();

        $ip = $_SERVER['REMOTE_ADDR'];
        $date = $this->pandaHelper->gmtDate();

        $data = [];

        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            if (isset($data['success'])) {
                $data['country_code'] = $this->pandaHelper->getCountryCode($ip);
            }
        }

        $data['campaign_id'] = $campaign->getId();
        $data['subscriber_id'] = $subscriber->getId();
        $data['customer_id'] = $subscriber->getCustomerId();
        $data['event_at'] = $date;
        $data['type'] = $type;
        $data['ip'] = $ip;

        if ($bResult) {
            $data['platform'] = $bResult['platform'];
            $data['browser'] = $bResult['browser'];
            $data['version'] = $bResult['version'];
        }

        $this->statsFactory->create()
                           ->setData($data)
                           ->save();

        $uniqueDataType = 'unique_' . $type;

        $updateData = [];
        $updateData[$type] = new \Zend_Db_Expr($type . ' + 1');

        $campaign->setData($type, new \Zend_Db_Expr($type . ' + 1 '));

        if ($isUnique) {
            $campaign->setData($uniqueDataType, new \Zend_Db_Expr($uniqueDataType . ' + 1 '));
        }

        $campaign->save();

        if ($campaign->getParentId()) {
            $parent = $this->campaignsFactory->create()->load($campaign->getParentId());
            if ($parent->getId()) {
                $parent->setData($type, new \Zend_Db_Expr($parent->getData($type) . ' + 1 '));
                if ($isUnique) {
                    $parent->setData(
                        'unique_' . $type,
                        new \Zend_Db_Expr($parent->getData('unique_' . $type) . ' + 1 ')
                    );
                }
                $parent->save();
            }
        }

        if ($campaign->getAutoresponderId()) {
            $autoresponder =
                $this->autorespondersFactory->create()->load($campaign->getAutoresponderId());

            if ($autoresponder->getId()) {
                $autoresponder->setData($type, new \Zend_Db_Expr($type . ' + 1 '));
                if ($isUnique) {
                    $autoresponder->setData($uniqueDataType, new \Zend_Db_Expr($uniqueDataType . ' + 1 '));
                }
                $autoresponder->save();
            }
        }

        if ($campaign->getSplitId() && ($isUnique || $type == 'clicks')) {
            $split = $this->splitsFactory->create()->load($campaign->getSplitId());
            if ($split->getId()) {
                $split->setData(
                    $type . '_' . $campaign->getSplitVersion(),
                    new \Zend_Db_Expr($split->getData($type . '_' . $campaign->getSplitVersion()) . ' + 1')
                );
                $split->save();
            }
        }

        if ($type == 'views') {
            $subscriber->setLastOpenCampaignId($campaign->getId());
            $subscriber->setLastMessageOpenAt($this->pandaHelper->gmtDate());
        } else {
            $subscriber->setLastClickCampaignId($campaign->getId());
            $subscriber->setLastMessageClickAt($this->pandaHelper->gmtDate());
        }

        if ($isUnique) {
            $subscriber->setData($type, $subscriber->getData($type) + 1);
        }

        $subscriber->save();

        return true;
    }

    /**
     * @return mixed
     */
    public function loadCampaign()
    {

        return $this->statsCollection->create();
    }

    /**
     * @throws \Exception
     */
    public function randomStats()
    {

        $campaigns = $this->campaignsCollection->create()->getAllIds();
        $subscribers = $this->subscribersCollection->create()->getAllIds();
        $locale = new \Zend_Locale('en_US');
        $countries = $locale->getTranslationList('Territory', 'en', 2);
        asort($countries, SORT_LOCALE_STRING);

        $platform = explode('|', 'Android|CrOS|Tizen|iPhone|iPad|iPod|Linux|Macintosh|Windows');
        $browser = explode('|', 'Chrome|Edge|Safari|BlackBerry|Internet|Firefox');

        $types = [0 => 'views', 1 => 'clicks'];

        for ($a = 0; $a <= 1000000; $a++) {
            $data = [];
            $data['campaign_id'] = $campaigns[array_rand($campaigns)];
            $data['subscriber_id'] = $subscribers[array_rand($subscribers)];
            $data['event_at'] = new \Zend_Db_Expr('NOW() - INTERVAL FLOOR(RAND() * 120) DAY');
            $data['type'] = $types[array_rand($types)];
            $data['country'] = $countries[array_rand($countries)];
            $data['browser'] = $browser[array_rand($browser)];
            $data['platform'] = $platform[array_rand($platform)];
            $data['version'] = rand(1, 60);

            $this->setData($data)
                 ->save();
        }
    }
}
