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
 * Class Campaigns
 *
 * @package Licentia\Panda\Model
 */
class Campaigns extends \Magento\Rule\Model\AbstractModel
{

    const CAMPAIGN_TYPES = ['email', 'sms', 'callback'];

    const DEFAULT_CAMPAIGN_TYPE = 'email';

    const MYSQL_DATE = 'yyyy-MM-dd';

    const MYSQL_DATETIME = 'yyyy-MM-dd HH:mm:ss';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_campaigns';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'campaigns';

    /**
     * @var
     */
    protected $scopeConfig;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var LinksFactory
     */
    protected $linksFactory;

    /**
     * @var FollowupFactory
     */
    protected $followupFactory;

    /**
     * @var ServiceFactory
     */
    protected $serviceFactory;

    /**
     * @var CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * @var ResourceModel\Archive\CollectionFactory
     */
    protected $archiveCollection;

    /**
     * @var SubscribersFactory
     */
    protected $subscribersCollection;

    /**
     * @var ResourceModel\Links\CollectionFactory
     */
    protected $linksCollection;

    /**
     * @var ResourceModel\Campaigns\CollectionFactory
     */
    protected $campaignsCollection;

    /**
     * @var TagsFactory
     */
    protected $tagsFactory;

    /**
     * @var array
     */
    protected $statsFields = [
        'clicks',
        'unique_clicks',
        'views',
        'unique_views',
        'unsent',
        'sent',
        'total_messages',
        'errors',
        'bounces',
    ];

    /**
     * @var \Licentia\Equity\Model\Segments\Condition\CombineFactory
     */
    protected $conditionsCombine;

    /**
     * @var \Licentia\Equity\Model\Segments\Action\CollectionFactory
     */
    protected $collectionCombine;

    /**
     * Campaigns constructor.
     *
     * @param TagsFactory                                                  $tagsFactory
     * @param \Licentia\Equity\Model\Segments\Condition\CombineFactory     $combineFactory
     * @param \Licentia\Equity\Model\Segments\Action\CollectionFactory     $collectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scope
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param LinksFactory                                                 $linksFactory
     * @param FollowupFactory                                              $followupFactory
     * @param ServiceFactory                                               $serviceFactory
     * @param CampaignsFactory                                             $campaignsFactory
     * @param ResourceModel\Archive\CollectionFactory                      $archiveCollection
     * @param ResourceModel\Subscribers\CollectionFactory                  $subscribersCollection
     * @param ResourceModel\Campaigns\CollectionFactory                    $campaignsCollection
     * @param ResourceModel\Links\CollectionFactory                        $linksCollection
     * @param \Licentia\Panda\Helper\Data                                  $pandaHelper
     * @param \Magento\Framework\Data\FormFactory                          $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface         $localeDate
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        TagsFactory $tagsFactory,
        \Licentia\Equity\Model\Segments\Condition\CombineFactory $combineFactory,
        \Licentia\Equity\Model\Segments\Action\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scope,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        LinksFactory $linksFactory,
        FollowupFactory $followupFactory,
        ServiceFactory $serviceFactory,
        CampaignsFactory $campaignsFactory,
        ResourceModel\Archive\CollectionFactory $archiveCollection,
        ResourceModel\Subscribers\CollectionFactory $subscribersCollection,
        ResourceModel\Campaigns\CollectionFactory $campaignsCollection,
        ResourceModel\Links\CollectionFactory $linksCollection,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);

        $this->tagsFactory = $tagsFactory;
        $this->scopeConfig = $scope;
        $this->pandaHelper = $pandaHelper;
        $this->linksFactory = $linksFactory;
        $this->linksCollection = $linksCollection;
        $this->followupFactory = $followupFactory;
        $this->serviceFactory = $serviceFactory;
        $this->campaignsFactory = $campaignsFactory;
        $this->archiveCollection = $archiveCollection;
        $this->campaignsCollection = $campaignsCollection;
        $this->subscribersCollection = $subscribersCollection;
        $this->collectionCombine = $collectionFactory;
        $this->conditionsCombine = $combineFactory;

        $this->_registry->register('panda_campaign_environment', true, true);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Campaigns::class);
    }

    /**
     * Returns a list of available cron options
     *
     * @return array
     */
    public static function getCronList()
    {

        $list = [
            '0' => __('No'),
            'd' => __('Daily'),
            'w' => __('Weekly'),
            'm' => __('Monthly'),
            'y' => __('Yearly'),
        ];

        return $list;
    }

    /**
     * Returns a list of days
     *
     * @return array
     */
    public static function getDaysList()
    {

        $lista = [
            '0' => __('Sunday'),
            '1' => __('Monday'),
            '2' => __('Tuesday'),
            '3' => __('Wednesday'),
            '4' => __('Thursday'),
            '5' => __('Friday'),
            '6' => __('Saturday'),
        ];

        $list = [];
        foreach ($lista as $key => $value) {
            $list[] = ['value' => $key, 'label' => $value];
        }

        return $list;
    }

    /**
     * returns month list
     *
     * @return array
     */
    public static function getMonthsList()
    {

        $list = [
            '1'  => __('January'),
            '2'  => __('February'),
            '3'  => __('March'),
            '4'  => __('April'),
            '5'  => __('May'),
            '6'  => __('June'),
            '7'  => __('July'),
            '8'  => __('August'),
            '9'  => __('September'),
            '10' => __('October'),
            '11' => __('November'),
            '12' => __('December'),
        ];

        return $list;
    }

    /**
     * Returns a list of hours
     *
     * @return array
     */
    public static function getRunAroundList()
    {

        $return = [];

        for ($i = 0; $i <= 23; $i++) {
            $return[] = ['value' => $i, 'label' => str_pad($i, 2, '0', STR_PAD_LEFT) . ':00'];
        }

        return $return;
    }

    /**
     * Retuns a list of possible days and expressions available to send a campaign.
     * Such expressions include Last day of the month, first Mondat, etcet
     *
     * @return array
     */
    public static function getDaysMonthsList()
    {

        $list = [];

        for ($i = 1; $i <= 31; $i++) {
            $list[] = ['label' => $i, 'value' => $i];
        }

        $final = [];
        $final[] = ['label' => __('Specific Day'), 'value' => $list];

        $days = self::getDaysList();

        $din = [];
        for ($i = 1; $i <= 4; $i++) {
            foreach ($days as $day) {
                $din[] = [
                    'value' => $i . '-' . $day['value'],
                    'label' => __('On the %1 %2 of the month', $i, $day['label']),
                ];
            }
        }

        foreach ($days as $day) {
            $din[] = [
                'value' => '|' . $day['value'],
                'label' => __('On the last %1 of the month', $day['label']),
            ];
        }

        $din[] = ['value' => 'u-u', 'label' => __('Last Day of the Month ')];
        $din[] = ['value' => 'a-u', 'label' => __('First Weekday of the Month ')];
        $din[] = ['value' => 'a2-u', 'label' => __('Second Weekday of the Month ')];
        $din[] = ['value' => 'b-u', 'label' => __('Last Weekday of the Month ')];
        $din[] = ['value' => 'b2-u', 'label' => __('Second to last Weekday of the Month ')];

        $final[] = ['label' => __('Dynamic Day'), 'value' => $din];

        return $final;
    }

    /**
     * @param Campaigns $campaign
     *
     * @return array|Service\ServiceAbstract
     */
    protected function _queueEmail(Campaigns $campaign)
    {

        if ($campaign->getStatus() == 'paused') {
            return [];
        }

        $this->_registry->register('panda_campaign', $campaign, true);

        $service = $this->serviceFactory->create()->getEmailService();

        $data = [];
        $data['campaign'] = $campaign;
        try {
            $this->_eventManager->dispatch(
                'panda_campaign_send_before',
                ['campaign' => $campaign, 'server_data' => $data]
            );

            $result = $service->setData($data)
                              ->buildQueue();
            if ($result->getData('id')) {
                $this->_eventManager->dispatch(
                    'panda_campaign_send_after',
                    ['campaign' => $campaign, 'server_data' => $data]
                );
            }
        } catch (\Exception $e) {
            $result = [];
        }

        return $result;
    }

    /**
     * @return $this
     */
    public function queueCampaigns()
    {

        $now = $this->pandaHelper->gmtDate();

        //Non-Recurring Campaigns
        $collection = $this->campaignsCollection->create()
                                                ->addFieldToFilter('status', ['in' => ['standby', 'queuing']])
                                                ->addFieldToFilter('deploy_at', ['lteq' => $now])
                                                ->addFieldToFilter('recurring', '0')
                                                ->addFieldToFilter('autoresponder_id', 0);

        /** @var Campaigns $campaign */
        foreach ($collection as $campaign) {
            $this->_queueEmail($campaign);
        }

        //Recurring Campaigns
        $collectionRecurring = $this->campaignsCollection->create()
                                                         ->addFieldToFilter('status', ['in' => ['standby', 'queuing']])
                                                         ->addFieldToFilter('recurring_next_run', ['lteq' => $now])
                                                         ->addFieldToFilter('recurring', ['neq' => '0'])
                                                         ->addFieldToFilter('followup_id', ['null' => true])
                                                         ->addFieldToFilter('autoresponder_id', 0);

        /** @var Campaigns $campaign */
        foreach ($collectionRecurring as $campaign) {
            $newCampaignData = $campaign->getData();
            unset($newCampaignData['campaign_id']);
            $newCampaignData['nex_run'] = new \Zend_Db_Expr('NULL');
            $newCampaignData['recurring'] = 0;
            $newCampaignData['parent_id'] = $campaign->getId();
            $newCampaignData['auto'] = '1';
            $newCampaignData['internal_name'] = $newCampaignData['internal_name'] . ' [AUTO]';

            $newCampaignData['clicks'] = 0;
            $newCampaignData['unique_clicks'] = 0;
            $newCampaignData['views'] = 0;
            $newCampaignData['unique_views'] = 0;
            $newCampaignData['unsent'] = 0;
            $newCampaignData['sent'] = 0;
            $newCampaignData['bounces'] = 0;
            $newCampaignData['unsubscribes'] = 0;

            $newCampaign = $this->campaignsFactory->create()
                                                  ->setData($newCampaignData)
                                                  ->save();
            $this->_queueEmail($newCampaign);

            $campaignFinal = $this->campaignsFactory->create()->load($campaign->getId());
            $campaignFinal->updateCampaignAfterSend($campaignFinal);
        }

        return $this;
    }

    /**
     * Builds next send date for recurring campaigns
     *
     * @param $campaignData
     *
     * @return string
     */
    public function getNextRecurringDate($campaignData)
    {

        $nextDate = null;

        if (isset($campaignData['recurring_last_run'])) {
            $now = new \Zend_Date;
            $now->setDate($campaignData['recurring_last_run'], self::MYSQL_DATE)
                ->setTime($campaignData['recurring_last_run'], self::MYSQL_DATETIME);
        } else {
            $now = new \Zend_Date;
        }

        if ($campaignData['recurring'] == '0') {
            return $campaignData['deploy_at'];
        }

        $campaignData['recurring_time'] = str_pad($campaignData['recurring_time'], 2, '0', STR_PAD_LEFT);

        if (!isset($campaignData['recurring_first_run']) || strlen($campaignData['recurring_first_run']) == 0) {
            $campaignData['recurring_first_run'] = $now->get(self::MYSQL_DATE);
        }

        if (isset($campaignData['run_until'])) {
            $dateStart = new \Zend_Date;
            $dateStart->setDate($campaignData['recurring_first_run'], self::MYSQL_DATE)
                      ->setHour(0)
                      ->setMinute(0)
                      ->setSecond(0);
        } else {
            $dateStart = $now;
        }

        $today = $dateStart->get(\Zend_Date::WEEKDAY_DIGIT);

        switch ($campaignData['recurring']) {
            case 'd':
            case 'w':
                $oldDay = null;
                $days = explode(',', $campaignData['recurring_daily']);

                if ($campaignData['recurring'] == 'w') {
                    $days = explode(',', $campaignData['recurring_day']);
                }

                if (count($days) > 1) {
                    $index = array_search($today, $days);

                    if ($index === false) {
                        foreach ($days as $key => $day) {
                            if (!isset($oldDay)) {
                                $oldDay = $key;
                            }
                            if ($day > $today) {
                                $index = $oldDay;
                                break;
                            }

                            $oldDay = $key;
                        }

                        if ($index === false) {
                            $index = $days[0];
                        }
                    }

                    if (isset($days[$index])) {
                        $nextDay = $days[$index];
                    } else {
                        $nextDay = reset($days);
                    }

                    $nextDay = $nextDay - $today;
                } else {
                    if ($today == 0) {
                        $nextDay = $days[0];
                    } else {
                        $nextDay = abs(7 - $today + $days[0]);
                    }

                    if ($nextDay == 7) {
                        $nextDay = 0;
                    }
                }

                if ($nextDay < 0) {
                    $nextDay = $nextDay + 6;
                }

                $run = $dateStart->setMinute(0)
                                 ->setSecond(0)
                                 ->setHour($campaignData['recurring_time'])
                                 ->addDay($nextDay);

                if ($now->get(self::MYSQL_DATETIME) >= $run->get(self::MYSQL_DATETIME)) {
                    if ($campaignData['recurring'] == 'w') {
                        $run = $now->setMinute(0)
                                   ->setSecond(0)
                                   ->setHour($campaignData['recurring_time'])
                                   ->addWeek(1);
                    } else {
                        $run = $now->setMinute(0)
                                   ->setSecond(0)
                                   ->setHour($campaignData['recurring_time'])
                                   ->addDay(1);
                    }
                }

                $nextDate = $run->get(self::MYSQL_DATETIME);

                break;

            case 'm':
                $nextDateTemp = $this->calculateRecurringMonth($dateStart, $campaignData);
                $nextDate = $nextDateTemp->get(self::MYSQL_DATETIME);

                break;
            case 'y':
                $dateStart->setMinute(0)
                          ->setSecond(0)
                          ->setHour($campaignData['recurring_time'])
                          ->setMonth($campaignData['recurring_month']);
                $day = $this->calculateRecurringMonth($dateStart, $campaignData)
                            ->get(\Zend_Date::DAY);
                $run = $dateStart->setDay($day);

                if ($now->get(self::MYSQL_DATETIME) >= $run->get(self::MYSQL_DATETIME)) {
                    $run->setMinute(0)
                        ->setSecond(0)
                        ->setHour($campaignData['recurring_time'])
                        ->setMonth($campaignData['recurring_month']);
                    $run->addYear(1);

                    $day = $this->calculateRecurringMonth($run, $campaignData)
                                ->get(\Zend_Date::DAY);
                    $run = $run->setDay($day);
                }

                $nextDate = $run->get(self::MYSQL_DATETIME);

                break;
        }

        return $nextDate;
    }

    /**
     * Builds next month to send recurring campaign
     *
     * @param \Zend_Date $dateStart
     * @param array      $campaignData
     * @param mixed      $monthsToAdd
     *
     * @return \Zend_Date
     */
    public function calculateRecurringMonth($dateStart, $campaignData, $monthsToAdd = null)
    {

        $now = new \Zend_Date;

        if (strpos($campaignData['recurring_monthly'], '|') !== false) {
            $tDate = $dateStart->setMinute(0)
                               ->setSecond(0)
                               ->setHour($campaignData['recurring_time']);

            $lastDay = cal_days_in_month(CAL_GREGORIAN, $tDate->get(\Zend_Date::MONTH), $tDate->get(\Zend_Date::YEAR));

            $calcDay = trim($campaignData['recurring_monthly'], '|');

            if ($monthsToAdd) {
                $tDate->addMonth($monthsToAdd);
            }

            $testDate = clone $dateStart;
            $testDate->setDay($lastDay);

            for ($i = $lastDay; $i >= $lastDay - 7; $i--) {
                $dayN = $testDate->get(\Zend_Date::WEEKDAY_DIGIT);

                if ($dayN == $calcDay) {
                    $finalDay = $testDate->get(\Zend_Date::DAY);
                    break;
                }
                $testDate->subDay(1);
            }

            $run = $dateStart->setMinute(0)
                             ->setSecond(0)
                             ->setHour($campaignData['recurring_time'])
                             ->setDay($finalDay);

            if ($now->get(self::MYSQL_DATETIME) > $run->get(self::MYSQL_DATETIME)) {
                $run = $this->calculateRecurringMonth($dateStart, $campaignData, 1);
            }
        } elseif ($campaignData['recurring_monthly'] == 'u-u') {
            $tDate = $dateStart->setMinute(0)
                               ->setSecond(0)
                               ->setHour($campaignData['recurring_time']);
            $lastDay = cal_days_in_month(CAL_GREGORIAN, $tDate->get(\Zend_Date::MONTH), $tDate->get(\Zend_Date::YEAR));
            $run = $dateStart->setDay($lastDay);
        } elseif ($campaignData['recurring_monthly'] == 'a-u') {
            $day = date(
                'N',
                strtotime($dateStart->get(\Zend_Date::YEAR) . '-' . $dateStart->get(\Zend_Date::MONTH) . '-01')
            );

            $daysToAdd = 1;
            if ($day == 6) {
                $daysToAdd = 3;
            }
            if ($day == 7) {
                $daysToAdd = 2;
            }

            $run = $dateStart->setDay($daysToAdd);
        } elseif ($campaignData['recurring_monthly'] == 'a2-u') {
            $day = date(
                'N',
                strtotime($dateStart->get(\Zend_Date::YEAR) . '-' . $dateStart->get(\Zend_Date::MONTH) . '-01')
            );

            $daysToAdd = 2;
            if ($day == 6) {
                $daysToAdd = 4;
            }
            if ($day == 7) {
                $daysToAdd = 5;
            }

            $run = $dateStart->setDay($daysToAdd);
        } elseif ($campaignData['recurring_monthly'] == 'b-u') {
            $lastDay = date($dateStart->get(\Zend_Date::YEAR) . '-' . $dateStart->get(\Zend_Date::MONTH) . "-t");

            $day = date(
                'N',
                strtotime($dateStart->get(\Zend_Date::YEAR) . '-' . $dateStart->get(\Zend_Date::MONTH) . '-' . $lastDay)
            );

            if ($day == 6) {
                $lastDay -= 1;
            }
            if ($day == 7) {
                $lastDay -= 2;
            }

            $run = $dateStart->setDay($lastDay);
        } elseif ($campaignData['recurring_monthly'] == 'b2-u') {
            $lastDay = date($dateStart->get(\Zend_Date::YEAR) . '-' . $dateStart->get(\Zend_Date::MONTH) . "-t");

            $day = date(
                'N',
                strtotime($dateStart->get(\Zend_Date::YEAR) . '-' . $dateStart->get(\Zend_Date::MONTH) . '-' . $lastDay)
            );

            $lastDay -= 1;

            if ($day == 6) {
                $lastDay -= 1;
            }
            if ($day == 7) {
                $lastDay -= 2;
            }

            $run = $dateStart->setDay($lastDay);
        } elseif (strpos($campaignData['recurring_monthly'], '-') !== false) {
            $calcDay = explode('-', $campaignData['recurring_monthly']);

            $tDate = $dateStart->setMinute(0)
                               ->setSecond(0)
                               ->setHour($campaignData['recurring_time']);

            if ($monthsToAdd) {
                $tDate->addMonth($monthsToAdd);
            }

            $testDate = clone $dateStart;
            $testDate->setDay(1);

            for ($i = 0; $i <= 6; $i++) {
                $dayN = $testDate->get(\Zend_Date::WEEKDAY_DIGIT);

                if ($dayN == $calcDay[1]) {
                    $day = $testDate->get(\Zend_Date::DAY);
                    break;
                }

                $testDate->addDay(1);
            }

            if ($calcDay[0] > 1) {
                $finalDay = ($day + (($calcDay[0] - 1) * 7));
            } else {
                $finalDay = $day;
            }

            $run = $dateStart->setMinute(0)
                             ->setSecond(0)
                             ->setHour($campaignData['recurring_time'])
                             ->setDay($finalDay);

            if ($now->get(self::MYSQL_DATETIME) > $run->get(self::MYSQL_DATETIME)) {
                $run = $this->calculateRecurringMonth($dateStart, $campaignData, 1);
            }
        } else {
            $run = $dateStart->setMinute(0)
                             ->setSecond(0)
                             ->setHour($campaignData['recurring_time'])
                             ->setDay($campaignData['recurring_monthly']);

            if ($now->get(self::MYSQL_DATETIME) > $run->get(self::MYSQL_DATETIME)) {
                $dateStart->addMonth(1)
                          ->setMinute(0)
                          ->setSecond(0)
                          ->setHour($campaignData['recurring_time'])
                          ->setDay($campaignData['recurring_monthly']);
            }
        }

        return $run;
    }

    /**
     * @return \Magento\Rule\Model\AbstractModel
     */
    public function afterSave()
    {

        if ($this->getData('controller_panda')) {
            $this->findLinksForCampaign($this);
        }

        return parent::afterSave();
    }

    /**
     * @return \Magento\Rule\Model\AbstractModel
     */
    public function beforeSave()
    {

        if ($this->getData('controller_panda')) {
            $this->setDeployAt($this->_localeDate->convertConfigTimeToUtc($this->getDeployAt()));
        }

        $this->setData('recurring_next_run', $this->getNextRecurringDate($this->getData()));

        if ($this->getRunTimes() && $this->isObjectNew()) {
            $this->setRunTimes($this->getRunTimes());
        }

        if ($this->getId()) {
            $this->followupFactory->create()->updateSendDate($this);
        }

        if ($this->isObjectNew() && !$this->getStatus()) {
            $this->setStatus('standby');
        }

        if ($this->isObjectNew() && !$this->getType()) {
            $this->setType('email');
        }

        foreach ($this->statsFields as $field) {
            $this->unsetData($field);
        }

        return parent::beforeSave();
    }

    /**
     * Processes some "meta data" for campaigns
     *
     * @param Campaigns $campaign
     * @param bool      $reloadData
     *
     * @throws \Exception
     */
    public function updateCampaignAfterSend($campaign, $reloadData = false)
    {

        if ($reloadData) {
            $campaign = $this->load($campaign->getId());
        }

        if ($campaign->getData('status') == 'running') {
            return;
        }

        $data = $campaign->getData();

        $now = new \Zend_Date;

        $data['recurring_last_run'] = $now->get(self::MYSQL_DATETIME);

        if ($data['recurring'] != '0') {
            $data['status'] = 'standby';

            $finishRun = false;
            if ($data['run_times_left'] <= 1 && $data['end_method'] != 'run_until') {
                $finishRun = true;
            }

            #$data['run_times_left'] = new \Zend_Db_Expr('run_times_left - 1');

            $finishDate = false;
            if ($now->get(self::MYSQL_DATE) > $data['run_until'] ||
                $data['recurring_next_run'] > $data['run_until']
            ) {
                $finishDate = true;
            }

            if (($data['end_method'] == 'run_until' && $finishDate) ||
                ($data['end_method'] == 'any' && ($finishDate || $finishRun)) ||
                ($data['end_method'] == 'number' && $finishRun) ||
                ($data['end_method'] == 'both' && $finishDate && $finishRun)
            ) {
                $data['status'] = 'finished';
            }
        } else {
            $data['status'] = 'finished';
        }

        $campaign->setData($data)
                 ->save();
    }

    /**
     * Returns a list of campaigns IDS and internal name
     *
     * @param null $type
     *
     * @return array
     */
    public function toFormValues($type = null)
    {

        $return = [];
        $collection = $this->campaignsCollection->create()
                                                ->addFieldToSelect('internal_name')
                                                ->addFieldToSelect('campaign_id')
                                                ->setOrder('internal_name', 'ASC');

        if ($type) {
            $collection->addFieldToFilter('type', $type);
        }

        /** @var Campaigns $campaign */
        foreach ($collection as $campaign) {
            $return[$campaign->getId()] =
                $campaign->getInternalName() . '[' . $campaign->getType() . ']' . ' (ID: ' . $campaign->getId() . ')';
        }

        return $return;
    }

    /**
     * @param bool|false $campaignId
     *
     * @return array
     * @internal param bool|true $pending
     */
    public function toFormValuesNonAuto($campaignId = false)
    {

        $return = [];
        $collection = $this->campaignsCollection->create()
                                                ->addFieldToFilter('auto', 0)
                                                ->addFieldToSelect('internal_name')
                                                ->addFieldToSelect('campaign_id')
                                                ->addFieldToFilter('type', 'email')
                                                ->setOrder('campaign_id', 'DESC');

        if ($campaignId) {
            $collection->addFieldToFilter('campaign_id', $campaignId);
        }

        /** @var Campaigns $campaign */
        foreach ($collection as $campaign) {
            $return[$campaign->getId()] =
                $campaign->getInternalName() . ' (ID: ' . $campaign->getId() . ')';
        }

        return $return;
    }

    /**
     *
     * @param Campaigns $campaign
     *
     * @return Campaigns
     */
    public function findLinksForCampaign(Campaigns $campaign)
    {

        if (!$campaign->getId()) {
            return false;
        }

        $message = $this->pandaHelper->getTemplateProcessor()->filter($campaign->getMessage());

        $data = [];
        $data['campaign_id'] = $campaign->getId();

        $links = $this->linksCollection->create()->addFieldToFilter('campaign_id', $campaign->getId());

        $exists = [];
        $temp = [];

        /** @var LinksFactory $link */
        foreach ($links as $link) {
            $exists[$link->getId()] = $link->getLink();
            #$link->delete();
        }

        try {
            $doc = new \DOMDocument();
            $doc->loadHTML($message);
            $history = [];

            $paramsToRemove = $this->scopeConfig->getValue(
                'panda_nuntius/config/url',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $paramsToRemove = explode(',', $paramsToRemove);
            $paramsToRemove = array_map('trim', $paramsToRemove);

            /** @var \DOMNode $link */
            foreach ($doc->getElementsByTagName('a') as $link) {
                if (substr($link->getAttribute('href'), 0, 7) == 'mailto:') {
                    continue;
                }

                $data['link'] = $link->getAttribute('href');
                $url = $data['link'];
                if (count($paramsToRemove) > 0) {
                    $urlTemp = parse_url($url);
                    if (isset($urlTemp['query'])) {
                        parse_str($urlTemp['query'], $output);
                        foreach ($output as $key => $value) {
                            if (in_array($key, $paramsToRemove)) {
                                $url = str_replace('&' . $key . '=' . $value, '', $url);
                                $url = str_replace($key . '=' . $value, '', $url);
                            }
                        }
                    }
                }
                $url = trim($url);
                $url = rtrim($url, '/');
                $url = rtrim($url, '?');
                $data['link'] = $url;
                if (in_array($data['link'], $history)) {
                    continue;
                }

                $history[] = $data['link'];
                $data['campaign_id'] = $campaign->getId();
                $data['clicks'] = 0;

                if (!filter_var($data['link'], FILTER_VALIDATE_URL)) {
                    continue;
                }

                if (in_array($data['link'], $exists)) {
                    $temp[] = $data['link'];
                    continue;
                }

                $result = $this->linksFactory->create()
                                             ->setData($data)
                                             ->save();

                $temp[] = $result->getLink();
            }
        } catch (\Exception $e) {
        }
        $links = $this->linksCollection->create()->addFieldToFilter('campaign_id', $campaign->getId());

        foreach ($links as $link) {
            if (!in_array($link->getLink(), $temp)) {
                $link->delete();
            }
        }

        return $this;
    }

    /**
     *
     * @param $records
     *
     * @return array
     * @throws \Exception
     */
    public function tryAgain($records)
    {

        $messages = [];
        $result = $this->serviceFactory->create()
                                       ->getEmailService()->sendEmail(true, $records);
        foreach ($result->getData('messages') as $message) {
            $messages[] = $message;
        }

        $result = $this->serviceFactory->create()
                                       ->getSmsService()->sendSms(true, $records);
        foreach ($result->getData('messages') as $message) {
            $messages[] = $message;
        }

        return $messages;
    }

    /**
     * @return Service\Smtp
     */
    public function sendEmails()
    {

        return $this->serviceFactory->create()->getEmailService()->sendEmail();
    }

    /**
     *
     * @param Subscribers $subscriber
     *
     * @return boolean
     */
    public function getMessageForSubscriber(Subscribers $subscriber)
    {

        $message = $this->archiveCollection->create()
                                           ->addFieldToFilter('campaign_id', $this->getId())
                                           ->addFieldToFilter('subscriber_id', $subscriber->getId());

        if ($message->count() == 1) {
            return $message->getFirstItem()->getMessage();
        }

        return false;
    }

    /**
     *
     */
    public function trackCampaign()
    {

        if ($this->getTrack() == 2 && $this->getTrack() != 0) {
            return (bool) $this->scopeConfig->getValue(
                'panda_nuntius/info/track',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            );
        }

        return $this->getTrack() == 1 ? true : false;
    }

    /**
     * @return bool
     */
    public function autologin()
    {

        if ($this->getAutologin() == 2 || !$this->getAutologin()) {
            return (bool) $this->scopeConfig->getValue(
                'panda_nuntius/info/autologin',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            );
        }

        return $this->getAutologin() == 1 ? true : false;
    }

    /**
     * @param null $campaign
     *
     * @return bool|int
     */
    public function calculateNumberRecipients($campaign = null)
    {

        if (null === $campaign) {
            $campaign = $this;
        }

        if ($campaign->getStatus() != 'standby' && $campaign->getStatus() != 'queuing' && $campaign->getStatus()) {
            return false;
        }

        /** @var \Licentia\Panda\Model\ResourceModel\Subscribers\Collection $subscribers */
        $subscribers = $this->subscribersCollection->create();
        $subscribers->addSegments($campaign->getSegmentsIds())
                    ->addStoreIds($campaign->getStoreId())
                    ->addSubscriberTypeFilter($campaign->getType());

        if ($campaign->getData('previous_customers') == 1) {
            $subscribers->addFieldToFilter('previous_customer', '1');
        } else {
            $subscribers->addActiveSubscribers();
        }

        return $subscribers->getSize();
    }

    /**
     * @throws \Exception
     */
    public function fixIssues()
    {

        $connection = $this->getResource()->getConnection();

        $date = $this->_localeDate->date()
                                  ->sub(new \DateInterval('PT12H'))
                                  ->format('Y-m-d H:i:s');

        $connection->update(
            $this->getResource()
                 ->getMainTable(),
            ['status' => 'queuing'],
            ['deploy_at<=?' => $date, 'status=?' => 'running']
        );
    }

    /**
     * @return mixed
     */
    public function getConditionsInstance()
    {

        return $this->conditionsCombine->create();
    }

    /**
     * @return mixed
     */
    public function getActionsInstance()
    {

        return $this->collectionCombine->create();
    }

    /**
     * @return $this
     */
    public function afterLoad()
    {

        parent::afterLoad();

        if ($this->getId()) {
            $tags = $this->tagsFactory->create()->getTagsHash('campaigns', $this->getId());
            $this->setData('tags', array_flip($tags));
        }

        return $this;
    }

    /**
     * @return \Magento\Rule\Model\AbstractModel
     */
    public function afterDelete()
    {

        $this->tagsFactory->create()->updateTags('campaigns', $this, []);

        return parent::afterDelete();
    }

    /**
     * @return mixed
     */
    public function getTags()
    {

        return $this->getData('tags');
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
     * @param $parentId
     *
     * @return $this
     */
    public function setParentId($parentId)
    {

        return $this->setData('parent_id', $parentId);
    }

    /**
     * @param $type
     *
     * @return $this
     */
    public function setType($type)
    {

        return $this->setData('type', $type);
    }

    /**
     * @param $senderId
     *
     * @return $this
     */
    public function setSenderId($senderId)
    {

        return $this->setData('sender_id', $senderId);
    }

    /**
     * @param $status
     *
     * @return $this
     */
    public function setStatus($status)
    {

        return $this->setData('status', $status);
    }

    /**
     * @param $segmentsIds
     *
     * @return $this
     */
    public function setSegmentsIds($segmentsIds)
    {

        return $this->setData('segments_ids', $segmentsIds);
    }

    /**
     * @param $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {

        return $this->setData('store_id', $storeId);
    }

    /**
     * @param $internalName
     *
     * @return $this
     */
    public function setInternalName($internalName)
    {

        return $this->setData('internal_name', $internalName);
    }

    /**
     * @param $subject
     *
     * @return $this
     */
    public function setSubject($subject)
    {

        return $this->setData('subject', $subject);
    }

    /**
     * @param $message
     *
     * @return $this
     */
    public function setMessage($message)
    {

        return $this->setData('message', $message);
    }

    /**
     * @param $messageParsed
     *
     * @return $this
     */
    public function setMessageParsed($messageParsed)
    {

        return $this->setData('message_parsed', $messageParsed);
    }

    /**
     * @param $conditionsSerialized
     *
     * @return $this
     */
    public function setConditionsSerialized($conditionsSerialized)
    {

        return $this->setData('conditions_serialized', $conditionsSerialized);
    }

    /**
     * @param $deployAt
     *
     * @return $this
     */
    public function setDeployAt($deployAt)
    {

        return $this->setData('deploy_at', $deployAt);
    }

    /**
     * @param $recurringMonth
     *
     * @return $this
     */
    public function setRecurringMonth($recurringMonth)
    {

        return $this->setData('recurring_month', $recurringMonth);
    }

    /**
     * @param $recurringMonthly
     *
     * @return $this
     */
    public function setRecurringMonthly($recurringMonthly)
    {

        return $this->setData('recurring_monthly', $recurringMonthly);
    }

    /**
     * @param $recurringDay
     *
     * @return $this
     */
    public function setRecurringDay($recurringDay)
    {

        return $this->setData('recurring_day', $recurringDay);
    }

    /**
     * @param $recurringDaily
     *
     * @return $this
     */
    public function setRecurringDaily($recurringDaily)
    {

        return $this->setData('recurring_daily', $recurringDaily);
    }

    /**
     * @param $recurringLastRun
     *
     * @return $this
     */
    public function setRecurringLastRun($recurringLastRun)
    {

        return $this->setData('recurring_last_run', $recurringLastRun);
    }

    /**
     * @param $recurringNextRun
     *
     * @return $this
     */
    public function setRecurringNextRun($recurringNextRun)
    {

        return $this->setData('recurring_next_run', $recurringNextRun);
    }

    /**
     * @param $recurringTime
     *
     * @return $this
     */
    public function setRecurringTime($recurringTime)
    {

        return $this->setData('recurring_time', $recurringTime);
    }

    /**
     * @param $recurringFirstRun
     *
     * @return $this
     */
    public function setRecurringFirstRun($recurringFirstRun)
    {

        return $this->setData('recurring_first_run', $recurringFirstRun);
    }

    /**
     * @param $runUntil
     *
     * @return $this
     */
    public function setRunUntil($runUntil)
    {

        return $this->setData('run_until', $runUntil);
    }

    /**
     * @param $runTimes
     *
     * @return $this
     */
    public function setRunTimes($runTimes)
    {

        return $this->setData('run_times', $runTimes);
    }

    /**
     * @param $runTimesLeft
     *
     * @return $this
     */
    public function setRunTimesLeft($runTimesLeft)
    {

        return $this->setData('run_times_left', $runTimesLeft);
    }

    /**
     * @param $endMethod
     *
     * @return $this
     */
    public function setEndMethod($endMethod)
    {

        return $this->setData('end_method', $endMethod);
    }

    /**
     * @param $recurring
     *
     * @return $this
     */
    public function setRecurring($recurring)
    {

        return $this->setData('recurring', $recurring);
    }

    /**
     * @param $auto
     *
     * @return $this
     */
    public function setAuto($auto)
    {

        return $this->setData('auto', $auto);
    }

    /**
     * @param $segmentsOptions
     *
     * @return $this
     */
    public function setSegmentsOptions($segmentsOptions)
    {

        return $this->setData('segments_options', $segmentsOptions);
    }

    /**
     * @param $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {

        return $this->setData('is_active', $isActive);
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
     * @param $cost
     *
     * @return $this
     */
    public function setCost($cost)
    {

        return $this->setData('cost', $cost);
    }

    /**
     * @param $recurringUnique
     *
     * @return $this
     */
    public function setRecurringUnique($recurringUnique)
    {

        return $this->setData('recurring_unique', $recurringUnique);
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
     * @param $clicks
     *
     * @return $this
     */
    public function setClicks($clicks)
    {

        return $this->setData('clicks', $clicks);
    }

    /**
     * @param $uniqueClicks
     *
     * @return $this
     */
    public function setUniqueClicks($uniqueClicks)
    {

        return $this->setData('unique_clicks', $uniqueClicks);
    }

    /**
     * @param $views
     *
     * @return $this
     */
    public function setViews($views)
    {

        return $this->setData('views', $views);
    }

    /**
     * @param $uniqueViews
     *
     * @return $this
     */
    public function setUniqueViews($uniqueViews)
    {

        return $this->setData('unique_views', $uniqueViews);
    }

    /**
     * @param $unsent
     *
     * @return $this
     */
    public function setUnsent($unsent)
    {

        return $this->setData('unsent', $unsent);
    }

    /**
     * @param $sent
     *
     * @return $this
     */
    public function setSent($sent)
    {

        return $this->setData('sent', $sent);
    }

    /**
     * @param $totalMessages
     *
     * @return $this
     */
    public function setTotalMessages($totalMessages)
    {

        return $this->setData('total_messages', $totalMessages);
    }

    /**
     * @param $errors
     *
     * @return $this
     */
    public function setErrors($errors)
    {

        return $this->setData('errors', $errors);
    }

    /**
     * @param $bounces
     *
     * @return $this
     */
    public function setBounces($bounces)
    {

        return $this->setData('bounces', $bounces);
    }

    /**
     * @param $autoresponderId
     *
     * @return $this
     */
    public function setAutoresponderId($autoresponderId)
    {

        return $this->setData('autoresponder_id', $autoresponderId);
    }

    /**
     * @param $autoresponderRecipient
     *
     * @return $this
     */
    public function setAutoresponderRecipient($autoresponderRecipient)
    {

        return $this->setData('autoresponder_recipient', $autoresponderRecipient);
    }

    /**
     * @param $autoresponderEvent
     *
     * @return $this
     */
    public function setAutoresponderEvent($autoresponderEvent)
    {

        return $this->setData('autoresponder_event', $autoresponderEvent);
    }

    /**
     * @param $autoresponderEventId
     *
     * @return $this
     */
    public function setAutoresponderEventId($autoresponderEventId)
    {

        return $this->setData('autoresponder_event_id', $autoresponderEventId);
    }

    /**
     * @param $followupId
     *
     * @return $this
     */
    public function setFollowupId($followupId)
    {

        return $this->setData('followup_id', $followupId);
    }

    /**
     * @param $splitId
     *
     * @return $this
     */
    public function setSplitId($splitId)
    {

        return $this->setData('split_id', $splitId);
    }

    /**
     * @param $splitVersion
     *
     * @return $this
     */
    public function setSplitVersion($splitVersion)
    {

        return $this->setData('split_version', $splitVersion);
    }

    /**
     * @param $splitFinal
     *
     * @return $this
     */
    public function setSplitFinal($splitFinal)
    {

        return $this->setData('split_final', $splitFinal);
    }

    /**
     * @param $unsubscribes
     *
     * @return $this
     */
    public function setUnsubscribes($unsubscribes)
    {

        return $this->setData('unsubscribes', $unsubscribes);
    }

    /**
     * @param $subscriberTime
     *
     * @return $this
     */
    public function setSubscriberTime($subscriberTime)
    {

        return $this->setData('subscriber_time', $subscriberTime);
    }

    /**
     * @param $abandonedId
     *
     * @return $this
     */
    public function setAbandonedId($abandonedId)
    {

        return $this->setData('abandoned_id', $abandonedId);
    }

    /**
     * @param $track
     *
     * @return $this
     */
    public function setTrack($track)
    {

        return $this->setData('track', $track);
    }

    /**
     * @param $templateId
     *
     * @return $this
     */
    public function setTemplateId($templateId)
    {

        return $this->setData('template_id', $templateId);
    }

    /**
     * @param $previousCustomers
     *
     * @return $this
     */
    public function setPreviousCustomers($previousCustomers)
    {

        return $this->setData('previous_customers', $previousCustomers);
    }

    /**
     * @param $autologin
     *
     * @return $this
     */
    public function setAutologin($autologin)
    {

        return $this->setData('autologin', $autologin);
    }

    /**
     * @param $globalTemplateId
     *
     * @return $this
     */
    public function setGlobalTemplateId($globalTemplateId)
    {

        return $this->setData('global_template_id', $globalTemplateId);
    }

    /**
     * @param $maxQueueHour
     *
     * @return $this
     */
    public function setMaxQueueHour($maxQueueHour)
    {

        return $this->setData('max_queue_hour', $maxQueueHour);
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
    public function getParentId()
    {

        return $this->getData('parent_id');
    }

    /**
     * @return mixed
     */
    public function getType()
    {

        return $this->getData('type');
    }

    /**
     * @return mixed
     */
    public function getSenderId()
    {

        return $this->getData('sender_id');
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {

        return $this->getData('status');
    }

    /**
     * @return mixed
     */
    public function getSegmentsIds()
    {

        return $this->getData('segments_ids');
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {

        return $this->getData('store_id');
    }

    /**
     * @return mixed
     */
    public function getInternalName()
    {

        return $this->getData('internal_name');
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {

        return $this->getData('subject');
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {

        return $this->getData('message');
    }

    /**
     * @return mixed
     */
    public function getMessageParsed()
    {

        return $this->getData('message_parsed');
    }

    /**
     * @return mixed
     */
    public function getConditionsSerialized()
    {

        return $this->getData('conditions_serialized');
    }

    /**
     * @return mixed
     */
    public function getDeployAt()
    {

        return $this->getData('deploy_at');
    }

    /**
     * @return mixed
     */
    public function getRecurringMonth()
    {

        return $this->getData('recurring_month');
    }

    /**
     * @return mixed
     */
    public function getRecurringMonthly()
    {

        return $this->getData('recurring_monthly');
    }

    /**
     * @return mixed
     */
    public function getRecurringDay()
    {

        return $this->getData('recurring_day');
    }

    /**
     * @return mixed
     */
    public function getRecurringDaily()
    {

        return $this->getData('recurring_daily');
    }

    /**
     * @return mixed
     */
    public function getRecurringLastRun()
    {

        return $this->getData('recurring_last_run');
    }

    /**
     * @return mixed
     */
    public function getRecurringNextRun()
    {

        return $this->getData('recurring_next_run');
    }

    /**
     * @return mixed
     */
    public function getRecurringTime()
    {

        return $this->getData('recurring_time');
    }

    /**
     * @return mixed
     */
    public function getRecurringFirstRun()
    {

        return $this->getData('recurring_first_run');
    }

    /**
     * @return mixed
     */
    public function getRunUntil()
    {

        return $this->getData('run_until');
    }

    /**
     * @return mixed
     */
    public function getRunTimes()
    {

        return $this->getData('run_times');
    }

    /**
     * @return mixed
     */
    public function getRunTimesLeft()
    {

        return $this->getData('run_times_left');
    }

    /**
     * @return mixed
     */
    public function getEndMethod()
    {

        return $this->getData('end_method');
    }

    /**
     * @return mixed
     */
    public function getRecurring()
    {

        return $this->getData('recurring');
    }

    /**
     * @return mixed
     */
    public function getAuto()
    {

        return $this->getData('auto');
    }

    /**
     * @return mixed
     */
    public function getSegmentsOptions()
    {

        return $this->getData('segments_options');
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {

        return $this->getData('is_active');
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

    /**
     * @return mixed
     */
    public function getCost()
    {

        return $this->getData('cost');
    }

    /**
     * @return mixed
     */
    public function getRecurringUnique()
    {

        return $this->getData('recurring_unique');
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
    public function getClicks()
    {

        return $this->getData('clicks');
    }

    /**
     * @return mixed
     */
    public function getUniqueClicks()
    {

        return $this->getData('unique_clicks');
    }

    /**
     * @return mixed
     */
    public function getViews()
    {

        return $this->getData('views');
    }

    /**
     * @return mixed
     */
    public function getUniqueViews()
    {

        return $this->getData('unique_views');
    }

    /**
     * @return mixed
     */
    public function getUnsent()
    {

        return $this->getData('unsent');
    }

    /**
     * @return mixed
     */
    public function getSent()
    {

        return $this->getData('sent');
    }

    /**
     * @return mixed
     */
    public function getTotalMessages()
    {

        return $this->getData('total_messages');
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {

        return $this->getData('errors');
    }

    /**
     * @return mixed
     */
    public function getBounces()
    {

        return $this->getData('bounces');
    }

    /**
     * @return mixed
     */
    public function getAutoresponderId()
    {

        return $this->getData('autoresponder_id');
    }

    /**
     * @return mixed
     */
    public function getAutoresponderRecipient()
    {

        return $this->getData('autoresponder_recipient');
    }

    /**
     * @return mixed
     */
    public function getAutoresponderEvent()
    {

        return $this->getData('autoresponder_event');
    }

    /**
     * @return mixed
     */
    public function getAutoresponderEventId()
    {

        return $this->getData('autoresponder_event_id');
    }

    /**
     * @return mixed
     */
    public function getFollowupId()
    {

        return $this->getData('followup_id');
    }

    /**
     * @return mixed
     */
    public function getSplitId()
    {

        return $this->getData('split_id');
    }

    /**
     * @return mixed
     */
    public function getSplitVersion()
    {

        return $this->getData('split_version');
    }

    /**
     * @return mixed
     */
    public function getSplitFinal()
    {

        return $this->getData('split_final');
    }

    /**
     * @return mixed
     */
    public function getUnsubscribes()
    {

        return $this->getData('unsubscribes');
    }

    /**
     * @return mixed
     */
    public function getSubscriberTime()
    {

        return $this->getData('subscriber_time');
    }

    /**
     * @return mixed
     */
    public function getAbandonedId()
    {

        return $this->getData('abandoned_id');
    }

    /**
     * @return mixed
     */
    public function getTrack()
    {

        return $this->getData('track');
    }

    /**
     * @return mixed
     */
    public function getTemplateId()
    {

        return $this->getData('template_id');
    }

    /**
     * @return mixed
     */
    public function getPreviousCustomers()
    {

        return $this->getData('previous_customers');
    }

    /**
     * @return mixed
     */
    public function getAutologin()
    {

        return $this->getData('autologin');
    }

    /**
     * @return mixed
     */
    public function getGlobalTemplateId()
    {

        return $this->getData('global_template_id');
    }

    /**
     * @return mixed
     */
    public function getMaxQueueHour()
    {

        return $this->getData('max_queue_hour');
    }

    /**
     * @param $templateFile
     *
     * @return $this
     */
    public function setTemplateFile($templateFile)
    {

        return $this->setData('template_file', $templateFile);
    }

    /**
     * @return mixed
     */
    public function getTemplateFile()
    {

        return $this->getData('template_file');
    }

    /**
     * @param $numberRecipients
     *
     * @return $this
     */
    public function setNumberRecipients($numberRecipients)
    {

        return $this->setData('number_recipients', $numberRecipients);
    }

    /**
     * @return mixed
     */
    public function getNumberRecipients()
    {

        return $this->getData('number_recipients');
    }
}
