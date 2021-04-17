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

namespace Licentia\Panda\Block\Adminhtml\Senders;

/**
 * Class Grid
 *
 * @package Licentia\Panda\Block\Adminhtml\Senders
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory
     */
    protected \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory $collectionFactory;

    /**
     * @var \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface
     */
    protected \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder;

    /**
     * @param \Magento\Backend\Block\Template\Context                          $context
     * @param \Magento\Backend\Helper\Data                                     $backendHelper
     * @param \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory    $collectionFactory
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     * @param array                                                            $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory $collectionFactory,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        array $data = []
    ) {

        $this->collectionFactory = $collectionFactory;
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Row click url
     *
     * @param \Magento\Framework\DataObject $item
     *
     * @return string
     */
    public function getRowUrl($item)
    {

        return $this->getUrl('*/*/edit', ['id' => $item->getId()]);
    }

    protected function _construct()
    {

        parent::_construct();
        $this->setId('pandaSendersGrid');
        $this->setDefaultSort('sender_id');
        $this->setDefaultDir('ASC');
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
        /* @var $collection \Licentia\Panda\Model\ResourceModel\Senders\Collection */
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {

        $this->addColumn('sender_id',
            [
                'header' => __('ID'),
                'index'  => 'sender_id',
            ]);

        $this->addColumn('type',
            [
                'header'  => __('Sender Type'),
                'index'   => 'type',
                'type'    => 'options',
                'options' => [
                    'email' => 'Email',
                    'sms'   => 'SMS',
                ],
            ]);

        $this->addColumn('name',
            [
                'header' => __('Name'),
                'index'  => 'name',
            ]);

        $this->addColumn('email',
            [
                'header' => __('Email Sender'),
                'index'  => 'email',
            ]);

        #$this->addColumn('originator', ['header' => __('SMS Originator'), 'index' => 'originator']);

        return parent::_prepareColumns();
    }
}
