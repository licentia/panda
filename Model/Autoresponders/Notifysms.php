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
 * @modified   03/06/20, 14:14 GMT
 *
 */

namespace Licentia\Panda\Model\Autoresponders;

/**
 * Class Notifysms
 *
 * @package Licentia\Panda\Model\Autoresponders
 */

use Magento\Framework\Model;

/**
 * Class Notifysms
 *
 * @package Licentia\Panda\Model\Autoresponders
 */
class Notifysms extends AbstractModel
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
     * @var \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory
     */
    protected $sendersCollection;

    /**
     * @var \Licentia\Panda\Model\PopupsFactory
     */
    protected $sendersFactory;

    /**
     * @var \Magento\Email\Model\TemplateFactory
     */
    protected $templateFactory = null;

    /**
     * @var \Magento\Newsletter\Model\Template\FilterFactory
     */
    protected $filterFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * Notifysms constructor.
     *
     * @param \Magento\Newsletter\Model\Template\FilterFactory                 $filterFactory
     * @param \Magento\Email\Model\TemplateFactory                             $templateFactory
     * @param \Magento\Customer\Model\CustomerFactory                          $customerFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface               $scope
     * @param \Licentia\Panda\Model\ChainseditFactory                          $chainseditFactory
     * @param \Licentia\Panda\Helper\Data                                      $pandaHelper
     * @param \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory    $sendersCollection
     * @param \Licentia\Panda\Model\SendersFactory                             $sendersFactory
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
        \Magento\Newsletter\Model\Template\FilterFactory $filterFactory,
        \Magento\Email\Model\TemplateFactory $templateFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scope,
        \Licentia\Panda\Model\ChainseditFactory $chainseditFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory $sendersCollection,
        \Licentia\Panda\Model\SendersFactory $sendersFactory,
        \Magento\Backend\Block\Template $block,
        \Licentia\Panda\Model\ChainsFactory $chainsFactory,
        \Licentia\Panda\Model\ResourceModel\Chainsedit\CollectionFactory $chainseditCollection,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        $this->filterFactory = $filterFactory;
        $this->templateFactory = $templateFactory;
        $this->customerFactory = $customerFactory;

        $this->sendersCollection = $sendersCollection;
        $this->sendersFactory = $sendersFactory;
        $this->pandaHelper = $pandaHelper;
        $this->scopeConfig = $scope;

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
        $data['name'] = "Send SMS : " . $params['recipient'];
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

        $senders = $this->sendersCollection->create()->getSenders('sms');
        $selectSender = "<select name='sender_id'>";

        /** @var \Licentia\Panda\Model\Senders $sender */
        foreach ($senders as $sender) {
            $selected = '';
            if ($this->getData('sender_id') == $sender->getId()) {
                $selectSender = ' selected="select" ';
            }

            $selectSender .= "<option $selected value='{$sender->getId()}'>{$sender->getName()}</option>";
        }

        $selectSender .= "</select>";

        $img = $this->template->getViewFileUrl('Licentia_Panda::images/close.png');

        $class = $this->getData('chain_id') ? 'edit' : 'submit';
        $label = $this->getData('chain_id') ? 'Edit' : 'Add';
        $form = $this->getData('chain_id') ? 'edit_data' : 'add_data';

        $cellphone = __('Please use the format: countryCode-number. Eg: 1-555555555');

        $t = function ($t) {

            return __($t);
        };

        $return = <<<EOL
        <span class="formTriggersWrapper">
        <form class="$form" id="formTriggers" method="post" action="">
        <img class="close" src="$img" />
        <label>{$t('Sender')}</label>
        $selectSender        
        <label>{$t('Recipient')}</label>
        <small><em>$cellphone</em></small>
        <input type="text" value="{$this->getData(
            'extra_data/recipient'
        )}" class="required" name="recipient" placeholder="{$t('SMS Recipient')}">
        <label>{$t('Message')}</label>
        <textarea  class="required" name="message">{$this->getData('extra_data/message')}</textarea>
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

        $data = json_decode($chain->getExtraData(), true);

        if (!$data) {
            return false;
        }

        $sms = $data['recipient'];
        $message = $data['message'];

        try {
            if (stripos($message, '{{') !== false) {
                $variables = [];
                $variables['subscriber'] = $subscriber;
                $variables['autoresponder'] = $autoresponder;
                $variables['customer'] = $this->customerFactory->create();

                if ($subscriber->getCustomerId()) {
                    $variables['customer']->load($subscriber->getCustomerId());
                }

                $template = $this->templateFactory->create();

                $template->setTemplateFilter(
                    $this->filterFactory->create()->setVariables($variables)
                );

                $template->setTemplateType('html');
                $template->setTemplateText($message);
                $template->emulateDesign($subscriber->getStoreId());
                $message = $this->_appState->emulateAreaCode(
                    \Magento\Email\Model\AbstractTemplate::DEFAULT_DESIGN_AREA,
                    [$template, 'getProcessedTemplate']
                );
            }

            $sender = $this->sendersFactory->create()->load($data['sender_id']);

            $transport = $this->pandaHelper->getSmsTransport($sender);

            if (!$transport) {
                return false;
            }

            $send = $transport->sendSMS($sms, $message);

            if (!$send) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Cannot send autoresponders SMS notifications. Sender Id:' . $data['sender_id']));
            }
        } catch (\Exception $e) {
            $this->pandaHelper->logException($e);

            return false;
        }
    }
}
