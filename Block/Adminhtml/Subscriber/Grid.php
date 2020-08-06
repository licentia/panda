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

namespace Licentia\Panda\Block\Adminhtml\Subscriber;

/**
 * Class Grid
 *
 * @package Licentia\Panda\Block\Adminhtml\Subscriber
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Subscribers\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Licentia\Panda\Model\Subscribers
     */
    protected $subscribersFactory;

    /**
     * @var \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface
     */
    protected $pageLayoutBuilder;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context                           $context
     * @param \Magento\Backend\Helper\Data                                      $backendHelper
     * @param \Licentia\Panda\Model\Subscribers                                 $subscribersFactory
     * @param \Licentia\Panda\Helper\Data                                       $pandaHelper
     * @param \Licentia\Panda\Model\ResourceModel\Subscribers\CollectionFactory $collectionFactory
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface  $pageLayoutBuilder
     * @param array                                                             $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Model\Subscribers $subscribersFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\ResourceModel\Subscribers\CollectionFactory $collectionFactory,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        array $data = []
    ) {

        $this->collectionFactory = $collectionFactory;
        $this->subscribersFactory = $subscribersFactory;
        $this->pandaHelper = $pandaHelper;
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {

        parent::_construct();
        $this->setId('pandaSubscriberGrid');
        $this->setDefaultSort('subscriber_id');
        $this->setDefaultDir('ASC');
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
        /* @var $collection \Licentia\Panda\Model\ResourceModel\Subscribers\Collection */
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
            'subscriber_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'subscriber_id',
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
            'firstname',
            [
                'header' => __('Sub. First Name'),
                'align'  => 'left',
                'index'  => 'firstname',
            ]
        );

        $this->addColumn(
            'lastname',
            [
                'header' => __('Sub. Last Name'),
                'align'  => 'left',
                'index'  => 'lastname',
            ]
        );

        $this->addColumn(
            'email',
            [
                'header' => __('Email'),
                'align'  => 'left',
                'index'  => 'email',
            ]
        );

        $this->addColumn(
            'status',
            [
                'header'  => __('Status'),
                'type'    => 'options',
                'align'   => 'left',
                'options' => [0 => __('Unsubscribed'), 1 => __('Active')],
                'index'   => 'status',
            ]
        );

        $this->addColumn(
            'sent',
            [
                'header' => __('Emails Sent'),
                'align'  => 'left',
                'index'  => 'sent',
                'type'   => 'number',
                'width'  => '40px',
            ]
        );

        $this->addColumn(
            'views',
            [
                'header' => __('U. Views'),
                'align'  => 'left',
                'index'  => 'views',
                'type'   => 'number',
                'width'  => '50px',
            ]
        );

        $this->addColumn(
            'bounces',
            [
                'header' => __('Bounces'),
                'align'  => 'left',
                'index'  => 'bounces',
                'type'   => 'number',
                'width'  => '50px',
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
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->getMassactionBlock()->setTemplate('Licentia_Panda::widget/grid/massaction_extended.phtml');

        $this->setMassactionIdField('subscriber_id');
        $this->getMassactionBlock()->setFormFieldName('subscribers');

        $this->getMassactionBlock()
             ->addItem(
                 'unsubscribe',
                 [
                     'label'   => __('Unsubscribe'),
                     'url'     => $this->getUrl('*/*/massUnsubscribe'),
                     'confirm' => __('Are you sure?'),
                 ]
             );

        $this->getMassactionBlock()
             ->addItem(
                 'subscribe',
                 [
                     'label'   => __('Subscribe'),
                     'url'     => $this->getUrl('*/*/massSubscribe'),
                     'confirm' => __('Are you sure?'),
                 ]
             );

        $this->getMassactionBlock()
             ->addItem(
                 'delete',
                 [
                     'label'   => __('Delete'),
                     'url'     => $this->getUrl('*/*/massDelete'),
                     'confirm' => __('Are you sure?'),
                 ]
             );

        return $this;
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
            return '<style>.totals .massaction-checkbox{display:none;}</style>';
        }

        if ((int) $value > 0) {
            $url = $this->getUrl('customer/index/edit', ['id' => $value]);

            return '<a href="' . $url . '">' . __('Yes') . '</a>';
        }

        return __('No');
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
