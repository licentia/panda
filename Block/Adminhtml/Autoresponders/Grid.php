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

namespace Licentia\Panda\Block\Adminhtml\Autoresponders;

/**
 * Adminhtml Campaigns grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected \Licentia\Panda\Helper\Data $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\AutorespondersFactory
     */
    protected \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory
     */
    protected \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory $campaignsCollection;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Events\CollectionFactory
     */
    protected \Licentia\Panda\Model\ResourceModel\Events\CollectionFactory $eventsCollection;

    /**
     * Grid constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                              $context
     * @param \Magento\Backend\Helper\Data                                         $backendHelper
     * @param \Licentia\Panda\Helper\Data                                          $pandaHelper
     * @param \Licentia\Panda\Model\ResourceModel\Autoresponders\CollectionFactory $collectionFactory
     * @param \Licentia\Panda\Model\ResourceModel\Events\CollectionFactory         $eventsCollection
     * @param \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory      $campaignsCollection
     * @param \Licentia\Panda\Model\AutorespondersFactory                          $autorespondersFactory
     * @param array                                                                $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\ResourceModel\Autoresponders\CollectionFactory $collectionFactory,
        \Licentia\Panda\Model\ResourceModel\Events\CollectionFactory $eventsCollection,
        \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory $campaignsCollection,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        array $data = []
    ) {

        $this->collectionFactory = $collectionFactory;
        $this->autorespondersFactory = $autorespondersFactory;
        $this->pandaHelper = $pandaHelper;
        $this->campaignsCollection = $campaignsCollection;
        $this->eventsCollection = $eventsCollection;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {

        parent::_construct();
        $this->setId('pandaCampaignsGrid');
        $this->setDefaultSort('autoresponder_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {

        $collection = $this->collectionFactory->create();
        /* @var $collection \Licentia\Panda\Model\ResourceModel\Campaigns\Collection */
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
            'autoresponder_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'autoresponder_id',
            ]
        );

        $this->addColumn(
            'event',
            [
                'header'  => __('Event'),
                'align'   => 'left',
                'index'   => 'event',
                'type'    => 'options',
                'options' => $this->autorespondersFactory->create()
                                                         ->toOptionArray(),
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'align'  => 'left',
                'index'  => 'name',
            ]
        );

        $this->addColumn(
            'total_messages',
            [
                'header' => __('N. Sends'),
                'align'  => 'right',
                'type'   => 'number',
                'index'  => 'total_messages',
            ]
        );

        $this->addColumn(
            'conversions_number',
            [
                'header' => __('Conversions'),
                'align'  => 'left',
                'width'  => '80px',
                'type'   => 'number',
                'index'  => 'conversions_number',
            ]
        );

        $this->addColumn(
            'conversions_amount',
            [
                'header'        => __('Conv. Amount'),
                'align'         => 'left',
                'width'         => '80px',
                'type'          => 'currency',
                'currency_code' => $this->_storeManager->getStore()
                                                       ->getStoreId(),
                'index'         => 'conversions_amount',
            ]
        );

        $this->addColumn(
            'is_active',
            [
                'header'  => __('Status'),
                'align'   => 'left',
                'width'   => '80px',
                'index'   => 'is_active',
                'type'    => 'options',
                'options' => ['0' => __('Inactive'), '1' => __('Active')],
            ]
        );

        $this->addColumn(
            'from_date',
            [
                'header'         => __('From Date'),
                'align'          => 'left',
                'width'          => '120px',
                'type'           => 'date',
                'default'        => '-- N/A --',
                'frame_callback' => [$this, 'date'],
                'index'          => 'from_date',
                'gmtoffset'      => true,
            ]
        );

        $this->addColumn(
            'to_date',
            [
                'header'         => __('To Date'),
                'align'          => 'left',
                'width'          => '120px',
                'type'           => 'date',
                'default'        => '-- N/A --',
                'frame_callback' => [$this, 'date'],
                'index'          => 'to_date',
                'gmtoffset'      => true,
            ]
        );

        $this->addColumn(
            'action',
            [
                'header'         => __('View'),
                'filter'         => false,
                'sortable'       => false,
                'frame_callback' => [$this, 'campaigns'],
                'index'          => 'is_active',
            ]
        );

        $this->addColumn(
            'action2',
            [
                'header'         => __('View'),
                'filter'         => false,
                'sortable'       => false,
                'frame_callback' => [$this, 'events'],
                'index'          => 'is_active',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {

        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $item
     *
     * @return string
     */
    public function getRowUrl($item)
    {

        return $this->getUrl('*/*/edit', ['id' => $item->getId()]);
    }

    /**
     * @param $value
     * @param $row
     *
     * @return string
     */
    public function events($value, $row)
    {

        if (!$value) {
            return '';
        }
        $url = $this->getUrl('*/events/index', ['id' => $row->getAutoresponderId()]);

        $total = $this->eventsCollection->create()
                                        ->addFieldToFilter('executed', 0)
                                        ->addFieldToFilter('autoresponder_id', $row->getAutoresponderId())
                                        ->getSize();

        return "<a href='$url'>" . __('Queue') . " ($total)</a>";
    }

    /**
     * @param $value
     * @param $row
     *
     * @return string
     */
    public function campaigns($value, $row)
    {

        if (!$value) {
            return '';
        }
        $url = $this->getUrl('*/events/campaigns', ['id' => $row->getAutoresponderId()]);

        $total = $this->campaignsCollection->create()
                                           ->addFieldToFilter('status', ['neq' => 'finished'])
                                           ->addFieldToFilter('autoresponder_id', $row->getAutoresponderId())
                                           ->getSize();

        return "<a href='$url'>" . __('Campaigns') . " ($total)</a>";
    }

    /**
     * @param $value
     * @param $row
     *
     * @return string
     */
    public function date($value, $row)
    {

        if (!$row->getId()) {
            return '';
        }

        return $value;
    }
}
