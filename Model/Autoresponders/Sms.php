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
 * Class Sms
 *
 * @package Licentia\Panda\Model\Autoresponders
 */

use Magento\Framework\Model;

/**
 * Class Sms
 *
 * @package Licentia\Panda\Model\Autoresponders
 */
class Sms extends AbstractModel
{

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

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
     * Email constructor.
     *
     * @param \Magento\Framework\UrlInterface                                  $url
     * @param \Magento\Framework\App\Config\ScopeConfigInterface               $scope
     * @param \Licentia\Panda\Model\ChainseditFactory                          $chainseditFactory
     * @param \Licentia\Panda\Model\TemplatesFactory                           $templatesFactory
     * @param \Licentia\Panda\Model\ResourceModel\Templates\CollectionFactory  $templatesCollection
     * @param \Licentia\Panda\Model\CampaignsFactory                           $campaignsFactory
     * @param \Licentia\Panda\Model\ServiceFactory                             $serviceFactory
     * @param \Licentia\Panda\Helper\Data                                      $pandaHelper
     * @param \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory    $sendersCollection
     * @param \Magento\Backend\Block\Template                                  $block
     * @param \Licentia\Panda\Model\ChainsFactory                              $chainsFactory
     * @param \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory $chainseditCollection
     * @param Model\Context                                                    $context
     * @param \Magento\Framework\Registry                                      $registry
     * @param Model\ResourceModel\AbstractResource|null                        $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null               $resourceCollection
     * @param array                                                            $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Config\ScopeConfigInterface $scope,
        \Licentia\Panda\Model\ChainseditFactory $chainseditFactory,
        \Licentia\Panda\Model\TemplatesFactory $templatesFactory,
        \Licentia\Panda\Model\ResourceModel\Templates\CollectionFactory $templatesCollection,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\ServiceFactory $serviceFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory $sendersCollection,
        \Magento\Backend\Block\Template $block,
        \Licentia\Panda\Model\ChainsFactory $chainsFactory,
        \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory $chainseditCollection,
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
        $this->urlBuilder = $url;

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
        $data['name'] = "Send Sms: " . $params['name'];
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

        $return = '<li><div  class="div_droppable editable" id = "' . $chain->getId() .
                  '" ><span class="name" > ' . $data['name'] . ' </span ></div></li>';

        return $return;
    }

    /**
     * @return string
     */
    public function render()
    {

        $senders = $this->sendersCollection->create()->getSenders('sms');

        $select = "<select name='sender_id'>";

        foreach ($senders as $sender) {
            $selected = '';
            if ($this->getData('sender_id') == $sender->getId()) {
                $selected = ' selected="select" ';
            }

            $select .= "<option $selected value='{$sender->getId()}'>" . $sender->getName() .
                       ' / ' . $sender->getOriginator() . "</option>";
        }

        $select .= "</select>";

        $img = $this->template->getViewFileUrl('Licentia_Panda::images/close.png');

        $class = $this->getData('chain_id') ? 'edit' : 'submit';
        $label = $this->getData('chain_id') ? __('Edit') : __('Add');
        $form = $this->getData('chain_id') ? 'edit_data' : 'add_data';

        $url = $this->urlBuilder->getUrl('*/*/help');

        $t = function ($t) {

            return __($t);
        };

        $return = <<<EOL
        <span class="formTriggersWrapper">
        <form class="$form" id="formTriggers" method="post" action="">
        <img class="close" src="$img" />
        <label>{$t('Name')}</label>
        <input type="text" value="{$this->getData('extra_data/name')}" class="required" name="name" placeholder="{$t(
            'Name'
        )}">
        <label>{$t('Sender')}</label>
        $select
        <label>{$t('Message')}</label>
        <textarea class="required" name="message" placeholder="{$t('Message')}">{$this->getData('extra_data/message')}</textarea>
        <a href="#" onClick='window.open("$url", "_blank", "toolbar=no,scrollbars=yes,resizable=yes,top=100,left=500,width=500,height=600"); return false;'>{$t(
            'Variables Available'
        )}</a>
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
     * @return bool|void
     */
    public function run(
        \Licentia\Panda\Model\Autoresponders $autoresponder,
        \Licentia\Panda\Model\Subscribers $subscriber,
        \Licentia\Panda\Model\Events $event,
        \Licentia\Panda\Model\Chains $chain
    ) {

        $extraData = json_decode($chain->getExtraData(), true);

        if (!$extraData) {
            return;
        }

        if (isset($extraData['subject'])) {
            $subject = $extraData['subject'];
        } else {
            $subject = $autoresponder->getName();
        }

        $data = [];
        $data['subject'] = $subject;
        $data['internal_name'] = '[AR] ' . $subject;
        $data['deploy_at'] = $event->getExecuteAt();
        $data['message'] = $extraData['message'];
        $data['sender_id'] = $extraData['sender_id'];
        $data['recurring'] = '0';
        $data['auto'] = '1';
        $data['type'] = 'sms';
        $data['previous_customers'] = $autoresponder->getPreviousCustomers();
        $data['autoresponder_id'] = $autoresponder->getId();
        $data['autoresponder_recipient'] = $subscriber->getCellphone();
        $data['autoresponder_event'] = $autoresponder->getEvent();
        $data['autoresponder_event_id'] = $event->getId();

        $service = $this->serviceFactory->create()->getEmailService();
        /** @var \Licentia\Panda\Model\Campaigns $campaign */
        $campaign = $this->campaignsFactory->create()
                                           ->setData($data)
                                           ->save();

        $data = [];
        $data['campaign'] = $campaign;
        $data['once'] = true;
        $data['subscribers'] = $subscriber->getId();

        $service->setData($data)
                ->buildQueue();

        $autoresponder->setTotalMessages($autoresponder->getTotalMessages() + 1)
                      ->save();

        return true;
    }
}
