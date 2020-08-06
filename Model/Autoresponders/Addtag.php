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
 * Class Addtag
 *
 * @package Licentia\Panda\Model\Autoresponders
 */

use Magento\Framework\Model;

/**
 * Class Addtag
 *
 * @package Licentia\Panda\Model\Autoresponders
 */
class Addtag extends AbstractModel
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\TagsFactory
     */
    protected $tagsFactory;

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected $subscribersFactory;

    /**
     * Addtag constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface               $scope
     * @param \Licentia\Panda\Model\ChainseditFactory                          $chainseditFactory
     * @param \Licentia\Panda\Helper\Data                                      $pandaHelper
     * @param \Magento\Backend\Block\Template                                  $block
     * @param \Licentia\Panda\Model\ChainsFactory                              $chainsFactory
     * @param \Licentia\Panda\Model\SubscribersFactory                         $subscribersFactory
     * @param \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory $chainseditCollection
     * @param \Licentia\Panda\Model\TagsFactory                                $tagsFactory
     * @param Model\Context                                                    $context
     * @param \Magento\Framework\Registry                                      $registry
     * @param Model\ResourceModel\AbstractResource|null                        $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null               $resourceCollection
     * @param array                                                            $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scope,
        \Licentia\Panda\Model\ChainseditFactory $chainseditFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Backend\Block\Template $block,
        \Licentia\Panda\Model\ChainsFactory $chainsFactory,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory $chainseditCollection,
        \Licentia\Panda\Model\TagsFactory $tagsFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        $this->pandaHelper = $pandaHelper;
        $this->scopeConfig = $scope;
        $this->tagsFactory = $tagsFactory;
        $this->subscribersFactory = $subscribersFactory;

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

        $tagsHash = $this->tagsFactory->create()->getAllTagsHash();

        $array = (array) $this->getData('extra_data/tags');
        $selectCustomer = "<select name='tags[]' size='7' required='required' multiple='multiple'>";
        foreach ($tagsHash as $key => $value) {
            $selected = '';

            if (in_array($key, $array)) {
                $selected = ' selected="selected" ';
            }

            $selectCustomer .= "<option $selected value='{$key}'>{$value}</option>";
        }
        $selectCustomer .= "</select>";

        $tagsHash = "<label>Add Tag(s)</label>$selectCustomer";

        $img = $this->template->getViewFileUrl('Licentia_Panda::images/close.png');

        $class = $this->getData('chain_id') ? 'edit' : 'submit';
        $label = $this->getData('chain_id') ? 'Edit' : 'Add';
        $form = $this->getData('chain_id') ? 'edit_data' : 'add_data';

        $return = <<<EOL
        <span class="formTriggersWrapper">
        <form class="$form" id="formTriggers" method="post" action="">
        <img class="close" src="$img" />
        $tagsHash
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

        $tags = $this->tagsFactory->create()->getAllTagsHash();
        $tagsNames = array_intersect_key($tags, array_flip($params['tags']));
        $data['name'] = 'Add Tag (' . implode(',', $tagsNames) . ')';

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
     * @param \Licentia\Panda\Model\Events         $event
     * @param \Licentia\Panda\Model\Chains         $chain
     *
     * @return bool
     */
    public function run(
        \Licentia\Panda\Model\Autoresponders $autoresponder,
        \Licentia\Panda\Model\Subscribers $subscriber,
        \Licentia\Panda\Model\Events $event,
        \Licentia\Panda\Model\Chains $chain
    ) {

        $data = json_decode($chain->getExtraData(), true);

        if (!$data) {
            return false;
        }

        $tags = (array) $data['tags'];

        foreach ($tags as $tagId) {
            $this->subscribersFactory->create()->addTagToSubscriber($subscriber, $tagId);
        }

        return true;
    }
}
