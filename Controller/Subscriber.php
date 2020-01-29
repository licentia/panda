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

namespace Licentia\Panda\Controller;

/**
 * Class Subscriber
 *
 * @package Licentia\Panda\Controller
 */
class Subscriber extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected $subscribersFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $coreSubscribersFactory;

    /**
     * @var \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory
     */
    protected $coreSubscriberCollection;

    /**
     * @var \Licentia\Panda\Model\UnsubscribesFactory
     */
    protected $unsubscribesFactory;

    /**
     * @var \Licentia\Panda\Model\CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * @var \Licentia\Forms\Model\FormsFactory
     */
    protected $formsFactory;

    /**
     * @var \Licentia\Forms\Model\FormEntriesFactory
     */
    protected $formEntriesFactory;

    /**
     *
     */
    public function execute()
    {
    }

    /**
     * @param \Licentia\Forms\Model\FormsFactory                                   $formsFactory
     * @param \Licentia\Forms\Model\FormEntriesFactory                             $formEntriesFactory
     * @param \Magento\Framework\App\Action\Context                                $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator                       $formKeyValidator
     * @param \Magento\Customer\Api\CustomerRepositoryInterface                    $customerRepository
     * @param \Magento\Framework\Registry                                          $coreRegistry
     * @param \Licentia\Panda\Helper\Data                                          $pandaHelper
     * @param \Magento\Customer\Model\Session                                      $session
     * @param \Licentia\Panda\Model\SubscribersFactory                             $subscribersFactory
     * @param \Magento\Newsletter\Model\SubscriberFactory                          $coreSubscribersFactory
     * @param \Magento\Store\Model\StoreManagerInterface                           $storeManagerInterface
     * @param \Magento\Framework\View\Result\PageFactory                           $resultPageFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory                  $resultForwardFactory
     * @param \Licentia\Panda\Model\CampaignsFactory                               $campaignsFactory
     * @param \Licentia\Panda\Model\UnsubscribesFactory                            $unsubscribesFactory
     * @param \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory $coreSubscriberCollection
     */
    public function __construct(
        \Licentia\Forms\Model\FormsFactory $formsFactory,
        \Licentia\Forms\Model\FormEntriesFactory $formEntriesFactory,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Customer\Model\Session $session,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        \Magento\Newsletter\Model\SubscriberFactory $coreSubscribersFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Licentia\Panda\Model\CampaignsFactory $campaignsFactory,
        \Licentia\Panda\Model\UnsubscribesFactory $unsubscribesFactory,
        \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory $coreSubscriberCollection
    ) {

        parent::__construct($context);

        $this->formEntriesFactory = $formEntriesFactory;
        $this->formsFactory = $formsFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerRepository = $customerRepository;
        $this->campaignsFactory = $campaignsFactory;
        $this->coreSubscriberCollection = $coreSubscriberCollection;
        $this->unsubscribesFactory = $unsubscribesFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->subscribersFactory = $subscribersFactory;
        $this->coreSubscribersFactory = $coreSubscribersFactory;
        $this->pandaHelper = $pandaHelper;
        $this->registry = $coreRegistry;
        $this->customerSession = $session;
        $this->storeManager = $storeManagerInterface;
    }
}
