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
 * Class Unsubscribe
 *
 * @package Licentia\Panda\Model\Autoresponders
 */

use Magento\Framework\Model;

/**
 * Class Unsubscribe
 *
 * @package Licentia\Panda\Model\Autoresponders
 */
class Unsubscribe extends AbstractModel
{

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected \Magento\Newsletter\Model\SubscriberFactory $subscribersFactory;

    /**
     * Unsubscribe constructor.
     *
     * @param \Magento\Newsletter\Model\SubscriberFactory                      $subscribersFactory
     * @param Model\Context                                                    $context
     * @param \Magento\Framework\Registry                                      $registry
     * @param Model\ResourceModel\AbstractResource                             $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb                    $resourceCollection
     * @param \Licentia\Panda\Model\ChainseditFactory                          $chainseditFactory
     * @param \Magento\Backend\Block\Template                                  $block
     * @param \Licentia\Panda\Model\ChainsFactory                              $chainsFactory
     * @param \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory $chainseditCollection
     * @param array                                                            $data
     */
    public function __construct(
        \Magento\Newsletter\Model\SubscriberFactory $subscribersFactory,
        \Licentia\Panda\Model\ChainseditFactory $chainseditFactory,
        \Magento\Backend\Block\Template $block,
        \Licentia\Panda\Model\ChainsFactory $chainsFactory,
        \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory $chainseditCollection,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

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
     * @throws \Exception
     */
    public function add()
    {

        $params = $this->getData('params');
        $parentId = '';
        $data = [];
        $data['chain_id'] = $this->getData('chain_id');
        $data['autoresponder_id'] = $params['autoresponder_id'];
        $data['name'] = "Change Subscriber Status to Unsubscribed";
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
     * @return string
     */
    public function render()
    {

        $img = $this->template->getViewFileUrl('Licentia_Panda::images/close.png');

        $class = $this->getData('chain_id') ? 'edit' : 'submit';
        $label = $this->getData('chain_id') ? 'Edit' : 'Add';
        $form = $this->getData('chain_id') ? 'edit_data' : 'add_data';

        $t = function ($t) {

            return __($t);
        };

        $return = <<<EOL
        <span class="formTriggersWrapper">
        <form class="$form" id="formTriggers" method="post" action="">
        <img class="close" src="$img" />
        <label>{$t('Change Subscriber Status to Unsubscribed')}</label>
        <input type="submit" class="$class" name="send" value="$label">
        </form>
        </span>
EOL;

        return $return;
    }

    /**
     * @param \Licentia\Panda\Model\Autoresponders $autoresponder
     * @param \Licentia\Panda\Model\Subscribers    $subscriber
     * @param \Licentia\Panda\Model\Events         $event
     * @param \Licentia\Panda\Model\Chains         $chain
     *
     * @return bool|\Licentia\Panda\Model\Subscribers|void
     */
    public function run(
        \Licentia\Panda\Model\Autoresponders $autoresponder,
        \Licentia\Panda\Model\Subscribers $subscriber,
        \Licentia\Panda\Model\Events $event,
        \Licentia\Panda\Model\Chains $chain
    ) {

        return $subscriber->unsubscribe();
    }
}
