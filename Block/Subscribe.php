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
 * @modified   03/06/20, 16:18 GMT
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
