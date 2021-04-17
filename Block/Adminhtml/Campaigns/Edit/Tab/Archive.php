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
 * Class Archive
 *
 * @package Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab
 */
class Archive extends \Magento\Backend\Block\Widget\Grid\Extended
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
     * @param \Magento\Backend\Block\Template\Context                       $context
     * @param \Magento\Backend\Helper\Data                                  $backendHelper
     * @param \Licentia\Panda\Model\ResourceModel\Archive\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry                                   $registry
     * @param array                                                         $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Model\ResourceModel\Archive\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    public function _construct()
    {

        parent::_construct();
        $this->setId('archive_grid');
        $this->setDefaultSort('archive_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
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
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
            'archive_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'archive_id',
            ]
        );

        $this->addColumn(
            'subject_archive',
            [
                'header' => __('Subject'),
                'align'  => 'left',
                'index'  => 'subject',
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Sub. Name'),
                'align'  => 'left',
                'index'  => 'name',
            ]
        );

        $this->addColumn(
            'email',
            [
                'header' => __('Sub. Email'),
                'align'  => 'left',
                'index'  => 'email',
            ]
        );

        $this->addColumn(
            'sent_date',
            [
                'header'    => __('Date'),
                'align'     => 'left',
                'index'     => 'sent_date',
                'width'     => '170px',
                'type'      => 'datetime',
                'gmtoffset' => true,
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

        return $this->getUrl('*/*/archivegrid', ['_current' => true]);
    }
}
