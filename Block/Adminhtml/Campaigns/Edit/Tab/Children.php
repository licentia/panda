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

namespace Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab;

/**
 * Class Children
 *
 * @package Licentia\Panda\Block\Adminhtml\Campaigns\Edit\Tab
 */
class Children extends \Magento\Backend\Block\Widget\Grid\Extended
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
     * @param \Magento\Backend\Block\Template\Context                         $context
     * @param \Magento\Backend\Helper\Data                                    $backendHelper
     * @param \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry                                     $registry
     * @param array                                                           $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    public function _construct()
    {

        parent::_construct();
        $this->setId('campaign_children_grid');
        $this->setDefaultSort('campaign_id');
        $this->setDefaultDir('DESC');
        $this->setEmptyText(__('No campaigns have been sent yet.'));
        $this->setFilterVisibility(false);
        $this->setSortable(false);
        $this->setPagerVisibility(false);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {

        $current = $this->registry->registry('panda_campaign');

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('parent_id', $current->getId());

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
            'campaign_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'campaign_id',
            ]
        );

        $this->addColumn(
            'internal_name',
            [
                'header' => __('Campaign Name'),
                'align'  => 'left',
                'index'  => 'internal_name',
            ]
        );

        $this->addColumn(
            'subject',
            [
                'header' => __('Subject'),
                'align'  => 'left',
                'index'  => 'subject',
            ]
        );

        $this->addColumn(
            'recurring_last_run',
            [
                'header'    => __('Last Run'),
                'align'     => 'left',
                'width'     => '180px',
                'index'     => 'recurring_last_run',
                'type'      => 'datetime',
                'gmtoffset' => true,
            ]
        );

        $this->addColumn(
            'status',
            [
                'header'         => __('Status'),
                'align'          => 'left',
                'width'          => '80px',
                'frame_callback' => [$this, 'serviceResult'],
                'index'          => 'status',
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
                                                       ->getBaseCurrencyCode(),
                'index'         => 'conversions_amount',
            ]
        );

        $this->addColumn(
            'sent',
            [
                'header'         => __('Sent'),
                'align'          => 'right',
                'index'          => 'sent',
                'width'          => '80px',
                'frame_callback' => [$this, 'sentResult'],
            ]
        );

        $this->addColumn(
            'unique_views',
            [
                'header' => __('U. Views'),
                'align'  => 'right',
                'index'  => 'unique_views',
                'width'  => '20px',
            ]
        );

        $this->addColumn(
            'unique_clicks',
            [
                'header' => __('U. Clicks'),
                'align'  => 'right',
                'index'  => 'unique_clicks',
                'width'  => '20px',
            ]
        );

        $this->addColumn(
            'conv',
            [
                'header'   => __('Detail'),
                'type'     => 'action',
                'width'    => '50px',
                'filter'   => false,
                'sortable' => false,
                'actions'  => [
                    [
                        'url'     => $this->getUrl('*/campaigns/conversions', ['id' => '$campaign_id']),
                        'caption' => __('Conversions'),
                    ],
                ],
                'index'    => 'type',
            ]
        );

        $this->addColumn(
            'action',
            [
                'header'   => __('View'),
                'type'     => 'action',
                'width'    => '50px',
                'filter'   => false,
                'sortable' => false,
                'actions'  => [
                    [
                        'url'     => $this->getUrl('*/reports/detail', ['id' => '$campaign_id']),
                        'caption' => __('Reports'),
                    ],
                ],
                'index'    => 'type',
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
     * @param $value
     *
     * @return string
     */
    public function serviceResult($value)
    {

        if ($value == "standby") {
            return ' <span class="grid-severity-minor"><span>' . __('Stand By') . '</span></span>';
        }

        if ($value == "running") {
            return ' <span class="grid-severity-major"><span>' . __('Running') . '</span></span>';
        }

        if ($value == "finished") {
            return ' <span class="grid-severity-notice"><span>' . __('Finished') . '</span></span>';
        }

        return '';
    }

    /**
     * @param $value
     * @param $row
     *
     * @return string
     */
    public function sentResult($value, $row)
    {

        $return = $value . ' /' . $row->getData('unsent');
        if ($value < (int) $row->getData('unsent') && $row->getData('status') == 'finished') {
            $url = $this->getUrl('*/errors/', ['id' => $row->getData('campaign_id')]);

            return '<a href="' . $url . '">' . $return . '</a>';
        }

        return $return;
    }
}
