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

namespace Licentia\Panda\Block\Adminhtml\Urls;

/**
 * Class Grid
 *
 * @package Licentia\Panda\Block\Adminhtml\Urls
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
    protected ?\Magento\Framework\Registry $registry = null;

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
        $this->setId('urls_grid');
        $this->setDefaultSort('url_id');
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

        if ($id = $this->getRequest()->getParam('id')) {
            $collection->addFieldToFilter('link_id', $id);
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
            'url_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'url_id',
            ]
        );

        $this->addColumn(
            'customer_id',
            [
                'header'         => __('Customer'),
                'align'          => 'center',
                'width'          => '50px',
                'index'          => 'customer_id',
                'frame_callback' => [$this, 'customerResult'],
                'is_system'      => true,
            ]
        );

        $this->addColumn(
            'subscriber_firstname',
            [
                'header' => __('Sub. First Name'),
                'align'  => 'left',
                'index'  => 'subscriber_firstname',
            ]
        );

        $this->addColumn(
            'subscriber_lastname',
            [
                'header' => __('Sub. Last Name'),
                'align'  => 'left',
                'index'  => 'subscriber_lastname',
            ]
        );

        $this->addColumn(
            'subscriber_email',
            [
                'header' => __('Email'),
                'index'  => 'subscriber_email',
            ]
        );

        $this->addColumn(
            'visit_at',
            [
                'header'    => __('Visit time'),
                'align'     => 'left',
                'width'     => '170px',
                'type'      => 'datetime',
                'index'     => 'visit_at',
                'gmtoffset' => true,
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
     * @param $value
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function customerResult($value)
    {

        if ((int) $value > 0) {
            $url = $this->getUrl('customer/index/edit', ['id' => $value]);

            return '<a href="' . $url . '">' . __('Yes') . '</a>';
        }

        return __('No');
    }
}
