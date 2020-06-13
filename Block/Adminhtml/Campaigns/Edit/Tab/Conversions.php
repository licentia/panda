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
 * Class Conversions
 *
 * @package Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab
 */
class Conversions extends \Magento\Backend\Block\Widget\Grid\Extended
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
     * @param \Magento\Backend\Block\Template\Context                           $context
     * @param \Magento\Backend\Helper\Data                                      $backendHelper
     * @param \Licentia\Panda\Model\ResourceModel\Conversions\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry                                       $registry
     * @param array                                                             $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Model\ResourceModel\Conversions\CollectionFactory $collectionFactory,
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
        $this->setId('campaign_conversion_edit_grid');
        $this->setDefaultSort('order_date');
        $this->setDefaultDir('DESC');
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
            'conversion_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'conversion_id',
            ]
        );

        $this->addColumn(
            'subscriber_email',
            [
                'header' => __('Email'),
                'align'  => 'left',
                'index'  => 'subscriber_email',
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
            'order_date',
            [
                'header' => __('Date'),
                'align'  => 'left',
                'index'  => 'order_date',
                'width'  => '170px',
                'type'   => 'datetime',
            ]
        );

        $this->addColumn(
            'order_amount',
            [
                'header'        => __('Order Amount'),
                'type'          => 'currency',
                'currency_code' => $this->_storeManager->getStore()
                                                       ->getDefaultCurrencyCode(),
                'index'         => 'order_amount',
            ]
        );

        $this->addColumn(
            'action',
            [
                'header'   => __('Order'),
                'type'     => 'action',
                'width'    => '75px',
                'filter'   => false,
                'align'    => 'center',
                'sortable' => false,
                'actions'  => [
                    [
                        'url'     => $this->getUrl('sales/order/view', ['order_id' => '$order_id']),
                        'caption' => __('View'),
                    ],
                ],
                'index'    => 'type',
            ]
        );

        $this->addColumn(
            'customer_id',
            [
                'header'         => __('Customer'),
                'align'          => 'center',
                'width'          => '75px',
                'index'          => 'customer_id',
                'filter'         => false,
                'sortable'       => false,
                'frame_callback' => [$this, 'customerResult'],
            ]
        );

        $this->addColumn(
            'subscriber_id',
            [
                'header'         => __('Subscriber'),
                'align'          => 'center',
                'filter'         => false,
                'sortable'       => false,
                'width'          => '75px',
                'index'          => 'subscriber_id',
                'frame_callback' => [$this, 'subscriberResult'],
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
     * @param $value
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function customerResult($value)
    {

        if ((int) $value > 0) {
            $url = $this->getUrl('*/customer/edit', ['id' => $value]);

            return '<a href="' . $url . '">' . __('View') . '</a>';
        }

        return __('No');
    }

    /**
     * @param $value
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function subscriberResult($value)
    {

        if ((int) $value > 0) {
            $url = $this->getUrl('*/panda_subscribers/edit', ['id' => $value]);

            return '<a href="' . $url . '">' . __('View') . '</a>';
        }

        return __('No');
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {

        return $this->getUrl('*/campaigns/gridconv', ['_current' => true]);
    }
}
