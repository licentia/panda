<?php

/**
 * Copyright (C) 2020 Licentia, Unipessoal LDA
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @title      Licentia Panda - Magento® Sales Automation Extension
 * @package    Licentia
 * @author     Bento Vilas Boas <bento@licentia.pt>
 * @copyright  Copyright (c) Licentia - https://licentia.pt
 * @license    GNU General Public License V3
 * @modified   29/01/20, 15:22 GMT
 *
 */

namespace Licentia\Panda\Block\Adminhtml\Events;

/**
 * Class Grid
 *
 * @package Licentia\Panda\Block\Adminhtml\Events
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
    protected $autorespondersFactory;

    /**
     * @var \Licentia\Panda\Model\TemplatesFactory
     */
    protected $templatesFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context                      $context
     * @param \Magento\Backend\Helper\Data                                 $backendHelper
     * @param \Licentia\Panda\Model\AutorespondersFactory                  $autorespondersFactory
     * @param \Licentia\Panda\Model\TemplatesFactory                       $templatesFactory
     * @param \Licentia\Panda\Model\ResourceModel\Events\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry                                  $registry
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        \Licentia\Panda\Model\TemplatesFactory $templatesFactory,
        \Licentia\Panda\Model\ResourceModel\Events\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->autorespondersFactory = $autorespondersFactory;
        $this->templatesFactory = $templatesFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    public function _construct()
    {

        parent::_construct();
        $this->setId('events_grid');
        $this->setDefaultSort('event_id');
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

        $current = $this->registry->registry('panda_autoresponder');

        $collection->addFieldToFilter('executed', 0);

        if ($current) {
            $collection->addFieldToFilter('autoresponder_id', $current->getId());
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

        $list = $this->autorespondersFactory->create()->getActionsList();

        $options = [];

        foreach ($list as $item) {
            $options[$item['id']] = $item['name'];
        }

        $this->addColumn(
            'event_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'event_id',
            ]
        );
        $this->addColumn(
            'event',
            [
                'header'  => __('Next Action'),
                'index'   => 'event',
                'type'    => 'options',
                'options' => $options,
            ]
        );

        $this->addColumn(
            'autoresponder_id',
            [
                'header'  => __('Autoresponder'),
                'index'   => 'autoresponder_id',
                'type'    => 'options',
                'options' => $this->autorespondersFactory->create()
                                                         ->toFormValues(),
            ]
        );
        /*
                $this->addColumn(
                    'template_id',
                    [
                        'header'  => __('Template'),
                        'index'   => 'template_id',
                        'type'    => 'options',
                        'options' => $this->_templatesFactory->create()->toFormValues(),
                    ]
                );
        */
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
            'subscriber_email',
            [
                'header' => __('Email'),
                'index'  => 'subscriber_email',
            ]
        );

        $this->addColumn(
            'created_at_grid',
            [
                'header'    => __('Created at'),
                'align'     => 'left',
                'width'     => '170px',
                'type'      => 'datetime',
                'gmtoffset' => true,
                'index'     => 'created_at_grid',
            ]
        );

        $this->addColumn(
            'execute_at',
            [
                'header'    => __('Execute at'),
                'align'     => 'left',
                'width'     => '170px',
                'type'      => 'datetime',
                'gmtoffset' => true,
                'index'     => 'execute_at',
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

        $this->setMassactionIdField('event_id');
        $this->getMassactionBlock()->setFormFieldName('events');

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
     * @return string
     */
    public function getGridUrl()
    {

        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * @param $value
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function customerResult($value)
    {

        if ((int) $value > 0) {
            $url = $this->getUrl('customer/index/edit', ['id' => $value]);

            return '<a href="' . $url . '">' . __('Yes') . '</a>';
        }

        return __('No');
    }
}
