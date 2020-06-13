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

namespace Licentia\Panda\Block\Adminhtml\TemplatesGlobal\Edit\Tab;

/**
 * Class Grid
 *
 * @package Licentia\Panda\Block\Adminhtml\TemplatesGlobal
 */
class Variations extends \Magento\Backend\Block\Widget\Grid\Extended
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
     * Grid constructor.
     *
     * @param \Magento\Framework\Registry                                           $registry
     * @param \Magento\Backend\Block\Template\Context                               $context
     * @param \Magento\Backend\Helper\Data                                          $backendHelper
     * @param \Licentia\Panda\Model\ResourceModel\TemplatesGlobal\CollectionFactory $collectionFactory
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface      $pageLayoutBuilder
     * @param array                                                                 $data
     *
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Model\ResourceModel\TemplatesGlobal\CollectionFactory $collectionFactory,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        array $data = []
    ) {

        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Row click url
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {

        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }

    protected function _construct()
    {

        parent::_construct();
        $this->setId('pandaTemplatesGlobalGrid');
        $this->setDefaultSort('template_id');
        $this->setDefaultDir('DESC');
        $this->setFilterVisibility(false);
        $this->setSortable(false);
        $this->setPagerVisibility(false);
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {

        $model = $this->registry->registry('panda_template_global');

        /* @var $collection \Licentia\Panda\Model\ResourceModel\TemplatesGlobal\Collection */
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter('parent_id', $model->getId());

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
            'template_id',
            [
                'header' => __('ID'),
                'width'  => '50px',
                'index'  => 'template_id',
            ]
        );

        $this->addColumn(
            'store_id',
            [
                'header' => __('Store View'),
                'index'  => 'store_id',
                'type'   => 'store',
            ]
        );

        $this->addColumn(
            'status',
            [
                'header'  => __('Status'),
                'align'   => 'left',
                'width'   => '150px',
                'index'   => 'status',
                'type'    => 'options',
                'options' => ['0' => __('Inactive'), '1' => __('Active')],
            ]
        );

        return parent::_prepareColumns();
    }
}
