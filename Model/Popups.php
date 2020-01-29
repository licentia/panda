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
 * Class Popups
 *
 * @package Licentia\Panda\Model
 */
class Popups extends \Magento\Rule\Model\AbstractModel
{

    /**
     *
     */
    const BASE_MEDIA_PATH = 'panda/banners/images';

    /**
     *
     */
    const POPUP_TYPES = [
        'floating' => 'Floating Window',
        'modal'    => 'Modal Window',
        'sitebar'  => 'Site Bar',
        'block'    => 'Inline Info Block',
    ];

    /**
     * @var Popups\Condition\CombineFactory
     */
    protected $combineFactory;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_popups';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'popups';

    /**
     * @var ResourceModel\Templates\CollectionFactory
     */
    protected $popupsCollection;

    /**
     * @var \Magento\Widget\Model\Template\FilterFactory
     */
    protected $filterFactory;

    /**
     * @var Popups\Action\CollectionFactory
     */
    protected $collectionCombine;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Popups constructor.
     *
     * @param \Magento\Checkout\Model\Session                      $session
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date       $dateFilter
     * @param Popups\Condition\CombineFactory                      $combineFactory
     * @param Popups\Action\CollectionFactory                      $collectionFactory
     * @param ResourceModel\Popups\CollectionFactory               $popupsCollection
     * @param \Magento\Widget\Model\Template\FilterFactory         $filterFactory
     * @param \Magento\Framework\Model\Context                     $context
     * @param \Magento\Framework\Registry                          $registry
     * @param \Magento\Framework\Data\FormFactory                  $formFactory
     * @param \Licentia\Panda\Helper\Data                          $pandaHelper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param null                                                 $resource
     * @param null                                                 $resourceCollection
     * @param array                                                $data
     */
    public function __construct(
        \Magento\Checkout\Model\Session $session,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        Popups\Condition\CombineFactory $combineFactory,
        Popups\Action\CollectionFactory $collectionFactory,
        ResourceModel\Popups\CollectionFactory $popupsCollection,
        \Magento\Widget\Model\Template\FilterFactory $filterFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        $resource = null,
        $resourceCollection = null,
        array $data = []
    ) {

        $this->checkoutSession = $session;
        $this->pandaHelper = $pandaHelper;
        $this->dateFilter = $dateFilter;
        $this->timezone = $localeDate;
        $this->collectionCombine = $collectionFactory;
        $this->combineFactory = $combineFactory;
        $this->filterFactory = $filterFactory;
        $this->popupsCollection = $popupsCollection;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(\Licentia\Panda\Model\ResourceModel\Popups::class);
    }

    /**
     * @return mixed
     */
    public function getConditionsInstance()
    {

        return $this->combineFactory->create();
    }

    /**
     * @return mixed
     */
    public function getActionsInstance()
    {

        return $this->collectionCombine->create();
    }

    /**
     * @return \Magento\Rule\Model\AbstractModel
     */
    public function beforeSave()
    {

        if ($this->getData('controller_panda') && !$this->getCode()) {
            $this->setCode($this->getId() . '00');
        }

        return parent::beforeSave();
    }

    /**
     * @return int
     */
    public function isActive()
    {

        /** @var \Licentia\Panda\Model\ResourceModel\Popups\Collection $collection */
        $collection = $this->getCollection();

        $collectionSelect = $collection->addFieldToFilter('is_active', 1);
        $select = $collectionSelect->getSelect();
        $select->where("from_date <= ? or from_date IS NULL", $this->pandaHelper->gmtDate());
        $select->where("to_date >= ? or to_date IS NULL", $this->pandaHelper->gmtDate());
        $collection->addFieldToFilter('popup_id', $this->getId());

        return $collection->getSize();
    }

    /**
     * @param        $data
     * @param        $storeId
     * @param bool   $popupId
     *
     * @return array|bool
     */
    public function getPopupForDisplay($data, $storeId, $popupId = false)
    {

        $session = $this->checkoutSession;
        $segmentsIds = $this->pandaHelper->getCustomerSegmentsIds();
        $customerId = $this->pandaHelper->getCustomerId();
        $subscriber = $this->pandaHelper->getSubscriber();
        $useragent = $data['useragent'];

        $data['params'] = json_decode($data['params'], true);
        $this->_registry->register('panda_popup_data', $data);

        $ignore = $this->getIgnoredCookiesForUser();

        /** @var \Licentia\Panda\Model\ResourceModel\Popups\Collection $collection */
        $collection = $this->getCollection();

        $collectionSelect = $collection->addFieldToFilter('is_active', 1);
        $select = $collectionSelect->getSelect();
        $select->where("from_date <= ? or from_date IS NULL", $this->pandaHelper->gmtDate('Y-m-d'));
        $select->where("to_date >= ? or to_date IS NULL", $this->pandaHelper->gmtDate('Y-m-d'));

        $select->where('FIND_IN_SET(?,store_id) OR store_id IS NULL', $storeId);

        if ($popupId) {
            $select->where("type = ?", 'block');
            if (is_numeric($popupId)) {
                $select->where("popup_id = ?", (int) $popupId);
            } else {
                $select->where("identifier = ?", (string) $popupId);
            }

            $select->orderRand();
        } else {
            $select->where("type != ?", 'block');
        }

        $select->where(
            " ((INSTR(?,LOWER(useragent)) > 0 AND useragent_filter = 'contains' AND LENGTH(useragent)>0) 
                        OR (INSTR(?,LOWER(useragent)) = 0 AND useragent_filter = 'not' AND LENGTH(useragent)>0 )) 
                        OR LENGTH(useragent)=0 
                        OR LENGTH(useragent) IS NULL ",
            strtolower($useragent)
        );

        if ($subscriber) {
            $select->where("avoid_subscribers = ?", 0);
        }

        if (count($ignore) > 0 && !$popupId) {
            $select->where("(popup_id NOT IN (?) OR type='sitebar' )", $ignore);
        }

        if ($customerId) {
            $select->where("display_to = 'both' OR display_to = 'customers' ");
        } else {
            $select->where("display_to = 'both' OR display_to = 'guests' ");
        }

        if ($collection->getSize() == 0) {
            return [];
        }

        $popups = [];
        $popupsSet = [];

        /** @var \Licentia\Panda\Model\Popups $item */
        foreach ($collection as $item) {
            if ($item->getSegmentsIds()) {
                $segsIds = explode(',', $item->getSegmentsIds());

                if ($item->getData('segments_options') == 'include_any' && !array_intersect($segsIds, $segmentsIds)) {
                    continue;
                }

                if ($item->getData('segments_options') == 'include_all' &&
                    count(array_intersect($segsIds, $segmentsIds)) != count($segmentsIds)) {
                    continue;
                }

                if ($item->getData('segments_options') == 'exclude_any' && array_intersect($segsIds, $segmentsIds)) {
                    continue;
                }

                if ($item->getData('segments_options') == 'exclude_any' &&
                    count(array_intersect($segsIds, $segmentsIds)) == count($segmentsIds)) {
                    continue;
                }
            }

            if ($item->validate($session->getQuote()->getShippingAddress())) {
                $popups[] = $item;

                if ($item->getType() == 'sitebar') {
                    $popupsSet['sitebar-' . $item->getPosition()] = true;
                } elseif ($item->getType() == 'block') {
                    $popupsSet['block'] = true;
                }

                if (isset($popups['sitebar-top']) && isset($popups['sitebar-bottom']) && isset($popups['window'])) {
                    break;
                }
            }
        }

        if (!$popups) {
            return [];
        }

        foreach ($popups as $key => $item) {
            if (!$popupId) {
                $item->assertDisplaySettings();

                if ($item->getData('hide_for') != 0) {
                    $dateIgnore = (new \DateTime($this->pandaHelper->gmtDate()))
                        ->add(new \DateInterval('P' . $item->getData('hide_for') . 'D'))
                        ->format('Y-m-d H:i:s');
                } else {
                    $dateIgnore = (new \DateTime($this->pandaHelper->gmtDate()))
                        ->add(new \DateInterval('P10Y'))
                        ->format('Y-m-d H:i:s');
                }

                $resource = $this->getResource();
                $connection = $resource->getConnection();

                $connection->delete(
                    $resource->getTable('panda_popups_history'),
                    [
                        'visitor=?'  => $this->getVisitorId(),
                        'popup_id=?' => $item->getId(),
                    ]
                );
                $connection->insert(
                    $resource->getTable('panda_popups_history'),
                    [
                        'visitor'    => $this->getVisitorId(),
                        'popup_id'   => $item->getId(),
                        'hide_until' => $dateIgnore,
                    ]
                );
            }

            $item->setData('impressions', $item->getData('impressions') + 1)
                 ->save();
        }

        return $popups;
    }

    /**
     * @param $useragent
     * @param $storeId
     *
     * @return bool
     */
    public function hasActivePopups($useragent, $storeId)
    {

        /** @var \Licentia\Panda\Model\ResourceModel\Popups\Collection $collection */
        $collection = $this->getCollection();

        $collectionSelect = $collection->addFieldToFilter('is_active', 1);
        $select = $collectionSelect->getSelect();
        $select->where("from_date <= ? or from_date IS NULL", $this->pandaHelper->gmtDate('Y-m-d'));
        $select->where("to_date >= ? or to_date IS NULL", $this->pandaHelper->gmtDate('Y-m-d'));

        $select->where('FIND_IN_SET(?,store_id) OR store_id IS NULL', $storeId);
        $select->where("type != ?", 'block');

        $select->where(
            " ((INSTR(?,LOWER(useragent)) > 0 AND useragent_filter = 'contains' AND LENGTH(useragent)>0) 
                        OR (INSTR(?,LOWER(useragent)) = 0 AND useragent_filter = 'not' AND LENGTH(useragent)>0 )) 
                        OR LENGTH(useragent)=0 
                        OR LENGTH(useragent) IS NULL ",
            strtolower($useragent)
        );

        if ($collection->getSize() == 0) {
            return false;
        }

        return true;
    }

    /**
     * @param $ids
     *
     * @return bool|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getPopups($ids)
    {

        if (!$ids) {
            return false;
        }

        return $this->getCollection()
                    ->addFieldToFilter('is_active', 1)
                    ->addFieldToFilter('popup_id', ['in' => $ids]);
    }

    /**
     *
     */
    public function assertDisplaySettings()
    {

        if (stripos($this->getContent(), '{{') !== false) {
            $this->setContent(
                $this->filterFactory->create()
                                    ->filter($this->getContent())
            );
        }

        if ($this->getType() == 'floating') {
            $this->setBackgroundActive(1);
        }

        if ($this->getType() == 'modal') {
            $this->setBackgroundActive(0);
            $this->setBackground(1);
        }

        if ($this->getAllowClose() == 0 || $this->getType() == 'sitebar') {
            $this->setBlur(0);
            $this->setEscapeKey(0);
            $this->setCloseButton(0);
        }

        if ((int) $this->getBorderRadius() == $this->getBorderRadius() &&
            $this->getBorderRadius() > 0
        ) {
            $this->setBorderRadius($this->getBorderRadius() . 'px');
        }

        if ($this->getBorderWidth() > 0) {
            if (!$this->getBorderColor()) {
                $this->setBorderColor('#333');
            }
        }

        if ((int) $this->getHeight() == $this->getHeight()) {
            $this->setHeight((int) $this->getHeight() . 'px');
        }

        if ((int) $this->getWidth() == $this->getWidth()) {
            $this->setWidth((int) $this->getWidth() . 'px');
        }

        if ((int) $this->getBorderWidth() == $this->getBorderWidth()) {
            $this->setBorderWidth((int) $this->getBorderWidth() . 'px');
        }

        if ($this->getType() == 'sitebar') {
            $this->setAfterScroll(false);
            $this->setAfterTime(false);
            $this->setWidth('100%');
        }
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getPopupToEmulate($id)
    {

        $this->load($id)
             ->setEmulated(true);

        $this->assertDisplaySettings();

        return [$this];
    }

    /**
     * @return array
     */
    public function getOptionArray()
    {

        $list = $this->popupsCollection->create()
                                       ->addFieldToFilter('is_active', 1)
                                       ->addFieldToSelect('popup_id')
                                       ->addFieldToSelect('name');

        $result = [];

        foreach ($list as $template) {
            $result[] = ['value' => $template->getId(), 'label' => $template->getName()];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function toFormValues()
    {

        $options = $this->getOptionArray();

        $return = [];

        foreach ($options as $option) {
            $return[$option['value']] = $option['label'];
        }

        return $return;
    }

    /**
     * @return \Magento\Rule\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateBeforeSave()
    {

        $date = $this->pandaHelper->gmtDate('Y-m-d');

        if ($this->getFromDate() && $this->getToDate()) {
            try {
                $inputFilter = new \Zend_Filter_Input(
                    ['to_date' => $this->dateFilter, 'from_date' => $this->dateFilter],
                    [],
                    $this->getData()
                );
                $data = $inputFilter->getUnescaped();
                $this->addData($data);
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid date format'));
            }

            try {
                $this->timezone->formatDate($this->getFromDate());
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid date in From Date'));
            }

            try {
                $this->timezone->formatDate($this->getToDate());
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid date in To Date'));
            }

            if ($this->getFromDate() > $this->getToDate()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The end date cannot be earlier than start date')
                );
            }

            if ($this->getToDate() < $date) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The end date cannot be earlier than today')
                );
            }
        }

        return parent::validateBeforeSave();
    }

    /**
     * @return array|bool
     */
    /**
     * @return array|bool
     */
    public function getVisitorId()
    {

        $visitor = $this->pandaHelper->getIdentifierValueFromCode('visitor');

        if (!$visitor) {
            $this->pandaHelper->addIdentifierValueFromArea('visitor', sha1(uniqid()));
        }

        return $visitor;
    }

    /**
     * @return array
     */
    public function getIgnoredCookiesForUser()
    {

        $resource = $this->getResource();
        $connection = $resource->getConnection();

        return $connection->fetchCol(
            $connection->select()
                       ->from($resource->getTable('panda_popups_history'), ['popup_id'])
                       ->where('visitor=?', $this->getVisitorId())
                       ->where('hide_until <=?', $this->pandaHelper->gmtDate())
        );
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function _convertFlatToRecursive(array $data)
    {

        $arr = [];
        foreach ($data as $key => $value) {
            if (($key === 'conditions' || $key === 'actions') && is_array($value)) {
                foreach ($value as $id => $data) {
                    $path = explode('--', $id);
                    $node = &$arr;
                    for ($i = 0, $l = sizeof($path); $i < $l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = [];
                        }
                        $node = &$node[$key][$path[$i]];
                    }
                    foreach ($data as $k => $v) {
                        $node[$k] = $v;
                    }
                }
            }
        }

        return $arr;
    }

    /**
     * @param $popupId
     *
     * @return $this
     */
    public function setPopupId($popupId)
    {

        return $this->setData('popup_id', $popupId);
    }

    /**
     * @param $code
     *
     * @return $this
     */
    public function setCode($code)
    {

        return $this->setData('code', $code);
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
     * @param $segmentsIds
     *
     * @return $this
     */
    public function setSegmentsIds($segmentsIds)
    {

        return $this->setData('segments_ids', $segmentsIds);
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
     * @param $identifier
     *
     * @return $this
     */
    public function setIdentifier($identifier)
    {

        return $this->setData('identifier', $identifier);
    }

    /**
     * @param $blocksIds
     *
     * @return $this
     */
    public function setBlocksIds($blocksIds)
    {

        return $this->setData('blocks_ids', $blocksIds);
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
     * @param $displayTo
     *
     * @return $this
     */
    public function setDisplayTo($displayTo)
    {

        return $this->setData('display_to', $displayTo);
    }

    /**
     * @param $platform
     *
     * @return $this
     */
    public function setPlatform($platform)
    {

        return $this->setData('platform', $platform);
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
     * @param $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {

        return $this->setData('is_active', $isActive);
    }

    /**
     * @param $avoidSubscribers
     *
     * @return $this
     */
    public function setAvoidSubscribers($avoidSubscribers)
    {

        return $this->setData('avoid_subscribers', $avoidSubscribers);
    }

    /**
     * @param $content
     *
     * @return $this
     */
    public function setContent($content)
    {

        return $this->setData('content', $content);
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
     * @param $impressions
     *
     * @return $this
     */
    public function setImpressions($impressions)
    {

        return $this->setData('impressions', $impressions);
    }

    /**
     * @param $closeButton
     *
     * @return $this
     */
    public function setCloseButton($closeButton)
    {

        return $this->setData('close_button', $closeButton);
    }

    /**
     * @param $escapeKey
     *
     * @return $this
     */
    public function setEscapeKey($escapeKey)
    {

        return $this->setData('escape_key', $escapeKey);
    }

    /**
     * @param $effect
     *
     * @return $this
     */
    public function setEffect($effect)
    {

        return $this->setData('effect', $effect);
    }

    /**
     * @param $opacity
     *
     * @return $this
     */
    public function setOpacity($opacity)
    {

        return $this->setData('opacity', $opacity);
    }

    /**
     * @param $position
     *
     * @return $this
     */
    public function setPosition($position)
    {

        return $this->setData('position', $position);
    }

    /**
     * @param $scrollLock
     *
     * @return $this
     */
    public function setScrollLock($scrollLock)
    {

        return $this->setData('scroll_lock', $scrollLock);
    }

    /**
     * @param $hideFor
     *
     * @return $this
     */
    public function setHideFor($hideFor)
    {

        return $this->setData('hide_for', $hideFor);
    }

    /**
     * @param $allowClose
     *
     * @return $this
     */
    public function setAllowClose($allowClose)
    {

        return $this->setData('allow_close', $allowClose);
    }

    /**
     * @param $afterScroll
     *
     * @return $this
     */
    public function setAfterScroll($afterScroll)
    {

        return $this->setData('after_scroll', $afterScroll);
    }

    /**
     * @param $afterTime
     *
     * @return $this
     */
    public function setAfterTime($afterTime)
    {

        return $this->setData('after_time', $afterTime);
    }

    /**
     * @param $blur
     *
     * @return $this
     */
    public function setBlur($blur)
    {

        return $this->setData('blur', $blur);
    }

    /**
     * @param $backgroundColor
     *
     * @return $this
     */
    public function setBackgroundColor($backgroundColor)
    {

        return $this->setData('background_color', $backgroundColor);
    }

    /**
     * @param $backgroundImage
     *
     * @return $this
     */
    public function setBackgroundImage($backgroundImage)
    {

        return $this->setData('background_image', $backgroundImage);
    }

    /**
     * @param $backgroundActive
     *
     * @return $this
     */
    public function setBackgroundActive($backgroundActive)
    {

        return $this->setData('background_active', $backgroundActive);
    }

    /**
     * @param $styles
     *
     * @return $this
     */
    public function setStyles($styles)
    {

        return $this->setData('styles', $styles);
    }

    /**
     * @param $background
     *
     * @return $this
     */
    public function setBackground($background)
    {

        return $this->setData('background', $background);
    }

    /**
     * @param $width
     *
     * @return $this
     */
    public function setWidth($width)
    {

        return $this->setData('width', $width);
    }

    /**
     * @param $height
     *
     * @return $this
     */
    public function setHeight($height)
    {

        return $this->setData('height', $height);
    }

    /**
     * @param $borderRadius
     *
     * @return $this
     */
    public function setBorderRadius($borderRadius)
    {

        return $this->setData('border_radius', $borderRadius);
    }

    /**
     * @param $borderWidth
     *
     * @return $this
     */
    public function setBorderWidth($borderWidth)
    {

        return $this->setData('border_width', $borderWidth);
    }

    /**
     * @param $borderColor
     *
     * @return $this
     */
    public function setBorderColor($borderColor)
    {

        return $this->setData('border_color', $borderColor);
    }

    /**
     * @param $layerBackgroundColor
     *
     * @return $this
     */
    public function setLayerBackgroundColor($layerBackgroundColor)
    {

        return $this->setData('layer_background_color', $layerBackgroundColor);
    }

    /**
     * @return mixed
     */
    public function getPopupId()
    {

        return $this->getData('popup_id');
    }

    /**
     * @return mixed
     */
    public function getCode()
    {

        return $this->getData('code');
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
    public function getSegmentsIds()
    {

        return $this->getData('segments_ids');
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
    public function getIdentifier()
    {

        return $this->getData('identifier');
    }

    /**
     * @return mixed
     */
    public function getBlocksIds()
    {

        return $this->getData('blocks_ids');
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
    public function getDisplayTo()
    {

        return $this->getData('display_to');
    }

    /**
     * @return mixed
     */
    public function getPlatform()
    {

        return $this->getData('platform');
    }

    /**
     * @return mixed
     */
    public function getName()
    {

        return $this->getData('name');
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
    public function getAvoidSubscribers()
    {

        return $this->getData('avoid_subscribers');
    }

    /**
     * @return mixed
     */
    public function getContent()
    {

        return $this->getData('content');
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
    public function getImpressions()
    {

        return $this->getData('impressions');
    }

    /**
     * @return mixed
     */
    public function getCloseButton()
    {

        return $this->getData('close_button');
    }

    /**
     * @return mixed
     */
    public function getEscapeKey()
    {

        return $this->getData('escape_key');
    }

    /**
     * @return mixed
     */
    public function getEffect()
    {

        return $this->getData('effect');
    }

    /**
     * @return mixed
     */
    public function getOpacity()
    {

        return $this->getData('opacity');
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {

        return $this->getData('position');
    }

    /**
     * @return mixed
     */
    public function getScrollLock()
    {

        return $this->getData('scroll_lock');
    }

    /**
     * @return mixed
     */
    public function getHideFor()
    {

        return $this->getData('hide_for');
    }

    /**
     * @return mixed
     */
    public function getAllowClose()
    {

        return $this->getData('allow_close');
    }

    /**
     * @return mixed
     */
    public function getAfterScroll()
    {

        return $this->getData('after_scroll');
    }

    /**
     * @return mixed
     */
    public function getAfterTime()
    {

        return $this->getData('after_time');
    }

    /**
     * @return mixed
     */
    public function getBlur()
    {

        return $this->getData('blur');
    }

    /**
     * @return mixed
     */
    public function getBackgroundColor()
    {

        return $this->getData('background_color');
    }

    /**
     * @return mixed
     */
    public function getBackgroundImage()
    {

        return $this->getData('background_image');
    }

    /**
     * @return mixed
     */
    public function getBackgroundActive()
    {

        return $this->getData('background_active');
    }

    /**
     * @return mixed
     */
    public function getStyles()
    {

        return $this->getData('styles');
    }

    /**
     * @return mixed
     */
    public function getBackground()
    {

        return $this->getData('background');
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {

        return $this->getData('width');
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {

        return $this->getData('height');
    }

    /**
     * @return mixed
     */
    public function getBorderRadius()
    {

        return $this->getData('border_radius');
    }

    /**
     * @return mixed
     */
    public function getBorderWidth()
    {

        return $this->getData('border_width');
    }

    /**
     * @return mixed
     */
    public function getBorderColor()
    {

        return $this->getData('border_color');
    }

    /**
     * @return mixed
     */
    public function getLayerBackgroundColor()
    {

        return $this->getData('layer_background_color');
    }
}
