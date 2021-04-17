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

namespace Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab;

/**
 * Class Followup
 *
 * @package Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab
 */
class Followup extends \Magento\Backend\Block\Widget\Grid\Extended
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
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context                        $context
     * @param \Magento\Backend\Helper\Data                                   $backendHelper
     * @param \Licentia\Panda\Helper\Data                                    $pandaHelper
     * @param \Licentia\Panda\Model\ResourceModel\Followup\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry                                    $registry
     * @param array                                                          $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\ResourceModel\Followup\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->pandaHelper = $pandaHelper;
        parent::__construct($context, $backendHelper, $data);
    }

    public function _construct()
    {

        parent::_construct();
        $this->setId('campaign_folowup_grid');
        $this->setDefaultSort('followup_id');
        $this->setDefaultDir('DESC');
        $this->setFilterVisibility(false);
        $this->setSortable(false);
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {

        $current = $this->registry->registry('panda_campaign');

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('campaign_id', $current->getId());

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
                'header'  => __('Is Active?'),
                'align'   => 'left',
                'width'   => '80px',
                'index'   => 'is_active',
                'type'    => 'options',
                'options' => ['0' => __('No'), '1' => __('Yes')],
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {

        return $this->getUrl('*/followups/edit', ['id' => $row->getId()]);
    }
}
