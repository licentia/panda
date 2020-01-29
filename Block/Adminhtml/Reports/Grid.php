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

namespace Licentia\Panda\Block\Adminhtml\Reports;

/**
 * Class Grid
 *
 * @package Licentia\Panda\Block\Adminhtml\Reports
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
    protected $campaignsFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context                       $context
     * @param \Magento\Backend\Helper\Data                                  $backendHelper
     * @param \Licentia\Panda\Model\CampaignsFactory                        $campaignsFactory
     * @param \Licentia\Panda\Model\ResourceModel\Reports\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry                                   $registry
     * @param array                                                         $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\ResourceModel\Reports\CollectionFactory $collectionFactory,
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
        $this->setId('reports_grid');
        $this->setDefaultSort('report_id');
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
            'report_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'report_id',
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Date'),
                'type'   => 'date',
                'index'  => 'created_at',
            ]
        );

        $this->addColumn(
            'subscribers',
            [
                'header' => __('Subscribers'),
                'index'  => 'subscribers',
            ]
        );
        $this->addColumn(
            'subscribers_variation',
            [
                'header' => __('<-Var.'),
                'index'  => 'subscribers_variation',
            ]
        );

        $this->addColumn(
            'campaigns',
            [
                'header' => __('Campaigns'),
                'index'  => 'campaigns',
            ]
        );

        $this->addColumn(
            'campaigns_variation',
            [
                'header' => __('<-Var.'),
                'index'  => 'campaigns_variation',
            ]
        );

        $this->addColumn(
            'clicks',
            [
                'header' => __('Clicks'),
                'index'  => 'clicks',
            ]
        );
        $this->addColumn(
            'clicks_variation',
            [
                'header' => __('<-Var.'),
                'index'  => 'clicks_variation',
            ]
        );

        $this->addColumn(
            'views',
            [
                'header' => __('Views'),
                'index'  => 'views',
            ]
        );
        $this->addColumn(
            'views_variation',
            [
                'header' => __('<-Var.'),
                'index'  => 'views_variation',
            ]
        );

        $this->addColumn(
            'conversions_number',
            [
                'header' => __('Conv.'),
                'index'  => 'conversions_number',
            ]
        );
        $this->addColumn(
            'conversions_number_variation',
            [
                'header' => __('<-Var.'),
                'index'  => 'conversions_number_variation',
            ]
        );

        $this->addColumn(
            'conversions_amount',
            [
                'header' => __('Amt.'),
                'index'  => 'conversions_amount',
            ]
        );
        $this->addColumn(
            'conversions_amount_variation',
            [
                'header' => __('<-Var.'),
                'index'  => 'conversions_amount_variation',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $item
     *
     * @return bool
     */
    public function getRowUrl($item)
    {

        return false;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {

        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}
