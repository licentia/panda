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

namespace Licentia\Panda\Block\Adminhtml\Splits;

/**
 * Adminhtml Campaigns grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected $splitsFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context                      $context
     * @param \Magento\Backend\Helper\Data                                 $backendHelper
     * @param \Licentia\Panda\Helper\Data                                  $pandaHelper
     * @param \Licentia\Panda\Model\ResourceModel\Splits\CollectionFactory $collectionFactory
     * @param \Licentia\Panda\Model\SplitsFactory                          $splitsFactory
     * @param array                                                        $data
     *
     * @internal param \Licentia\Panda\Model\CampaignsFactory $campaignsFactory
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\ResourceModel\Splits\CollectionFactory $collectionFactory,
        \Licentia\Panda\Model\SplitsFactory $splitsFactory,
        array $data = []
    ) {

        $this->collectionFactory = $collectionFactory;
        $this->pandaHelper = $pandaHelper;
        $this->splitsFactory = $splitsFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {

        parent::_construct();
        $this->setId('pandaSplitsGrid');
        $this->setDefaultSort('split_id');
        $this->setDefaultDir('DESC');
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

        $collection = $this->collectionFactory->create();
        /* @var $collection \Licentia\Panda\Model\ResourceModel\Campaigns\Collection */
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
            'split_id',
            [
                'header' => __('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'split_id',
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'align'  => 'left',
                'index'  => 'name',
            ]
        );

        $this->addColumn(
            'deploy_at',
            [
                'header'    => __('Test Send At'),
                'align'     => 'left',
                'type'      => 'datetime',
                'gmtoffset' => true,
                'width'     => '180px',
                'index'     => 'deploy_at',
            ]
        );

        $this->addColumn(
            'send_at',
            [
                'header'    => __('General Send At'),
                'align'     => 'left',
                'type'      => 'datetime',
                'gmtoffset' => true,
                'width'     => '180px',
                'index'     => 'send_at',
            ]
        );

        $this->addColumn(
            'views_a',
            [
                'header' => __('Views A'),
                'align'  => 'left',
                'index'  => 'views_a',
                'type'   => 'text',
            ]
        );

        $this->addColumn(
            'views_b',
            [
                'header' => __('Views B'),
                'align'  => 'left',
                'index'  => 'views_b',
                'type'   => 'text',
            ]
        );

        $this->addColumn(
            'clicks_a',
            [
                'header' => __('Clicks A'),
                'align'  => 'left',
                'index'  => 'clicks_a',
                'type'   => 'text',
            ]
        );

        $this->addColumn(
            'clicks_b',
            [
                'header' => __('Clicks B'),
                'align'  => 'left',
                'index'  => 'clicks_b',
                'type'   => 'text',
            ]
        );

        $this->addColumn(
            'conversions_a',
            [
                'header' => __('Conversions A '),
                'align'  => 'left',
                'index'  => 'conversions_a',
                'type'   => 'text',
            ]
        );

        $this->addColumn(
            'conversions_b',
            [
                'header' => __('Conversions B'),
                'align'  => 'left',
                'index'  => 'conversions_b',
                'type'   => 'text',
            ]
        );

        $this->addColumn(
            'is_active',
            [
                'header'  => __('Status'),
                'align'   => 'left',
                'index'   => 'is_active',
                'type'    => 'options',
                'options' => ['0' => __('Inactive'), '1' => __('Active')],
            ]
        );

        $this->addColumn(
            'closed',
            [
                'header'  => __('Finished'),
                'align'   => 'left',
                'index'   => 'closed',
                'type'    => 'options',
                'options' => ['0' => __('No'), '1' => __('Yes')],
            ]
        );

        $this->addColumn(
            'winner',
            [
                'header'  => __('Winner'),
                'align'   => 'left',
                'index'   => 'winner',
                'type'    => 'options',
                'options' => $this->splitsFactory->create()
                                                 ->getWinnerOptions(),
            ]
        );

        $this->addColumn(
            'closed_a',
            [
                'header'         => __('View'),
                'align'          => 'left',
                'filter'         => false,
                'sortable'       => false,
                'frame_callback' => [$this, 'serviceResult'],
                'index'          => 'closed',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {

        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {

        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }

    /**
     * @param $value
     * @param $row
     *
     * @return bool|string
     */
    public function serviceResult($value, $row)
    {

        $return = false;

        if ((int) $row->getSent() == 1) {
            $campaigns = $this->splitsFactory->create()->getTestCampaigns($row);

            if ($campaigns) {
                /** @var \Licentia\Panda\Model\Campaigns $campaign */
                foreach ($campaigns as $campaign) {
                    $url = $this->getUrl(
                        '*/campaigns/edit',
                        ['id' => $campaign->getData('campaign_id')]
                    );
                    $return .= '&nbsp; <a href="' . $url . '">Test [' .
                               ucfirst($campaign->getData('split_version')) . ']</a>';
                }
            }
        }
        if ((int) $row->getClosed() == 1) {

            /** @var \Licentia\Panda\Model\Campaigns $campaign */
            $campaign = $this->splitsFactory->create()->getFinalCampaign($row, 'campaign_id');

            if ($campaign && $campaign->getId()) {
                $url =
                    $this->getUrl('*/campaigns/edit', ['id' => $campaign->getData('campaign_id')]);
                $return .= '&nbsp; <a href="' . $url . '">Campaign</a>';
            }
        }

        if (!$return) {
            $return = 'N/A';
        }

        return $return;
    }
}
