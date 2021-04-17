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

namespace Licentia\Panda\Model\ResourceModel\Subscribers;

/**
 * Class Collection
 *
 * @package Licentia\Panda\Model\ResourceModel\Subscribers
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected \Licentia\Panda\Helper\Data $pandaHelper;

    /**
     * @var string
     */
    protected string $_idFieldName = 'subscriber_id';

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Licentia\Panda\Helper\Data                                  $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Licentia\Panda\Helper\Data                                  $pandaHelper
     * @param \Magento\Framework\DB\Adapter\AdapterInterface               $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null    $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );

        $this->pandaHelper = $pandaHelper;
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
        $this->_init(\Licentia\Panda\Model\Subscribers::class, \Licentia\Panda\Model\ResourceModel\Subscribers::class);
    }

    /**
     * @return $this
     */
    public function _initSelect()
    {

        parent::_initSelect();

        $this->getSelect()
             ->joinLeft(
                 ['td' => $this->getResource()->getTable('panda_tags_relations')],
                 "td.entity_id = main_table.subscriber_id AND td.entity_type='subscribers'",
                 []
             )
             ->joinLeft(
                 ['t' => $this->getResource()->getTable('panda_tags')],
                 't.tag_id = td.tag_id',
                 ['tags' => new \Zend_Db_Expr('GROUP_CONCAT(t.name)')]
             )
             ->group('main_table.subscriber_id');

        $this->addFilterToMap('tags', 't.tag_id');

        return $this;
    }

    /**
     * @return $this
     */
    public function addActiveSubscribers()
    {

        $this->addFieldToFilter('status', \Licentia\Panda\Model\Subscribers::STATUS_SUBSCRIBED);

        return $this;
    }

    /**
     * @param null $type
     *
     * @return $this
     */
    public function addSubscriberTypeFilter($type = null)
    {

        if (!$type) {
            return $this;
        }

        if ($type == 'sms') {
            $this->getSelect()->where('LENGTH(main_table.cellphone) >= 5');
        }
        if ($type == 'email') {
            $this->getSelect()->where('LENGTH(main_table.email) >= 5');
        }

        return $this;
    }

    /**
     * @param string $forms
     *
     * @return $this
     */
    public function addForms($forms = '')
    {

        if (!$forms) {
            return $this;
        }

        if (!is_array($forms)) {
            $forms = explode(',', $forms);
        }

        $select = $this->getSelect();
        $select->joinInner(
            $this->getTable('panda_forms_entries'),
            'main_table.subscriber_id=' . $this->getTable('panda_forms_entries') . '.subscriber_id',
            []
        );
        $select->where($this->getTable('panda_forms_entries') . '.form_id IN (?)', $forms);

        $this->addGroupFilter();

        return $this;
    }

    /**
     * @param string $tags
     *
     * @return $this
     */
    public function addTags($tags = '')
    {

        if (!$tags) {
            return $this;
        }

        if (!is_array($tags)) {
            $tags = explode(',', $tags);
        }

        if (!is_numeric(reset($tags))) {
            $tags = array_flip($tags);
        }

        $select = $this->getSelect();
        $select->joinInner(
            $this->getTable('panda_tags_relations'),
            'main_table.subscriber_id=' . $this->getTable('panda_tags_relations') . '.entity_id',
            []
        );
        $select->where($this->getTable('panda_tags_relations') . '.entity_type = ?', 'subscribers');
        $select->where($this->getTable('panda_tags_relations') . '.entity_id IN(?)', $tags);

        $this->addGroupFilter();

        return $this;
    }

    /**
     * @param string $segments
     *
     * @return $this
     */
    public function addSegments($segments = '')
    {

        if (!$segments) {
            return $this;
        }

        if (!is_array($segments)) {
            $segments = explode(',', $segments);
        }

        $select = $this->getSelect();
        $select->joinInner(
            $this->getTable('panda_segments_records'),
            'main_table.email=' . $this->getTable('panda_segments_records') . '.email',
            []
        );
        $select->where($this->getTable('panda_segments_records') . '.segment_id IN (?)', $segments);

        $this->addGroupFilter();

        return $this;
    }

    /**
     * @param string $storeIds
     *
     * @return $this
     */
    public function addStoreIds($storeIds = '')
    {

        if (!$storeIds) {
            return $this;
        }

        if (!is_array($storeIds)) {
            $storeIds = explode(',', $storeIds);
        }
        $this->addFieldToFilter('main_table.store_id', ['in' => $storeIds]);

        return $this;
    }

    public function addGroupFilter()
    {

        $select = $this->getSelect();

        $select->reset('group');
        $select->group("main_table.subscriber_id");
    }

    /**
     * @param bool|false $field
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
        $idsSelect->columns('main_table.' . $field, 'main_table');

        return $this->getConnection()->fetchCol($idsSelect);
    }
}
