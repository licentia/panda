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

namespace Licentia\Panda\Block\Adminhtml\Coupons;

/**
 * Class Grid
 *
 * @package Licentia\Panda\Block\Adminhtml\Coupons
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
     * @var \Licentia\Panda\Model\CouponsFactory
     */

    protected $couponsFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context                       $context
     * @param \Magento\Backend\Helper\Data                                  $backendHelper
     * @param \Licentia\Panda\Model\CouponsFactory                          $couponsFactory
     * @param \Licentia\Panda\Model\CampaignsFactory                        $campaignsFactory
     * @param \Licentia\Panda\Model\ResourceModel\Coupons\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry                                   $registry
     * @param array                                                         $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Model\CouponsFactory $couponsFactory,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\ResourceModel\Coupons\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->campaignsFactory = $campaignsFactory;
        $this->couponsFactory = $couponsFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    public function _construct()
    {

        parent::_construct();
        $this->setId('coupons_grid');
        $this->setDefaultSort('coupon_id');
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
            'coupon_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'coupon_id',
            ]
        );

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

        $this->addColumn(
            'rule_id',
            [
                'header'  => __('Promotion Rule'),
                'type'    => 'options',
                'index'   => 'rule_id',
                'options' => $this->couponsFactory->create()
                                                  ->toFormValues(),
            ]
        );

        $this->addColumn(
            'coupon_code',
            [
                'header' => __('Coupon Code'),
                'index'  => 'coupon_code',
            ]
        );

        $this->addColumn(
            'times_used',
            [
                'header'  => __('Used'),
                'index'   => 'times_used',
                'type'    => 'options',
                'options' => [
                    '0' => __('No'),
                    '1' => __('Yes'),
                ],
            ]
        );

        $this->addColumn(
            'subscriber_email',
            [
                'header' => __('Subscriber Email'),
                'index'  => 'subscriber_email',
            ]
        );

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
            'created_at',
            [
                'header'    => __('Created at'),
                'align'     => 'left',
                'type'      => 'datetime',
                'gmtoffset' => true,
                'index'     => 'created_at',
            ]
        );

        $this->addColumn(
            'used_at',
            [
                'header'    => __('Used at'),
                'align'     => 'left',
                'type'      => 'datetime',
                'gmtoffset' => true,
                'index'     => 'used_at',
            ]
        );

        $this->addColumn(
            'order_id',
            [
                'header'         => __('Order'),
                'align'          => 'left',
                'width'          => '100px',
                'index'          => 'order_id',
                'frame_callback' => [$this, 'orderResult'],
                'is_system'      => true,
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @param $value
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function orderResult($value)
    {

        if ((int) $value > 0) {
            $url = $this->getUrl('order/view', ['order_id' => $value]);

            return '<a href="' . $url . '">[ID:' . $value . '] ' . __('View') . '</a>';
        }

        return __('N/A');
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
