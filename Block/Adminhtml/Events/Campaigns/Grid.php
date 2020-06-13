<?php

/**
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

namespace Licentia\Panda\Block\Adminhtml\Events\Campaigns;

/**
 * Class Grid
 *
 * @package Licentia\Panda\Block\Adminhtml\Events\Campaigns
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Archive\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected $autorespondersFactory;

    /**
     * @var \Licentia\Panda\Model\TemplatesFactory
     */
    protected $templatesFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context                         $context
     * @param \Magento\Backend\Helper\Data                                    $backendHelper
     * @param \Licentia\Panda\Model\AutorespondersFactory                     $autorespondersFactory
     * @param \Licentia\Panda\Model\TemplatesFactory                          $templatesFactory
     * @param \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry                                     $registry
     * @param array                                                           $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        \Licentia\Panda\Model\TemplatesFactory $templatesFactory,
        \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->autorespondersFactory = $autorespondersFactory;
        $this->templatesFactory = $templatesFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    public function _construct()
    {

        parent::_construct();
        $this->setId('events_campaigns_grid');
        $this->setDefaultSort('event_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {

        $collection = $this->collectionFactory->create();

        $current = $this->registry->registry('panda_autoresponder');

        $collection->addFieldToFilter('autoresponder_id', ['gt' => 0]);

        if ($current->getId()) {
            $collection->addFieldToFilter('autoresponder_id', $current->getId());
        }

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
            'campaign_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'campaign_id',
            ]
        );

        $this->addColumn(
            'autoresponder_event',
            [
                'header'  => __('Event Type'),
                'index'   => 'autoresponder_event',
                'type'    => 'options',
                'options' => $this->autorespondersFactory->create()
                                                         ->toOptionArray(),
            ]
        );

        $this->addColumn(
            'internal_name',
            [
                'header' => __('Name'),
                'align'  => 'left',
                'index'  => 'internal_name',
            ]
        );

        $this->addColumn(
            'template_id',
            [
                'header'  => __('Template'),
                'index'   => 'template_id',
                'type'    => 'options',
                'options' => $this->templatesFactory->create()
                                                    ->toFormValues(),
            ]
        );

        /*
          $this->addColumn('subject', array(
          'header' => __('Subject'),
          'align' => 'left',
          'index' => 'subject',
          ));
         */
        $this->addColumn(
            'autoresponder_recipient',
            [
                'header' => __('Recipient'),
                'align'  => 'left',
                'index'  => 'autoresponder_recipient',
            ]
        );

        $this->addColumn(
            'status',
            [
                'header'         => __('Status'),
                'align'          => 'left',
                'width'          => '80px',
                'frame_callback' => [$this, 'serviceResult'],
                'index'          => 'status',
            ]
        );

        $this->addColumn(
            'recurring_next_run',
            [
                'header'    => __('Send Date'),
                'align'     => 'left',
                'width'     => '180px',
                'type'      => 'datetime',
                'gmtoffset' => true,
                'default'   => '-NA-',
                'index'     => 'recurring_next_run',
            ]
        );

        $this->addColumn(
            'conversions_amount',
            [
                'header'        => __('Conv. Amount'),
                'align'         => 'left',
                'width'         => '80px',
                'default'       => '-NA-',
                'type'          => 'currency',
                'currency_code' => $this->_storeManager->getStore()
                                                       ->getDefaultCurrencyCode(),
                'index'         => 'main_table.conversions_amount',
            ]
        );

        $this->addColumn(
            'views',
            [
                'header'  => __('Views'),
                'align'   => 'right',
                'default' => '-NA-',
                'index'   => 'views',
                'width'   => '20px',
            ]
        );

        $this->addColumn(
            'clicks',
            [
                'header'  => __('Clicks'),
                'align'   => 'right',
                'default' => '-NA-',
                'index'   => 'clicks',
                'width'   => '20px',
            ]
        );

        $this->addColumn(
            'conv',
            [
                'header'   => __('Detail'),
                'type'     => 'action',
                'width'    => '50px',
                'filter'   => false,
                'sortable' => false,
                'actions'  => [
                    [
                        'url'     => $this->getUrl('*/campaigns/conversions', ['id' => '$campaign_id']),
                        'caption' => __('Conversions'),
                    ],
                ],
                'index'    => 'type',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     *
     * @return bool
     */
    public function getRowUrl($row)
    {

        return false;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {

        return $this->getUrl('*/*/campaignsgrid', ['_current' => true]);
    }

    /**
     * @param $value
     * @param $row
     *
     * @return string
     */
    public function statsResult($value, $row)
    {

        return $row->getData('views') . ' / ' . $row->getData('clicks');
    }

    /**
     * @param $value
     * @param $row
     *
     * @return string
     */
    public function serviceResult($value, $row)
    {

        if ($value == "standby") {
            return ' <span class="grid-severity-minor"><span>' . __('Stand By') . '</span></span>';
        }

        if ($value == "running") {
            return ' <span class="grid-severity-major"><span>' . __('Running') . '</span></span>';
        }

        if ($value == "finished") {
            return ' <span class="grid-severity-notice"><span>' . __('Finished') . '</span></span>';
        }

        if ($value == "error") {
            return ' <span class="grid-severity-critical"><span>' . __('Error') . ' (' .
                   $row->getServiceResponse() . ')</span></span>';
        }

        return '';
    }
}
