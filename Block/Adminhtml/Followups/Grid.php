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

namespace Licentia\Panda\Block\Adminhtml\Followups;

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
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context                        $context
     * @param \Magento\Backend\Helper\Data                                   $backendHelper
     * @param \Licentia\Panda\Helper\Data                                    $pandaHelper
     * @param \Licentia\Panda\Model\ResourceModel\Followup\CollectionFactory $collectionFactory
     * @param \Licentia\Panda\Model\CampaignsFactory                         $campaignsFactory
     * @param array                                                          $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\ResourceModel\Followup\CollectionFactory $collectionFactory,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        array $data = []
    ) {

        $this->collectionFactory = $collectionFactory;
        $this->pandaHelper = $pandaHelper;
        $this->campaignsFactory = $campaignsFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {

        parent::_construct();
        $this->setId('pandaFollowupsGrid');
        $this->setDefaultSort('followup_id');
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
            'followup_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'followup_id',
            ]
        );

        $this->addColumn(
            'campaign_id',
            [
                'header'  => __('Campaign'),
                'align'   => 'left',
                'index'   => 'campaign_id',
                'type'    => 'options',
                'options' => $this->campaignsFactory->create()
                                                    ->toFormValues('email'),
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
            'send_at',
            [
                'header'    => __('Send At'),
                'align'     => 'left',
                'type'      => 'datetime',
                'gmtoffset' => true,
                'index'     => 'send_at',
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
            'sent',
            [
                'header'  => __('Finished'),
                'align'   => 'left',
                'width'   => '80px',
                'index'   => 'sent',
                'type'    => 'options',
                'options' => ['0' => __('No'), '1' => __('Yes')],
            ]
        );

        $this->addColumn(
            'action',
            [
                'header'   => __('Campaign'),
                'type'     => 'action',
                'align'    => 'center',
                'system'   => true,
                'filter'   => false,
                'sortable' => false,
                'actions'  => [
                    [
                        'url'     => $this->getUrl('*/campaigns/edit', ['id' => '$campaign_id']),
                        'caption' => __('View'),
                    ],
                ],
                'index'    => 'type',
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
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {

        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
