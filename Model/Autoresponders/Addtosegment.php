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

namespace Licentia\Panda\Model\Autoresponders;

/**
 * Class Condition
 *
 * @package Licentia\Panda\Model\Autoresponders
 */

use Licentia\Equity\Model\Segments\ListSegmentsFactory;
use Magento\Framework\Model;

/**
 * Class Condition
 *
 * @package Licentia\Panda\Model\Autoresponders
 */
class Addtosegment extends AbstractModel
{

    /**
     * @var \Licentia\Panda\Model\TemplatesFactory
     */
    protected $templatesFactory;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Templates\CollectionFactory
     */
    protected $templatesCollection;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory
     */
    protected $sendersCollection;

    /**
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * @var \Licentia\Panda\Model\ServiceFactory
     */
    protected $serviceFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Subscribers\CollectionFactory
     */
    protected $subscriberCollection;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Campaigns\CollectionFactory
     */
    protected $campaignsCollection;

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected $segmentsFactory;

    /**
     * @var ListSegmentsFactory
     */
    protected $listSegmentsFactory;

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
     * @param ListSegmentsFactory                                               $listSegmentsFactory
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
        ListSegmentsFactory $listSegmentsFactory,
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
        $this->scopeConfig = $scope;
        $this->serviceFactory = $serviceFactory;
        $this->campaignsFactory = $campaignsFactory;
        $this->campaignsCollection = $campaignsCollection;
        $this->subscriberCollection = $subscriberCollection;
        $this->segmentsFactory = $segmentsFactory;
        $this->listSegmentsFactory = $listSegmentsFactory;

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

        $segments = $this->segmentsFactory->create()->toFormValues();

        $array = (array) $this->getData('extra_data/segments');
        $selectCustomer = "<select name='segments[]' size='7' required='required' multiple='multiple'>";
        foreach ($segments as $key => $value) {
            $selected = '';

            if (in_array($key, $array)) {
                $selected = ' selected="selected" ';
            }

            $selectCustomer .= "<option $selected value='{$key}'>{$value}</option>";
        }
        $selectCustomer .= "</select>";

        $segments = "
        <label>Add to Segment(s)</label>
            $selectCustomer";

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
        $parentId = '';
        $data = [];
        $data['chain_id'] = $this->getData('chain_id');
        $data['autoresponder_id'] = $params['autoresponder_id'];
        $data['name'] = __("Add to Segment");
        $data['extra_data'] = json_encode($params);

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

        $return = '<li><div class="div_droppable editable" id = "' . $chain->getId() .
                  '" ><span class="name" > ' . $data['name'] . ' </span ></div ></li>';

        return $return;
    }

    /**
     * @param \Licentia\Panda\Model\Autoresponders $autoresponder
     * @param \Licentia\Panda\Model\Subscribers    $subscriber
     * @param \Licentia\Panda\Model\Events         $cron
     * @param \Licentia\Panda\Model\Chains         $chain
     *
     * @return Addtosegment
     */
    public function run(
        \Licentia\Panda\Model\Autoresponders $autoresponder,
        \Licentia\Panda\Model\Subscribers $subscriber,
        \Licentia\Panda\Model\Events $cron,
        \Licentia\Panda\Model\Chains $chain
    ) {

        $data = json_decode($chain->getExtraData(), true);

        if (!$data) {
            return $this;
        }

        $segments = (array) $data['segments'];

        foreach ($segments as $segmentId) {
            $this->listSegmentsFactory->create()->addRecordToSegment(
                $subscriber->getId(),
                $segmentId,
                'subscriber_id'
            );
        }

        return $this;
    }
}
