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

namespace Licentia\Panda\Block\Adminhtml\Tags;

/**
 * Class Grid
 *
 * @package Licentia\Panda\Block\Adminhtml\Tags
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\TagsFactory
     */
    protected $tagsFactory;

    /**
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * Grid constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                    $context
     * @param \Magento\Backend\Helper\Data                               $backendHelper
     * @param \Licentia\Panda\Helper\Data                                $pandaHelper
     * @param \Licentia\Panda\Model\CampaignsFactory                     $campaignsFactory
     * @param \Licentia\Panda\Model\TagsFactory                          $tagsFactory
     * @param \Licentia\Panda\Model\ResourceModel\Tags\CollectionFactory $collectionFactory
     * @param \Licentia\Equity\Model\SegmentsFactory                     $segmentsFactory
     * @param array                                                      $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\TagsFactory $tagsFactory,
        \Licentia\Panda\Model\ResourceModel\Tags\CollectionFactory $collectionFactory,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        array $data = []
    ) {

        $this->collectionFactory = $collectionFactory;
        $this->pandaHelper = $pandaHelper;
        $this->tagsFactory = $tagsFactory;
        $this->campaignsFactory = $campaignsFactory;
        $this->segmentsFactory = $segmentsFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {

        parent::_construct();
        $this->setId('pandaTagsGrid');
        $this->setDefaultSort('tag_id');
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
        /* @var $collection \Licentia\Panda\Model\ResourceModel\Tags\Collection */
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
            'tag_id',
            [
                'header' => __('ID'),
                'width'  => '50px',
                'index'  => 'tag_id',
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Tag Name'),
                'align'  => 'left',
                'index'  => 'name',
            ]
        );

        $this->addColumn(
            'from_date',
            [
                'header'    => __('Date Start'),
                'align'     => 'left',
                'width'     => '120px',
                'type'      => 'date',
                'index'     => 'from_date',
                'gmtoffset' => true,
            ]
        );

        $this->addColumn(
            'to_date',
            [
                'header'    => __('Date End'),
                'align'     => 'left',
                'width'     => '120px',
                'type'      => 'date',
                'index'     => 'to_date',
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
     * @return string
     */
    public function optionResult($value, $row)
    {

        $tag = $row->getData('tag_type');

        if (stripos($tag, 'segment_') !== false) {
            return 'N/A';
        }

        if (stripos($tag, 'segment_') !== false) {
            $model = $this->segmentsFactory->create()->load($value);
        } else {
            if (stripos($tag, 'global') !== false) {
                return 'N/A';
            }

            $model = $this->campaignsFactory->create()->load($value);
        }

        if ($model->getId()) {
            return $model->getName() ? $model->getName() : $model->getInternalName();
        }

        return 'N/A';
    }

    /**
     * @param $value
     * @param $row
     *
     * @return string
     */
    public function currentResult($value, $row)
    {

        $number = (int) $row->getData('expected_value') - (int) $row->getData('original_value');

        $valueColor = abs(round(($value - $row->getData('original_value')) * 100 / $number));

        $color = '#' . $this->percent2Color($valueColor);

        if ($row->getData('result') == 0) {
            $color = 'black; color: #FFF';
        }

        if ($row->getData('result') == 1) {
            $color = 'blue; color: white';
        }

        if ($row->getData('current_value') < $row->getData('original_value')) {
            $color = 'red; color: #FFF';
            $valueColor = '0';
        }

        if ($row->getData('result') == 3) {
            $color = '';
        }

        return '<div style="text-align:center; background-color:' . $color .
               '; border-radius: 7px; font-weight: bold;">' . $value . ' (' . $valueColor .
               '%)</div>';
    }

    /**
     * @param        $value
     * @param int    $brightness
     * @param int    $max
     * @param string $thirdColorHex
     *
     * @return string
     */
    public function percent2Color($value, $brightness = 255, $max = 100, $thirdColorHex = '00')
    {

        if ($value >= $max) {
            return "008000; color: #FFF";
        }

        $first = (1 - ($value / $max)) * $brightness;
        $second = ($value / $max) * $brightness;

        // Find the influence of the middle color (yellow if 1st and 2nd are red and green)
        $diff = abs($first - $second);
        $influence = ($brightness - $diff) / 2;
        $first = intval($first + $influence);
        $second = intval($second + $influence);

        // Convert to HEX, format and return
        $firstHex = str_pad(dechex($first), 2, 0, STR_PAD_LEFT);
        $secondHex = str_pad(dechex($second), 2, 0, STR_PAD_LEFT);

        return $firstHex . $secondHex . $thirdColorHex;
    }
}
