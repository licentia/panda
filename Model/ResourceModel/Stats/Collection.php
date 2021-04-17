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

namespace Licentia\Panda\Model\ResourceModel\Stats;

/**
 * Class Collection
 *
 * @package Licentia\Panda\Model\ResourceModel\Stats
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Links\CollectionFactory
     */
    protected $linksCollection;

    /**
     * @var CollectionFactory
     */
    protected $statsCollection;

    /**
     * Collection constructor.
     *
     * @param \Licentia\Panda\Model\ResourceModel\Links\CollectionFactory  $linksCollection
     * @param CollectionFactory                                            $statsCollection
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null          $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null    $resource
     */
    public function __construct(
        \Licentia\Panda\Model\ResourceModel\Links\CollectionFactory $linksCollection,
        CollectionFactory $statsCollection,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->linksCollection = $linksCollection;
        $this->statsCollection = $statsCollection;
    }

    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {

        parent::_construct();
        $this->_init(\Licentia\Panda\Model\Stats::class, \Licentia\Panda\Model\ResourceModel\Stats::class);
    }

    /**
     * @param bool $field
     *
     * @return array
     */
    public function getAllIds($field = false)
    {

        if (!$field) {
            return parent::getAllIds();
        }

        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Zend_Db_Select::ORDER);
        $idsSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(\Zend_Db_Select::COLUMNS);
        $idsSelect->columns($field, 'main_table');

        return $this->getConnection()->fetchCol($idsSelect);
    }

    /**
     * @param      $campaignId
     * @param      $field
     * @param null $startDate
     *
     * @return array
     */
    public function getStats($campaignId, $field, $startDate = null)
    {

        $columns = [
            'total' => new \Zend_Db_Expr('COUNT(*)'),
        ];

        if (is_array($field)) {
            $column = reset($field);
            $field = key($field);
            $columns[$field] = new \Zend_Db_Expr($column);
            $group = new \Zend_Db_Expr($column);
        } else {
            $columns[] = $field;
            $group = $field;
        }

        /** @var self $report */
        $report = $this->statsCollection->create();
        $report->addFieldToFilter('campaign_id', $campaignId);
        $report->addFieldToFilter('type', 'views');
        $report->getSelect()
               ->reset('columns')
               ->columns($columns)
               ->group($group)
               ->order('total DESC');

        if ($startDate) {
            $report->addFieldToFilter(
                'event_at',
                ['gt' => new \Zend_Db_Expr('NOW() - INTERVAL ' . (int) $startDate . ' DAY')]
            );
        }
        $result = [];
        $result[] = ['Browser', 'views', 'Clicks'];

        foreach ($report as $item) {
            $result[] = [$item->getData($field), (int) $item->getData('total')];
        }

        $report = $this->statsCollection->create();
        $report->addFieldToFilter('campaign_id', $campaignId);
        $report->addFieldToFilter('type', 'clicks');
        $report->getSelect()
               ->reset('columns')
               ->columns($columns)
               ->group($group)
               ->order('total DESC');

        if ($startDate) {
            $report->addFieldToFilter(
                'event_at',
                ['gt' => new \Zend_Db_Expr('NOW() - INTERVAL ' . (int) $startDate . ' DAY')]
            );
        }

        foreach ($report as $item) {
            if (count($result) > 1) {
                foreach ($result as $key => $country) {
                    if ($country[0] == $item->getData($field)) {
                        $result[$key][2] = (int) $item->getData('total');
                        break;
                    }
                    if (!isset($result[$key][2])) {
                        $result[$key][2] = 0;
                    }
                }
            } else {
                $result[] = [$item->getData($field), 0, (int) $item->getData('total')];
            }
        }

        return $result;
    }

    /**
     * @param      $campaignId
     * @param      $option
     * @param bool $unique
     *
     * @return mixed
     */
    public function getGeneral($campaignId, $option, $unique = false)
    {

        $report = $this->statsCollection->create();
        $report->addFieldToFilter('campaign_id', $campaignId);
        $report->addFieldToFilter('type', $option);
        $report->getSelect()
               ->reset('columns')
               ->columns(['total' => new \Zend_Db_Expr('COUNT(*)')]);

        if ($unique) {
            $report->getSelect()->group(['subscriber_id']);
        }

        return $report->getData();
    }

    /**
     * @param bool $campaignId
     *
     * @return array
     */
    public function getCities($campaignId = false)
    {

        $report = $this->statsCollection->create();
        $report->addFieldToFilter('campaign_id', $campaignId);
        $report->addFieldToFilter('type', 'views');
        $report->addFieldToFilter('city', ['notnull' => true]);
        $report->addFieldToFilter('country', ['notnull' => true]);
        $report->getSelect()
               ->reset('columns')
               ->columns(['total' => new \Zend_Db_Expr('COUNT(*)'), 'city', 'country'])
               ->group(['city', 'country'])
               ->order('total DESC');

        $countries = [];
        $countries[] = ['Country', 'City', 'views', 'Clicks'];

        foreach ($report as $item) {
            $countries[] =
                [$item->getData('country'), $item->getData('city'), (int) $item->getData('total')];
        }

        $report = $this->statsCollection->create();
        $report->addFieldToFilter('campaign_id', $campaignId);
        $report->addFieldToFilter('type', 'clicks');
        $report->addFieldToFilter('city', ['notnull' => true]);
        $report->addFieldToFilter('country', ['notnull' => true]);
        $report->getSelect()
               ->reset('columns')
               ->columns(['total' => new \Zend_Db_Expr('COUNT(*)'), 'city', 'country'])
               ->group(['city', 'country'])
               ->order('total DESC');

        foreach ($report as $item) {
            foreach ($countries as $key => $country) {
                if ($country[1] == $item->getData('city')) {
                    $countries[$key][3] = (int) $item->getData('total');
                    break;
                }
                if (!isset($countries[$key][2])) {
                    $countries[$key][3] = 0;
                }
            }
        }

        return $countries;
    }

    /**
     * @param $campaignId
     *
     * @return array
     */
    public function getLinks($campaignId)
    {

        $links = $this->linksCollection->create();
        $links->addFieldToFilter('campaign_id', $campaignId);

        $countries = [];
        $countries[] = ['Url', 'Opens', 'Conversions'];

        foreach ($links as $item) {
            $countries[] = [
                $item->getData('link'),
                (int) $item->getData('clicks'),
                (int) $item->getData('conversions_number'),
            ];
        }

        return $countries;
    }

    /**
     * @param string $field
     *
     * @return $this
     */
    public function addTimeToSelect($field = 'event_at')
    {

        $this->getSelect()
             ->columns(
                 [
                     'count_' . $field => new \Zend_Db_Expr('COUNT(*)'),
                     $field            => new \Zend_Db_Expr("DATE_FORMAT($field,'%H')"),
                 ]
             )
             ->group(new \Zend_Db_Expr("DATE_FORMAT($field,'%H')"));

        return $this;
    }
}
