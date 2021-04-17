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

namespace Licentia\Panda\Block\Adminhtml\Campaigns;

/**
 * Adminhtml Campaigns grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory
     */
    protected \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory $collectionFactory;

    /**
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected \Licentia\Panda\Model\CampaignsFactory $campaignsFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected \Licentia\Panda\Helper\Data $pandaHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context                         $context
     * @param \Magento\Backend\Helper\Data                                    $backendHelper
     * @param \Licentia\Panda\Helper\Data                                     $pandaHelper
     * @param \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory $collectionFactory
     * @param \Licentia\Panda\Model\CampaignsFactory                          $campaignsFactory
     * @param array                                                           $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory $collectionFactory,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        array $data = []
    ) {

        $this->collectionFactory = $collectionFactory;
        $this->campaignsFactory = $campaignsFactory;
        $this->pandaHelper = $pandaHelper;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {

        parent::_construct();
        $this->setId('pandaCampaignsGrid');
        $this->setDefaultSort('campaign_id');
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
        /* @var \Licentia\Panda\Model\ResourceModel\Campaigns\Collection $collection */

        $collection->addFieldToFilter('auto', '0')
                   ->addFieldToFilter('parent_id', ['null' => true]);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {

        /** @var \Licentia\Panda\Model\Campaigns $campaign */
        $campaign = $this->campaignsFactory->create();
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
            'internal_name',
            [
                'header' => __('Name'),
                'align'  => 'left',
                'index'  => 'internal_name',
            ]
        );

        $this->addColumn(
            'recurring_next_run',
            [
                'header'         => __('Next Run'),
                'align'          => 'left',
                'width'          => '180px',
                'type'           => 'datetime',
                'gmtoffset'      => true,
                'frame_callback' => [$this, 'lastRun'],
                'index'          => 'recurring_next_run',
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
            'recurring',
            [
                'header'  => __('Recurring'),
                'align'   => 'left',
                'width'   => '80px',
                'index'   => 'recurring',
                'type'    => 'options',
                'options' => $campaign::getCronList(),
            ]
        );

        $this->addColumn(
            'recurring_last_run',
            [
                'header'         => __('Last Run'),
                'align'          => 'left',
                'width'          => '120px',
                'frame_callback' => [$this, 'lastRun'],
                'index'          => 'recurring_last_run',
                'type'           => 'date',
                'gmtoffset'      => true,
            ]
        );

        $this->addColumn(
            'conversions_amount',
            [
                'header'        => __('Conversions'),
                'align'         => 'left',
                'width'         => '80px',
                'type'          => 'currency',
                'currency_code' => $this->_storeManager->getStore()
                                                       ->getBaseCurrencyCode(),
                'index'         => 'conversions_amount',
            ]
        );

        $this->addColumn(
            'sent',
            [
                'header'         => __('Sent'),
                'align'          => 'right',
                'index'          => 'sent',
                'width'          => '80px',
                'frame_callback' => [$this, 'sentResult'],
            ]
        );

        $this->addColumn(
            'unique_views',
            [
                'header' => __('U. Views'),
                'align'  => 'right',
                'index'  => 'unique_views',
                'width'  => '20px',
            ]
        );

        $this->addColumn(
            'unique_clicks',
            [
                'header' => __('U. Clicks'),
                'align'  => 'right',
                'index'  => 'unique_clicks',
                'width'  => '20px',
            ]
        );

        $this->addColumn(
            'action',
            [
                'header'         => __('View'),
                'frame_callback' => [$this, 'reports'],
                'width'          => '50px',
                'filter'         => false,
                'sortable'       => false,
                'index'          => 'campaign_id',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $item
     *
     * @return string
     */
    public function getRowUrl($item)
    {

        return $this->getUrl('*/*/edit', ['id' => $item->getCampaignId()]);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {

        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * @param $value
     * @param $row
     *
     * @return string
     */
    public function reports($value, $row)
    {

        if (!$value) {
            return '';
        }
        $url = $this->getUrl('*/reports/detail', ['id' => $row->getCampaignId()]);

        return "<a href='$url'>" . __('Reports') . " </a>";
    }

    /**
     * @param $value
     * @param $row
     *
     * @return mixed
     */
    public function lastRun($value, $row)
    {

        if (!$row->getId()) {
            return '';
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function serviceResult($value)
    {

        if ($value == "standby") {
            return ' <span class="grid-severity-minor"><span>' . __('Stand By') . '</span></span>';
        }

        if ($value == "queuing") {
            return ' <span class="grid-severity-major"><span>' . __('Queuing') . '</span></span>';
        }

        if ($value == "running") {
            return ' <span class="grid-severity-major"><span>' . __('Running') . '</span></span>';
        }

        if ($value == "finished") {
            return ' <span class="grid-severity-notice"><span>' . __('Finished') . '</span></span>';
        }
    }

    /**
     * @param $value
     * @param $row
     *
     * @return string
     */
    public function sentResult($value, $row)
    {

        if (!$row->getId()) {
            return $value;
        }

        $return = $value . ' /' . $row->getData('unsent');
        if ($value < (int) $row->getData('unsent') && $row->getData('status') == 'finished') {
            $url = $this->getUrl('*/errors/', ['id' => $row->getData('campaign_id')]);

            return '<a href="' . $url . '">' . $return . '</a>';
        }

        return $return;
    }
}
