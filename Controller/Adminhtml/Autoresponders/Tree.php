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

namespace Licentia\Panda\Controller\Adminhtml\Autoresponders;

use Magento\Backend\App\Action;

/**
 * Class Tree
 *
 * @package Licentia\Panda\Controller\Adminhtml\Autoresponders
 */
class Tree extends \Licentia\Panda\Controller\Adminhtml\Autoresponders
{

    /**
     * @var
     */
    protected $classes;

    /**
     * @var \Licentia\Panda\Model\ChainseditFactory
     */
    protected $chainseditFactory;

    /**
     * Tree constructor.
     *
     * @param \Licentia\Panda\Model\Autoresponders\ConditionFactory         $condition
     * @param \Licentia\Panda\Model\Autoresponders\CustomerFactory          $customer
     * @param \Licentia\Panda\Model\Autoresponders\EmailFactory             $email
     * @param \Licentia\Panda\Model\Autoresponders\NotifyFactory            $notify
     * @param \Licentia\Panda\Model\Autoresponders\SubscribersFactory       $subscriber
     * @param \Licentia\Panda\Model\Autoresponders\UnsubscribeFactory       $unsubscribe
     * @param \Licentia\Panda\Model\Autoresponders\WaitFactory              $wait
     * @param \Licentia\Panda\Model\Autoresponders\WebhookFactory           $webhook
     * @param \Licentia\Panda\Model\Autoresponders\SmsFactory               $sms
     * @param \Licentia\Panda\Model\Autoresponders\NotifysmsFactory         $notifysmsFactory
     * @param \Licentia\Panda\Model\Autoresponders\AddtosegmentFactory      $addtosegmentFactory
     * @param \Licentia\Panda\Model\Autoresponders\RemovefromsegmentFactory $removefromsegmentFactory
     * @param \Licentia\Panda\Model\Autoresponders\AddtagFactory            $addtagFactory
     * @param \Licentia\Panda\Model\Autoresponders\RemovetagFactory         $removetagFactory
     * @param \Licentia\Panda\Model\ChainseditFactory                       $chainseditFactory
     * @param Action\Context                                                $context
     * @param \Magento\Framework\View\Result\PageFactory                    $resultPageFactory
     * @param \Magento\Framework\Registry                                   $registry
     * @param \Licentia\Panda\Helper\Data                                   $pandaHelper
     * @param \Licentia\Panda\Model\AutorespondersFactory                   $autorespondersFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory             $resultForwardFactory
     * @param \Magento\Framework\View\Result\LayoutFactory                  $resultLayoutFactory
     */
    public function __construct(
        \Licentia\Panda\Model\Autoresponders\ConditionFactory $condition,
        \Licentia\Panda\Model\Autoresponders\CustomerFactory $customer,
        \Licentia\Panda\Model\Autoresponders\EmailFactory $email,
        \Licentia\Panda\Model\Autoresponders\NotifyFactory $notify,
        \Licentia\Panda\Model\Autoresponders\SubscribersFactory $subscriber,
        \Licentia\Panda\Model\Autoresponders\UnsubscribeFactory $unsubscribe,
        \Licentia\Panda\Model\Autoresponders\WaitFactory $wait,
        \Licentia\Panda\Model\Autoresponders\WebhookFactory $webhook,
        \Licentia\Panda\Model\Autoresponders\SmsFactory $sms,
        \Licentia\Panda\Model\Autoresponders\NotifysmsFactory $notifysmsFactory,
        \Licentia\Panda\Model\Autoresponders\AddtosegmentFactory $addtosegmentFactory,
        \Licentia\Panda\Model\Autoresponders\RemovefromsegmentFactory $removefromsegmentFactory,
        \Licentia\Panda\Model\Autoresponders\AddtagFactory $addtagFactory,
        \Licentia\Panda\Model\Autoresponders\RemovetagFactory $removetagFactory,
        \Licentia\Panda\Model\ChainseditFactory $chainseditFactory,
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {

        $this->chainseditFactory = $chainseditFactory;

        $this->classes['condition'] = $condition;
        $this->classes['customer'] = $customer;
        $this->classes['email'] = $email;
        $this->classes['notify'] = $notify;
        $this->classes['subscriber'] = $subscriber;
        $this->classes['unsubscribe'] = $unsubscribe;
        $this->classes['wait'] = $wait;
        $this->classes['webhook'] = $webhook;
        $this->classes['sms'] = $sms;
        $this->classes['notifysms'] = $notifysmsFactory;
        $this->classes['addtosegment'] = $addtosegmentFactory;
        $this->classes['removefromsegment'] = $removefromsegmentFactory;
        $this->classes['addtag'] = $addtagFactory;
        $this->classes['removetag'] = $removetagFactory;

        parent::__construct(
            $context,
            $resultPageFactory,
            $registry,
            $pandaHelper,
            $autorespondersFactory,
            $resultForwardFactory,
            $resultLayoutFactory
        );
    }

    /**
     *
     */
    public function execute()
    {

        $resultPage = $this->resultPageFactory->create();

        $action = $this->getRequest()->getParam('op');
        $type = $this->getRequest()->getParam('type');
        $chain = $this->chainseditFactory->create();

        if ($id = $this->getRequest()->getParam('id')) {
            $chain->load($id);
            $type = $chain->getEvent();
        }

        $type = strtolower($type);

        $class = $this->classes[$type]->create();
        $class->setData(
            'params',
            $this->getRequest()->getParams()
        );
        $class->setData('chain', $chain);
        $class->setData('chain_id', $id);
        $class->setData('extra_data', json_decode($chain->getData('extra_data'), true));

        $class->setData('mode', 'add');
        if ($id) {
            $class->setData('mode', 'edit');
        }

        if ($action == 'add') {
            $return = $class->add();
        }

        if ($action == 'edit') {
            $return = $class->add();
        }

        if ($action == 'renderAdd' || $action == 'editform') {
            $return = $class->render();
        }

        if ($action == 'delete') {
            $return = $class->deleteChain();
        }

        $this->_view->loadLayout('panda_empty');
        $resultPage->getLayout()
                   ->getBlock('panda.empty')
                   ->setData('content', $return);

        return $resultPage;
    }
}
