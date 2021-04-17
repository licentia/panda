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
 * Class Goals
 *
 * @package Licentia\Panda\Model
 */
class Goals extends \Magento\Framework\Model\AbstractModel
{

    const RESULTS = [
        1 => 'Accomplished',
        0 => 'Failed',
        2 => 'Running',
        3 => 'Stand By',
    ];

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_goals';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'goals';

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var ResourceModel\Campaigns\CollectionFactory
     */
    protected $campaignsCollection;

    /**
     * @var ResourceModel\Subscribers\CollectionFactory
     */
    protected $subscriberCollection;

    /**
     * @var GoalsFactory
     */
    protected $goalsFactory;

    /**
     * @var
     */
    protected $goalsCollection;

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * @var CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * @var \Licentia\Equity\Model\Segments\ListSegmentsFactory
     */
    protected $segmentListFactory;

    /**
     * @var \Licentia\Forms\Model\FormsFactory
     */
    protected $formsFactory;

    /**
     * @var \Licentia\Forms\Model\FormEntriesFactory
     */
    protected $formEntriesFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * Goals constructor.
     *
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface         $timezone
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param GoalsFactory                                                 $goalsFactory
     * @param \Licentia\Forms\Model\FormsFactory                           $formsFactory
     * @param \Licentia\Forms\Model\FormEntriesFactory                     $formEntriesFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date               $dateFilter
     * @param ResourceModel\Subscribers\CollectionFactory                  $subscriberCollection
     * @param ResourceModel\Campaigns\CollectionFactory                    $campaignsCollection
     * @param ResourceModel\Goals\CollectionFactory                        $goalsCollection
     * @param \Licentia\Panda\Helper\Data                                  $pandaHelper
     * @param \Licentia\Equity\Model\SegmentsFactory                       $segmentsFactory
     * @param CampaignsFactory                                             $campaignsFactory
     * @param \Licentia\Equity\Model\Segments\ListSegmentsFactory          $segmentListFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        GoalsFactory $goalsFactory,
        \Licentia\Forms\Model\FormsFactory $formsFactory,
        \Licentia\Forms\Model\FormEntriesFactory $formEntriesFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Licentia\Panda\Model\ResourceModel\Subscribers\CollectionFactory $subscriberCollection,
        \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory $campaignsCollection,
        \Licentia\Panda\Model\ResourceModel\Goals\CollectionFactory $goalsCollection,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        CampaignsFactory $campaignsFactory,
        \Licentia\Equity\Model\Segments\ListSegmentsFactory $segmentListFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->dateFilter = $dateFilter;
        $this->goalsFactory = $goalsFactory;
        $this->subscriberCollection = $subscriberCollection;
        $this->campaignsCollection = $campaignsCollection;
        $this->campaignsFactory = $campaignsFactory;
        $this->pandaHelper = $pandaHelper;
        $this->goalsCollection = $goalsCollection;
        $this->segmentsFactory = $segmentsFactory;
        $this->formsFactory = $formsFactory;
        $this->formEntriesFactory = $formEntriesFactory;
        $this->segmentListFactory = $segmentListFactory;
        $this->timezone = $timezone;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Goals::class);
    }

    /**
     * @return array
     */
    public static function getGoalTypes()
    {

        $info = [
            'global_conversions'   => __('Conversions - Global'),
            'campaign_conversions' => __('Conversions - Campaign'),
            'global_views'         => __('Views - Global'),
            'campaign_views'       => __('Views - Campaign'),
            'global_clicks'        => __('Clicks - Global'),
            'campaign_clicks'      => __('Clicks - Campaign'),
            'global_subscribers'   => __('Subscribers - Global'),
            'segment_subscribers'  => __('Subscribers - Segment'),
            'forms_subscribers'    => __('Forms - New Subscribers from Form'),
            'forms_entries'        => __('Forms - Total Entries in Form'),
        ];

        return $info;
    }

    /**
     *
     * @param Goals $goal
     *
     */
    public function updateGoalCurrentValue(Goals $goal)
    {

        if ($goal->getResult() != 2) {
            return;
        }

        $date = $this->timezone->date()->format('Y-m-d');

        if ($goal->getToDate() < $date) {
            if ($goal->getExpectedValue() < $goal->getCurrentValue()) {
                $goal->setResult(1);
            } else {
                $goal->setResult(0);
            }
        }

        $goal->setCurrentValue($goal->getGoalCurrentValue($goal))
             ->setSkipDateRange(true)
             ->save();
    }

    /**
     *
     */
    public function updateGoalsCurrentValue()
    {

        $goals = $this->goalsCollection->create();
        /** @var self $goal */
        foreach ($goals as $goal) {
            $goal->updateGoalCurrentValue($goal);
        }
    }

    /**
     *
     * @param Goals $goal
     *
     * @return string
     */
    public function getGoalCurrentValue(Goals $goal)
    {

        $type = '';
        $goalType = $goal->getGoalType();

        if (stripos($goalType, 'global') === false) {
            if (stripos($goalType, 'segment_') !== false) {
                $type = 'segments';
                $model = $this->segmentsFactory->create()->load($this->getGoalTypeOptionId());
            } elseif (stripos($goalType, 'forms_') !== false) {
                $type = 'forms';
                $model = $this->formsFactory->create()->load($this->getGoalTypeOptionId());
            } else {
                $model = $this->campaignsFactory->create()->load($this->getGoalTypeOptionId());
            }

            if (stripos($goalType, 'forms_') !== false) {
                if ($goalType == 'forms_subscribers') {
                    $value = $model->getSubscribers();
                }

                if ($goalType == 'forms_entries') {
                    $value = $model->getEntries();
                }
            } else {
                if (stripos($goalType, '_conversions') !== false) {
                    $value = $model->getConversionsNumber();
                }
                if (stripos($goalType, '_views') !== false) {
                    $value = $model->getViews();
                }
                if (stripos($goalType, '_clicks') !== false) {
                    $value = $model->getClicks();
                }
                if (stripos($goalType, '_subscribers') !== false) {
                    if ($type == 'segments') {
                        $value = $model->getRecords();
                    }
                }
            }
        } else {
            if ($goalType == 'global_conversions') {
                $collection = $this->campaignsCollection->create();
                $collection->getSelect()
                           ->reset('columns')
                           ->columns(['total' => new \Zend_Db_Expr('SUM(main_table.conversions_number)')]);
            }
            if ($goalType == 'global_views') {
                $collection = $this->campaignsCollection->create();
                $collection->getSelect()
                           ->reset('columns')
                           ->columns(['total' => new \Zend_Db_Expr('SUM(views)')]);
            }
            if ($goalType == 'global_clicks') {
                $collection = $this->campaignsCollection->create();
                $collection->getSelect()
                           ->reset('columns')
                           ->columns(['total' => new \Zend_Db_Expr('SUM(clicks)')]);
            }
            if ($goalType == 'global_subscribers') {
                $collection = $this->subscriberCollection->create();
                $collection->getSelect()
                           ->reset('columns')
                           ->columns(['total' => new \Zend_Db_Expr('COUNT(subscriber_id)')]);
            }

            $value = $collection->getFirstItem()->getTotal();
        }

        return $value;
    }

    /**
     *
     */
    public function cron()
    {

        $date = $this->timezone->date()->format('Y-m-d');

        $collection = $this->goalsCollection->create()->addFieldToFilter('result', ['nin' => [0, 1]]);

        /** @var Goals $goal */
        foreach ($collection as $goal) {
            if (stripos($goal->getGoalType(), 'segment_') !== false) {
                $segmentId = $goal->getGoalTypeOptionId();

                $this->segmentListFactory->create()->load($segmentId);
            }

            if ($date == $goal->getStartDate()) {
                $goal->setOriginalValue($goal->getGoalCurrentValue($goal));
            }

            $goal->updateGoalCurrentValue($goal);

            if ($goal->getFromDate() <= $date && $goal->getToDate() >= $date) {
                $goal->setResult(2);
            }

            if ($goal->getToDate() < $date) {
                if ($goal->getExpectedValue() < $goal->getCurrentValue()) {
                    $goal->setResult(1);
                } else {
                    $goal->setResult(0);
                }
            }

            $goal->setSkipDateRange(true)->save();
        }
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateBeforeSave()
    {

        if ($this->getSkipDateRange()) {
            return parent::validateBeforeSave();
        }

        $date = $this->pandaHelper->gmtDate('Y-m-d');

        if ($this->getFromDate()) {
            try {
                $inputFilter = new \Zend_Filter_Input(
                    ['from_date' => $this->dateFilter],
                    [],
                    $this->getData()
                );
                $data = $inputFilter->getUnescaped();
                $this->addData($data);
                $this->timezone->formatDate($this->getFromDate());
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid date in From Date'));
            }
        }
        if ($this->getToDate()) {
            try {
                $inputFilter = new \Zend_Filter_Input(
                    ['to_date' => $this->dateFilter],
                    [],
                    $this->getData()
                );
                $data = $inputFilter->getUnescaped();
                $this->addData($data);

                $this->timezone->formatDate($this->getToDate());
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid date in To Date'));
            }
        }

        $fromDate = $this->getFromDate() ?: $this->getFromDate();
        if ($fromDate > $this->getToDate()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The end date cannot be earlier than start date')
            );
        }
        if ($this->getToDate() < $date) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The end date cannot be earlier than today')
            );
        }

        return parent::validateBeforeSave();
    }

    /**
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save()
    {

        $date = $this->pandaHelper->gmtDate();

        if ($this->getFromDate() <= $date && $this->getToDate() >= $date) {
            $this->setResult(2);
        }

        if ($this->getFromDate() <= $date && $this->getToDate() <= $date) {
            $this->setResult(3);
        }

        if (!$this->getId()) {
            $this->setOriginalValue($this->getGoalCurrentValue($this));
            $this->setCurrentValue($this->getGoalCurrentValue($this));
        }

        if ($this->getId()) {
            $old = $this->goalsFactory->create()->load($this->getId());
            $this->setOriginalValue($old->getOriginalValue());

            if (in_array($this->getResult(), [0, 1, 2])) {
                $this->unsetData('from_date');
            }
        }

        $variation = $this->getVariation();

        $current = $this->getOriginalValue();
        $expectedValue = '';

        if (stripos($variation, '-') === false && stripos($variation, '+') === false &&
            stripos($variation, '%') === false
        ) {
            $expectedValue = $variation;
        }

        if (stripos($variation, '-') !== false && stripos($variation, '%') === false) {
            $expectedValue = $current - abs($variation);
        }

        if (stripos($variation, '+') !== false && stripos($variation, '%') === false) {
            $expectedValue = $current + $variation;
        }

        if (stripos($variation, '%') !== false) {
            $value = str_replace(['+', '%', '-'], "", $variation);

            if (stripos($variation, '+') !== false || stripos($variation, '-') === false) {
                $value = $current + $current * $value / 100;
            }

            if (stripos($variation, '-') !== false) {
                $value = $current - $current * $value / 100;
            }
            $expectedValue = $value;
        }

        $this->setExpectedValue($expectedValue);

        if ($this->getOriginalValue() >= $this->getExpectedValue()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'The original value is already greater than the expected value. Expected value: %1, current value: %2',
                    $this->getExpectedValue(),
                    $this->getOriginalValue()
                )
            );
        }

        return parent::save();
    }

    /**
     * @param $goalId
     *
     * @return $this
     */
    public function setGoalId($goalId)
    {

        return $this->setData('goal_id', $goalId);
    }

    /**
     * @param $goalType
     *
     * @return $this
     */
    public function setGoalType($goalType)
    {

        return $this->setData('goal_type', $goalType);
    }

    /**
     * @param $goalTypeOptionId
     *
     * @return $this
     */
    public function setGoalTypeOptionId($goalTypeOptionId)
    {

        return $this->setData('goal_type_option_id', $goalTypeOptionId);
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {

        return $this->setData('name', $name);
    }

    /**
     * @param $bool
     *
     * @return $this
     */
    public function setSkipDateRange($bool)
    {

        return $this->setData('skip_date_range', $bool);
    }

    /**
     * @param $description
     *
     * @return $this
     */
    public function setDescription($description)
    {

        return $this->setData('description', $description);
    }

    /**
     * @param $fromDate
     *
     * @return $this
     */
    public function setFromDate($fromDate)
    {

        return $this->setData('from_date', $fromDate);
    }

    /**
     * @param $toDate
     *
     * @return $this
     */
    public function setToDate($toDate)
    {

        return $this->setData('to_date', $toDate);
    }

    /**
     * @param $result
     *
     * @return $this
     */
    public function setResult($result)
    {

        return $this->setData('result', $result);
    }

    /**
     * @param $variation
     *
     * @return $this
     */
    public function setVariation($variation)
    {

        return $this->setData('variation', $variation);
    }

    /**
     * @param $expectedValue
     *
     * @return $this
     */
    public function setExpectedValue($expectedValue)
    {

        return $this->setData('expected_value', $expectedValue);
    }

    /**
     * @param $currentValue
     *
     * @return $this
     */
    public function setCurrentValue($currentValue)
    {

        return $this->setData('current_value', $currentValue);
    }

    /**
     * @param $originalValue
     *
     * @return $this
     */
    public function setOriginalValue($originalValue)
    {

        return $this->setData('original_value', $originalValue);
    }

    /**
     * @param $paramName
     *
     * @return $this
     */
    public function setParamName($paramName)
    {

        return $this->setData('param_name', $paramName);
    }

    /**
     * @param $paramValue
     *
     * @return $this
     */
    public function setParamValue($paramValue)
    {

        return $this->setData('param_value', $paramValue);
    }

    /**
     * @return mixed
     */
    public function getGoalId()
    {

        return $this->getData('goal_id');
    }

    /**
     * @return mixed
     */
    public function getGoalType()
    {

        return $this->getData('goal_type');
    }

    /**
     * @return mixed
     */
    public function getGoalTypeOptionId()
    {

        return $this->getData('goal_type_option_id');
    }

    /**
     * @return mixed
     */
    public function getName()
    {

        return $this->getData('name');
    }

    /**
     * @return bool
     */
    public function getSkipDateRange()
    {

        return $this->getData('skip_date_range');
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {

        return $this->getData('description');
    }

    /**
     * @return mixed
     */
    public function getFromDate()
    {

        return $this->getData('from_date');
    }

    /**
     * @return mixed
     */
    public function getToDate()
    {

        return $this->getData('to_date');
    }

    /**
     * @return mixed
     */
    public function getResult()
    {

        return $this->getData('result');
    }

    /**
     * @return mixed
     */
    public function getVariation()
    {

        return $this->getData('variation');
    }

    /**
     * @return mixed
     */
    public function getExpectedValue()
    {

        return $this->getData('expected_value');
    }

    /**
     * @return mixed
     */
    public function getCurrentValue()
    {

        return $this->getData('current_value');
    }

    /**
     * @return mixed
     */
    public function getOriginalValue()
    {

        return $this->getData('original_value');
    }

    /**
     * @return mixed
     */
    public function getParamName()
    {

        return $this->getData('param_name');
    }

    /**
     * @return mixed
     */
    public function getParamValue()
    {

        return $this->getData('param_value');
    }
}
