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

namespace Licentia\Panda\Model\Autoresponders;

/**
 * Class Condition
 *
 * @package Licentia\Panda\Model\Autoresponders
 */

use Magento\Framework\Model;

/**
 * Class Condition
 *
 * @package Licentia\Panda\Model\Autoresponders
 */
class Condition extends AbstractModel
{

    /**
     * @var \Licentia\Panda\Model\TemplatesFactory
     */
    protected \Licentia\Panda\Model\TemplatesFactory $templatesFactory;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Templates\CollectionFactory
     */
    protected \Licentia\Panda\Model\ResourceModel\Templates\CollectionFactory $templatesCollection;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory
     */
    protected \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory $sendersCollection;

    /**
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected \Licentia\Panda\Model\CampaignsFactory $campaignsFactory;

    /**
     * @var \Licentia\Panda\Model\ServiceFactory
     */
    protected \Licentia\Panda\Model\ServiceFactory $serviceFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scope;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected \Licentia\Panda\Helper\Data $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Subscribers\CollectionFactory
     */
    protected \Licentia\Panda\Model\ResourceModel\Subscribers\CollectionFactory $subscriberCollection;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory
     */
    protected \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory $campaignsCollection;

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected \Licentia\Equity\Model\SegmentsFactory $segmentsFactory;

    /**
     * Condition constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface                $scope
     * @param \Licentia\Panda\Model\ChainseditFactory                           $chainseditFactory
     * @param \Licentia\Panda\Model\TemplatesFactory                            $templatesFactory
     * @param \Licentia\Panda\Model\ResourceModel\Templates\CollectionFactory   $templatesCollection
     * @param \Licentia\Panda\Model\CampaignsFactory                            $campaignsFactory
     * @param \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory   $campaignsCollection
     * @param \Licentia\Panda\Model\ResourceModel\Subscribers\CollectionFactory $subscriberCollection
     * @param \Licentia\Panda\Model\ServiceFactory                              $serviceFactory
     * @param \Licentia\Panda\Helper\Data                                       $pandaHelper
     * @param \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory     $sendersCollection
     * @param \Magento\Backend\Block\Template                                   $block
     * @param \Licentia\Panda\Model\ChainsFactory                               $chainsFactory
     * @param \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory  $chainseditCollection
     * @param \Licentia\Equity\Model\SegmentsFactory                            $segmentsFactory
     * @param Model\Context                                                     $context
     * @param \Magento\Framework\Registry                                       $registry
     * @param Model\ResourceModel\AbstractResource|null                         $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null                $resourceCollection
     * @param array                                                             $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scope,
        \Licentia\Panda\Model\ChainseditFactory $chainseditFactory,
        \Licentia\Panda\Model\TemplatesFactory $templatesFactory,
        \Licentia\Panda\Model\ResourceModel\Templates\CollectionFactory $templatesCollection,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory $campaignsCollection,
        \Licentia\Panda\Model\ResourceModel\Subscribers\CollectionFactory $subscriberCollection,
        \Licentia\Panda\Model\ServiceFactory $serviceFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory $sendersCollection,
        \Magento\Backend\Block\Template $block,
        \Licentia\Panda\Model\ChainsFactory $chainsFactory,
        \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory $chainseditCollection,
        \Licentia\Equity\Model\SegmentsFactory $segmentsFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        $this->templatesFactory = $templatesFactory;
        $this->templatesCollection = $templatesCollection;
        $this->sendersCollection = $sendersCollection;
        $this->pandaHelper = $pandaHelper;
        $this->scope = $scope;
        $this->serviceFactory = $serviceFactory;
        $this->campaignsFactory = $campaignsFactory;
        $this->campaignsCollection = $campaignsCollection;
        $this->subscriberCollection = $subscriberCollection;
        $this->segmentsFactory = $segmentsFactory;

        parent::__construct(
            $chainseditFactory,
            $block,
            $chainsFactory,
            $chainseditCollection,
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return string
     */
    public function render()
    {

        $segments =
            $this->segmentsFactory->create()->toFormValues();

        $array = (array) $this->getData('extra_data/segment_in');
        $selectCustomer = "<select name='segment_in[]' size='7' multiple='multiple'>";
        foreach ($segments as $key => $value) {
            $selected = '';

            if (in_array($key, $array)) {
                $selected = ' selected="selected" ';
            }

            $selectCustomer .= "<option $selected value='{$key}'>{$value}</option>";
        }
        $selectCustomer .= "</select>";

        $array = (array) $this->getData('extra_data/segment_not');
        $selectCustomer2 = "<select name='segment_not[]' size='7' multiple='multiple'>";
        foreach ($segments as $key => $value) {
            $selected = '';

            if (in_array($key, $array)) {
                $selected = ' selected="selected" ';
            }

            $selectCustomer2 .= "<option $selected value='{$key}'>{$value}</option>";
        }
        $selectCustomer2 .= "</select>";

        $segments = "
        <label>Customer Segment IS one of the</label>
            $selectCustomer
        <label>Customer Segment IS NOT one of the</label>
            $selectCustomer2";

        $templates = [
            'no_open'       => "Didn't Open",
            'open'          => 'Open',
            'no_click'      => 'Not Clicked',
            'click'         => 'Clicked',
            'conversion'    => 'Converted',
            'no_conversion' => 'No Conversion',
        ];

        $select = "<select name='condition[]' size='6' multiple='multiple'>";

        $array = (array) $this->getData('extra_data/condition');

        foreach ($templates as $key => $value) {
            $selected = '';

            if (in_array($key, $array)) {
                $selected = ' selected="selected" ';
            }

            $select .= "<option $selected value='{$key}'>{$value}</option>";
        }

        $select .= "</select>";

        $img = $this->template->getViewFileUrl('Licentia_Panda::images/close.png');

        $class = $this->getData('chain_id') ? 'edit' : 'submit';
        $label = $this->getData('chain_id') ? 'Edit' : 'Add';
        $form = $this->getData('chain_id') ? 'edit_data' : 'add_data';

        $return = <<<EOL
        <span class="formTriggersWrapper">
        <form class="$form" id="formTriggers" method="post" action="">
        <img class="close" src="$img" />
        <label>Name</label>
        <input type="text" value="{$this->getData('extra_data/name')}" class="required" name="name" placeholder="Condition Name">
        $segments
        <label>Performed the following action in the last message from this chain</label>
        $select
        <input type="submit" class="$class" name="send" value="$label">
        </form>
        </span>

EOL;

        return $return;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function add()
    {

        $params = $this->getData('params');

        $data = [];
        $data['chain_id'] = $this->getData('chain_id');
        $data['autoresponder_id'] = $params['autoresponder_id'];
        $data['main_condition'] = 1;
        $data['name'] = "Condition: " . $params['name'];
        $data['extra_data'] = json_encode($params);

        $parentId = '';
        if (!isset($data['chain_id'])) {
            $parentId = $params['parentid'];
            $data['parent_id'] = $parentId;
            $data['event'] = $params['type'];
        }

        $chain = $this->chainseditFactory->create()
                                         ->setData($data)
                                         ->save();

        if ($this->getMode() == 'edit') {
            return $data['name'];
        }

        $this->chainseditFactory->create()
                                ->getResource()
                                ->getConnection()
                                ->update(
                                    $this->chainseditFactory->create()
                                                            ->getResource()
                                                            ->getTable('panda_autoresponders_chains_edit'),
                                    ['parent_id' => $chain->getId()],
                                    ['parent_id=?' => $parentId, 'chain_id !=?' => $chain->getId()]
                                );

        $yesData = $data;
        $yesData['name'] = 'Yes';
        $yesData['parent_id'] = $chain->getId();
        $yesData['main_condition'] = 0;
        $yesData['yes_condition'] = 1;
        $yesData['editable'] = 0;

        $yes = $this->chainseditFactory->create()
                                       ->setData($yesData)
                                       ->save();

        $noData = $data;
        $noData['name'] = 'No';
        $noData['parent_id'] = $chain->getId();
        $noData['main_condition'] = 0;
        $noData['editable'] = 0;

        $no = $this->chainseditFactory->create()
                                      ->setData($noData)
                                      ->save();

        $return = '
    <li><div  class="editable" id = "' . $chain->getId() . '" ><span class="name" > ' .
                  $chain->getName() . ' </span ></div >
<ul >
    <li class="trigger_condition" >
<div class="div_droppable" id = "' . $yes->getId() . '" ><span class="name" >Yes</span ></div >
    </li >
    <li class="trigger_condition" >
<div  class="div_droppable" id = "' . $no->getId() . '" ><span class="name" >No</span ></div >
    </li >
</ul ></li>';

        return $return;
    }

    /**
     * @param \Licentia\Panda\Model\Autoresponders $autoresponder
     * @param \Licentia\Panda\Model\Subscribers    $subscriber
     * @param \Licentia\Panda\Model\Events         $cron
     * @param \Licentia\Panda\Model\Chains         $chain
     *
     * @return bool|void
     */
    public function run(
        \Licentia\Panda\Model\Autoresponders $autoresponder,
        \Licentia\Panda\Model\Subscribers $subscriber,
        \Licentia\Panda\Model\Events $cron,
        \Licentia\Panda\Model\Chains $chain
    ) {

        $data = json_decode($chain->getExtraData(), true);

        if (!$data) {
            return false;
        }
        $return = 1;

        if (is_array($data['condition'])) {
            $campaignCollection = $this->campaignsCollection->create()
                                                            ->addFieldToSelect('campaign_id')
                                                            ->addFieldToFilter(
                                                                'autoresponder_id',
                                                                $autoresponder->getId()
                                                            )
                                                            ->addFieldToFilter(
                                                                'autoresponder_recipient',
                                                                $subscriber->getEmail()
                                                            )
                                                            ->setOrder('campaign_id', 'DESC')
                                                            ->setPageSize(1);

            if ($campaignCollection->count() != 1 && count($data['condition']) > 0) {
                return false;
            }

            $campaignId = $campaignCollection->getFirstItem()->getId();

            foreach ($data['condition'] as $condition) {
                if (!$this->validateCondition($condition, $campaignId, $subscriber)) {
                    $return = 0;
                    break;
                }
            }
        }

        if (is_array($data['segment_in'])) {
            foreach ($data['segment_in'] as $segment) {
                if (!$this->validateCondition('segment_in', $segment, $subscriber)) {
                    $return = 0;
                    break;
                }
            }
        }
        if (is_array($data['segment_not'])) {
            foreach ($data['segment_not'] as $segment) {
                if (!$this->validateCondition('segment_not', $segment, $subscriber)) {
                    $return = 0;
                    break;
                }
            }
        }

        $next = $this->chainseditCollection->create()
                                           ->addFieldToFilter('yes_condition', $return)
                                           ->addFieldToFilter('parent_id', $chain->getId())
                                           ->setOrder('sort_order', 'ASC');

        if ($next->count() == 1) {
            return $next->getFirstItem()->getId();
        }

        return false;
    }

    /**
     * @param $key
     * @param $value
     * @param $subscriber
     *
     * @return bool
     */
    public function validateCondition($key, $value, $subscriber)
    {

        if (in_array(
            $key,
            ['no_open', 'open', 'no_click', 'click', 'conversion', 'no_conversion']
        )) {
            $collection = $this->subscriberCollection->create();

            $select = $collection->getSelect();
            $subSelect = clone $select;

            if ($key == 'open') {
                $select->join(
                    $collection->getTable('panda_stats'),
                    'main_table.subscriber_id=' . $collection->getTable('panda_stats') .
                    '.subscriber_id',
                    []
                );
                $select->where($collection->getTable('panda_stats') . ".type ='views' ");
                $select->where($collection->getTable('panda_stats') . ".campaign_id =? ", $value);
            }

            if ($key == 'no_open') {
                $subSelect->reset('from');
                $subSelect->reset('columns');
                $subSelect->from($collection->getTable('panda_stats'), ['subscriber_id']);
                $subSelect->where(
                    $collection->getTable('panda_stats') . ".campaign_id = ? ",
                    $value
                );
                $subSelect->where($collection->getTable('panda_stats') . ".type = ? ", 'views');
                $select->where("main_table.subscriber_id NOT IN (?)", $subSelect);
            }

            if ($key == 'click') {
                $select->join(
                    $collection->getTable('panda_stats'),
                    'main_table.subscriber_id=' . $collection->getTable('panda_stats') .
                    '.subscriber_id',
                    []
                );
                $select->where($collection->getTable('panda_stats') . ".type ='clicks' ");
                $select->where(
                    $collection->getTable('panda_stats') . ".campaign_id =? ",
                    $value
                );
            }

            if ($key == 'no_click') {
                $subSelect->reset('from');
                $subSelect->reset('columns');
                $subSelect->from($collection->getTable('panda_stats'), ['subscriber_id']);
                $subSelect->where(
                    $collection->getTable('panda_stats') . ".campaign_id = ? ",
                    $value
                );
                $subSelect->where($collection->getTable('panda_stats') . ".type = ? ", 'clicks');
                $select->where("main_table.subscriber_id NOT IN (?)", $subSelect);
            }

            if ($key == 'conversion') {
                $select->join(
                    $collection->getTable('panda_conversions'),
                    'main_table.subscriber_id=' . $collection->getTable('panda_conversions') .
                    '.subscriber_id',
                    []
                );
                $select->where(
                    $collection->getTable('panda_conversions') . ".campaign_id =? ",
                    $value
                );
            }

            if ($key == 'no_conversion') {
                $subSelect->reset('from');
                $subSelect->reset('columns');
                $subSelect->from(
                    $collection->getTable('panda_conversions'),
                    ['subscriber_id']
                );
                $subSelect->where(
                    $collection->getTable('panda_conversions') .
                    ".campaign_id = ? ",
                    $value
                );
                $select->where("main_table.subscriber_id NOT IN (?)", $subSelect);
            }

            $select->join(
                $collection->getTable('panda_messages_archive'),
                'main_table.subscriber_id=' . $collection->getTable('panda_messages_archive') .
                '.subscriber_id',
                []
            );
            $select->where(
                $collection->getTable('panda_messages_archive') . ".campaign_id = ? ",
                $value
            );

            $select->group("main_table.subscriber_id");

            return $collection->getSize() > 0 ? true : false;
        }

        if ($key == 'segment_not' || $key == 'segment_in') {
            $result = $this->segmentsFactory->create()
                                            ->getCollection()
                                            ->addFieldToFilter('email', $subscriber->getCustomerId())
                                            ->addFieldToFilter('segment_id', $value)
                                            ->setPageSize(1)
                                            ->count();

            if ((!$result && $key == 'segment_not') || ($result && $key == 'segment_in')) {
                return true;
            }

            return false;
        }

        return false;
    }
}
