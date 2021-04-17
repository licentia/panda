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

namespace Licentia\Panda\Block\Adminhtml\Subscriber\Edit\Tab;

/**
 * Class Bounces
 *
 * @package Licentia\Panda\Block\Adminhtml\Subscriber\Edit\Tab
 */
class Bounces extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Subscribers\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface
     */
    protected $pageLayoutBuilder;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     *
     * @var \Licentia\Panda\Model\Campaigns
     */
    protected $campaignsList;

    /**
     * Bounces constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                          $context
     * @param \Magento\Backend\Helper\Data                                     $backendHelper
     * @param \Licentia\Panda\Model\ResourceModel\Bounces\CollectionFactory    $collectionFactory
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     * @param \Magento\Framework\Registry                                      $registry
     * @param \Licentia\Panda\Model\Campaigns                                  $campaigns
     * @param array                                                            $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Model\ResourceModel\Bounces\CollectionFactory $collectionFactory,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\Campaigns $campaigns,
        array $data = []
    ) {

        $this->campaignsList = $campaigns;
        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {

        parent::_construct();
        $this->setId('pandaBouncesGrid');
        $this->setDefaultSort('bounce_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {

        $current = $this->registry->registry('panda_subscriber');
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('subscriber_id', $current->getId());

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
            'bounce_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'bounce_id',
            ]
        );

        $this->addColumn(
            'campaign_id',
            [
                'header'  => __('Campaign Name'),
                'align'   => 'left',
                'index'   => 'campaign_id',
                'type'    => 'options',
                'options' => $this->campaignsList->toFormValues(),
            ]
        );

        $this->addColumn(
            'code',
            [
                'header' => __('Code'),
                'align'  => 'left',
                'index'  => 'code',
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header'    => __('Date'),
                'align'     => 'left',
                'index'     => 'created_at',
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

        $current = $this->registry->registry('panda_subscriber');

        return $this->getUrl('*/*/bouncesgrid', ['_current' => true, 'id' => $current->getId()]);
    }
}
