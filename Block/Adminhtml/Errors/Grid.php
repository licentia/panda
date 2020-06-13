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

namespace Licentia\Panda\Block\Adminhtml\Errors;

/**
 * Class Grid
 *
 * @package Licentia\Panda\Block\Adminhtml\Errors
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {

        return __('Campaign Errors');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {

        return __('Campaign Errors');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {

        return $this->hasData('can_show_tab') ? $this->getData('can_show_tab') : true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {

        return false;
    }

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
     * @param \Magento\Backend\Block\Template\Context                      $context
     * @param \Magento\Backend\Helper\Data                                 $backendHelper
     * @param \Licentia\Panda\Model\CampaignsFactory                       $campaignsFactory
     * @param \Licentia\Panda\Model\ResourceModel\Errors\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry                                  $registry
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\ResourceModel\Errors\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->campaignsFactory = $campaignsFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    public function _construct()
    {

        parent::_construct();
        $this->setId('errors_grid');
        $this->setDefaultSort('error_id');
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
            $collection->addFieldToFilter('campaign_id', $id);
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
            'error_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'error_id',
            ]
        );

        if (!($id = $this->getRequest()->getParam('id'))) {
            $this->addColumn(
                'campaign_id',
                [
                    'header'  => __('Campaign'),
                    'index'   => 'campaign_id',
                    'type'    => 'options',
                    'options' => $this->campaignsFactory->create()
                                                        ->toFormValues(),
                ]
            );
        }

        $this->addColumn(
            'email',
            [
                'header' => __('Subscriber Email'),
                'index'  => 'email',
            ]
        );

        $this->addColumn(
            'error_code',
            [
                'header' => __('Error Code'),
                'index'  => 'error_code',
            ]
        );

        $this->addColumn(
            'error_message',
            [
                'header' => __('Error Message'),
                'index'  => 'error_message',
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header'    => __('Created at'),
                'align'     => 'left',
                'type'      => 'datetime',
                'gmtoffset' => true,
                'index'     => 'created_at',
            ]
        );

        # $this->addExportType('*/*/exportCsv', __('CSV'));
        # $this->addExportType('*/*/exportXml', __('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->getMassactionBlock()->setTemplate('Licentia_Panda::widget/grid/massaction_extended.phtml');

        $this->setMassactionIdField('error_id');
        $this->getMassactionBlock()->setFormFieldName('errors');

        $this->getMassactionBlock()
             ->addItem(
                 'tryagain',
                 [
                     'label'   => __('Try Again'),
                     'url'     => $this->getUrl('*/errors/massSend'),
                     'confirm' => __('Are you sure?'),
                 ]
             );

        $this->getMassactionBlock()
             ->addItem(
                 'delete',
                 [
                     'label'   => __('Delete'),
                     'url'     => $this->getUrl('*/errors/massDelete'),
                     'confirm' => __('Are you sure?'),
                 ]
             );

        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {

        return $this->getUrl('*/errors/grid', ['_current' => true]);
    }
}
