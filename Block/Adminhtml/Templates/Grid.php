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

namespace Licentia\Panda\Block\Adminhtml\Templates;

/**
 * Class Grid
 *
 * @package Licentia\Panda\Block\Adminhtml\Templates
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
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
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * @var \Licentia\Panda\Model\TemplatesGlobalFactory
     */
    protected $templatesGlobalFactory;

    /**
     * Grid constructor.
     *
     * @param \Licentia\Panda\Model\CampaignsFactory                           $campaignsFactory
     * @param \Licentia\Panda\Model\TemplatesGlobalFactory                     $templatesGlobalFactory
     * @param \Magento\Backend\Block\Template\Context                          $context
     * @param \Magento\Backend\Helper\Data                                     $backendHelper
     * @param \Licentia\Panda\Model\ResourceModel\Templates\CollectionFactory  $collectionFactory
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     * @param array                                                            $data
     */
    public function __construct(
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\TemplatesGlobalFactory $templatesGlobalFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Model\ResourceModel\Templates\CollectionFactory $collectionFactory,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        array $data = []
    ) {

        $this->campaignsFactory = $campaignsFactory;
        $this->templatesGlobalFactory = $templatesGlobalFactory;
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

    /**
     * @return string
     */
    public function getGridUrl()
    {

        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    protected function _construct()
    {

        parent::_construct();
        $this->setId('pandaTemplatesGrid');
        $this->setDefaultSort('template_id');
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

        /* @var $collection \Licentia\Panda\Model\ResourceModel\Templates\Collection */
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter('parent_id', ['null' => true]);

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
                'class'  => '50px',
                'index'  => 'template_id',
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index'  => 'name',
            ]
        );

        $this->addColumn(
            'global_template_id',
            [
                'header'  => __('Design Template'),
                'index'   => 'global_template_id',
                'type'    => 'options',
                'options' => $this->templatesGlobalFactory->create()
                                                          ->toFormValues(),
            ]
        );

        $this->addColumn(
            'campaign_id',
            [
                'header'  => __('From Campaign'),
                'type'    => 'options',
                'options' => $this->campaignsFactory->create()
                                                    ->toFormValuesNonAuto(false),
                'index'   => 'campaign_id',
            ]
        );

        $this->addColumn(
            'parent_id',
            [
                'header'         => __('Variations'),
                'frame_callback' => [$this, 'variationsResult'],
                'index'          => 'parent_id',
            ]
        );

        $this->addColumn(
            'is_active',
            [
                'header'  => __('Status'),
                'align'   => 'left',
                'width'   => '150px',
                'index'   => 'is_active',
                'type'    => 'options',
                'options' => ['0' => __('Inactive'), '1' => __('Active')],
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @param $value
     * @param $row
     *
     * @return string
     */
    public function variationsResult($value, $row)
    {

        $vars = $this->collectionFactory->create()->addFieldToFilter('parent_id', $row->getId());

        $result = '';
        foreach ($vars as $item) {
            $result .= '<a href="' . $this->getUrl('*/*/edit', ['id' => $item->getId()]) . '">' . $item->getData(
                    'name'
                ) . '</a>  |  ';
        }

        $result = rtrim($result, '  |  ');

        return $result;
    }
}
