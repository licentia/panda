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

namespace Licentia\Panda\Block\Adminhtml\Conversions;

/**
 * Class Grid
 *
 * @package Licentia\Panda\Block\Adminhtml\Conversions
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
    protected $campaignsFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context                           $context
     * @param \Magento\Backend\Helper\Data                                      $backendHelper
     * @param \Licentia\Panda\Helper\Data                                       $pandaHelper
     * @param \Licentia\Panda\Model\CampaignsFactory                            $campaignsFactory
     * @param \Licentia\Panda\Model\ResourceModel\Conversions\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry                                       $registry
     * @param array                                                             $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\ResourceModel\Conversions\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->campaignsFactory = $campaignsFactory;
        $this->pandaHelper = $pandaHelper;
        parent::__construct($context, $backendHelper, $data);
    }

    public function _construct()
    {

        parent::_construct();
        $this->setId('panda_conversions_grid');
        $this->setDefaultSort('conversion_id');
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
            'campaign_id',
            [
                'header'  => __('Campaign Name'),
                'align'   => 'left',
                'index'   => 'campaign_id',
                'type'    => 'options',
                'options' => $this->campaignsFactory->create()
                                                    ->toFormValues('email'),
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
                'header'         => __('Order'),
                'width'          => '75px',
                'filter'         => false,
                'align'          => 'center',
                'sortable'       => false,
                'index'          => 'order_id',
                'frame_callback' => [$this, 'orders'],
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
                'is_system'      => true,
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
                'is_system'      => true,
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
    public function orders($value, $row)
    {

        if (!$value) {
            return '';
        }
        $url = $this->getUrl('*/sales_order/view', ['order_id' => $row->getOrderId()]);

        return "<a href='$url'>" . __('View') . " </a>";
    }

    /**
     * @param $value
     * @param $row
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function customerResult($value, $row)
    {

        if (!$row->getId()) {
            return '';
        }
        if ((int) $value > 0) {
            $url = $this->getUrl('customer/edit', ['id' => $value]);

            return '<a href="' . $url . '">' . __('View') . '</a>';
        }

        return __('No');
    }

    /**
     * @param $value
     * @param $row
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function subscriberResult($value, $row)
    {

        if (!$row->getId()) {
            return '';
        }
        if ((int) $value > 0) {
            $url = $this->getUrl('*/subscriber/edit', ['id' => $value]);

            return '<a href="' . $url . '">' . __('View') . '</a>';
        }

        return __('No');
    }
}
