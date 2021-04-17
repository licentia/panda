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
 * Class Forms
 *
 * @package Licentia\Panda\Block\Adminhtml\Subscriber\Edit\Tab
 */
class Forms extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Licentia\Panda\Model\ResourceModel\FormEntries\CollectionFactory
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
    protected $coreRegistry = null;

    /**
     * @var \Licentia\Forms\Model\FormsFactory
     */
    protected $formsFactory;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context                           $context
     * @param \Magento\Backend\Helper\Data                                      $backendHelper
     * @param \Licentia\Forms\Model\FormsFactory                                $formsFactory
     * @param \Licentia\Forms\Model\ResourceModel\FormEntries\CollectionFactory $collectionFactory
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface  $pageLayoutBuilder
     * @param \Magento\Framework\Registry                                       $registry
     * @param array                                                             $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Forms\Model\FormsFactory $formsFactory,
        \Licentia\Forms\Model\ResourceModel\FormEntries\CollectionFactory $collectionFactory,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->formsFactory = $formsFactory;
        $this->coreRegistry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {

        parent::_construct();
        $this->setId('panda_formElements_grid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('ASC');
        $this->setSortable(false);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {

        $current = $this->coreRegistry->registry('panda_subscriber');

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('subscriber_id', $current->getId());

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
            'form_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'form_id',
            ]
        );

        $this->addColumn(
            'form_id',
            [
                'header'  => __('Form Name'),
                'align'   => 'left',
                'index'   => 'form_id',
                'type'    => 'options',
                'options' => $this->formsFactory->create()
                                                ->toFormValues(),
            ]
        );

        $this->addColumn(
            'entries',
            [
                'header'         => __('Entries'),
                'type'           => 'action',
                'width'          => '50px',
                'filter'         => false,
                'system'         => true,
                'sortable'       => false,
                'frame_callback' => [$this, 'entriesResult'],
                'index'          => 'type',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @param $value
     * @param $row
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function entriesResult($value, $row)
    {

        $url = $this->getUrl('pandaf/forms/entries',
            [
                'id'            => $row->getData('form_id'),
                'subscriber_id' => $row->getData('subscriber_id'),
            ]);

        return '<a href="' . $url . '">' . __('Yes') . '</a>';

    }

}
