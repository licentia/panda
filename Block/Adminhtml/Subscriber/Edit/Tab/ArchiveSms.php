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
 * Class ArchiveSms
 *
 * @package Licentia\Panda\Block\Adminhtml\Subscriber\Edit\Tab
 */
class ArchiveSms extends \Magento\Backend\Block\Widget\Grid\Extended
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
     *
     * @param \Magento\Backend\Block\Template\Context                          $context
     * @param \Magento\Backend\Helper\Data                                     $backendHelper
     * @param \Licentia\Panda\Model\ResourceModel\Archive\CollectionFactory    $collectionFactory
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     * @param \Magento\Framework\Registry                                      $registry
     * @param \Licentia\Panda\Model\Campaigns                                  $campaigns
     * @param array                                                            $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Model\ResourceModel\Archive\CollectionFactory $collectionFactory,
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
        $this->setId('pandaArchiveGrid');
        $this->setDefaultSort('archive_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {

        $current = $this->registry->registry('panda_subscriber');

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('subscriber_id', $current->getId());
        $collection->addFieldToFilter('type', 'sms');

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
            'campaign_id',
            [
                'header'  => __('Campaign Name'),
                'align'   => 'left',
                'index'   => 'campaign_id',
                'type'    => 'options',
                'options' => $this->campaignsList->toFormValues('sms'),
            ]
        );

        $this->addColumn(
            'sender_name',
            [
                'header' => __('Sender name'),
                'align'  => 'left',
                'index'  => 'sender_name',
            ]
        );

        $this->addColumn(
            'message',
            [
                'header' => __('Message'),
                'align'  => 'left',
                'index'  => 'message',
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
     * @return string
     */
    public function getGridUrl()
    {

        $current = $this->registry->registry('panda_subscriber');

        return $this->getUrl(
            '*/subscriber/archivesmsgrid',
            ['_current' => true, 'id' => $current->getId()]
        );
    }
}
