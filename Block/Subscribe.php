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

namespace Licentia\Panda\Block;

use Magento\Framework\View\Element\Template;

/**
 * New products widget
 */
class Subscribe extends Template
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected $subscribersFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Licentia\Forms\Model\FormsFactory
     */
    protected $formsFactory;

    /**
     * @var Form\FormFactory
     */
    protected $formBlockFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * Subscribe constructor.
     *
     * @param Template\Context                         $context
     * @param \Licentia\Forms\Block\Form\FormFactory   $formBlockFactory
     * @param \Licentia\Forms\Model\FormsFactory       $formsFactory
     * @param \Licentia\Panda\Model\SubscribersFactory $subscribersFactory
     * @param \Magento\Customer\Model\CustomerFactory  $customerFactory
     * @param \Magento\Customer\Model\Session          $session
     * @param \Magento\Framework\Registry              $registry
     * @param array                                    $data
     */
    public function __construct(
        Template\Context $context,
        \Licentia\Forms\Block\Form\FormFactory $formBlockFactory,
        \Licentia\Forms\Model\FormsFactory $formsFactory,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        parent::__construct($context, $data);

        $this->formBlockFactory = $formBlockFactory;
        $this->registry = $registry;
        $this->formsFactory = $formsFactory;
        $this->subscribersFactory = $subscribersFactory;
        $this->customerFactory = $customerFactory;
        $this->session = $session;
    }

    /**
     * @return \Magento\Framework\Registry
     */
    public function getRegistry()
    {

        return $this->registry;
    }

    /**
     * @return string
     */
    public function getAction()
    {

        return $this->getUrl('panda/subscriber/saveGuest', ['_current' => true]);
    }

    /**
     * @return mixed
     */
    public function getSubscriber()
    {

        $id = $this->getRequest()->getParam('id');
        $code = $this->getRequest()->getParam('code');
        /** @var \Licentia\Panda\Model\Subscribers $subscriber */
        $subscriber = $this->subscribersFactory->create()->loadById($id);

        if ($subscriber->getCode() == $code && $subscriber->getId()) {
            return $subscriber;
        }

        return $this->registry->registry('panda_subscriber');
    }

    /**
     * @return mixed
     */
    public function getFormRender()
    {

        return $this->formBlockFactory->create()->setData('subscriber', $this->getSubscriber());
    }

    /**
     * @return mixed
     */
    public function getFormForManagePage()
    {

        return $this->formsFactory->create()
                                  ->getFormForManagePage($this->_storeManager->getStore()->getId());
    }
}
