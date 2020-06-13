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

namespace Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab;

/**
 * Class Links
 *
 * @package Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab
 */
class Links extends \Magento\Backend\Block\Widget\Grid\Extended
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
     * @param \Magento\Backend\Block\Template\Context                     $context
     * @param \Magento\Backend\Helper\Data                                $backendHelper
     * @param \Licentia\Panda\Model\ResourceModel\Links\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry                                 $registry
     * @param array                                                       $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Model\ResourceModel\Links\CollectionFactory $collectionFactory,
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
        $this->setId('links_grid');
        $this->setDefaultSort('link_id');
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
            'link_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'link_id',
            ]
        );

        $this->addColumn(
            'link',
            [
                'header' => __('Url'),
                'index'  => 'link',
            ]
        );

        $this->addColumn(
            'clicks',
            [
                'header' => __('Clicks'),
                'index'  => 'clicks',
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
                                                       ->getDefaultCurrencyCode(),
                'index'         => 'conversions_amount',
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

        return $this->getUrl('*/campaigns/links', ['_current' => true]);
    }
}
