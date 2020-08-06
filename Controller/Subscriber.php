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
