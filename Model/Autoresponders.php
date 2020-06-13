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

namespace Licentia\Panda\Model;

/**
 * Class Autoresponders
 *
 * @package Licentia\Panda\Model
 */
class Autoresponders extends \Magento\Rule\Model\AbstractModel
{

    const UTM_URL_PARAMS = [
        'utm_campaign' => 'Campaign (utm_campaign)',
        'utm_source'   => 'Source (utm_source)',
        'utm_medium'   => 'Medium (utm_medium)',
        'utm_term'     => 'Term (utm_term)',
        'utm_content'  => 'Content (utm_content)',
    ];

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_autoresponders';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'autoresponders';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var AutorespondersFactory
     */
    protected $autorespondersFactory;

    /**
     * @var EventsFactory
     */
    protected $eventsFactory;

    /**
     * @var ResourceModel\Events\CollectionFactory
     */
    protected $eventsCollection;

    /**
     * @var ResourceModel\Chains\CollectionFactory
     */
    protected $chainsCollection;

    /**
     * @var ResourceModel\Chainsedit\CollectionFactory
     */
    protected $chainseditCollection;

    /**
     * @var SubscribersFactory
     */
    protected $subscribersFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $helperData;

    /**
     * @var TemplatesFactory
     */
    protected $templatesFactory;

    /**
     * @var ChainsFactory
     */
    protected $chainsFactory;

    /**
     * @var ResourceModel\Subscribers\CollectionFactory
     */
    protected $subscribersCollection;

    /**
     * @var ResourceModel\Cancellation\CollectionFactory
     */
    protected $cancellationCollection;

    /**
     * @var CancellationFactory
     */
    protected $cancellationFactory;

    /**
     * @var ServiceFactory
     */
    protected $serviceFactory;

    /**
     * @var ResourceModel\Campaigns\CollectionFactory
     */
    protected $campaignsCollection;

    /**
     * @var ResourceModel\Links\CollectionFactory
     */
    protected $linksCollection;

    /**
     * @var CampaignsFactory
     */
    protected $campaignsFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */

    protected $quoteCollection;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollection;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Sales\Model\Order\ConfigFactory
     */
    protected $configFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Session
     */
    protected $pandaSession;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollection;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory
     */
    protected $invoiceCollection;

    /**
     * @var \Magento\Search\Helper\Data
     */
    protected $searchHelper;

    /**
     * @var ResourceModel\Autoresponders\CollectionFactory
     */
    protected $autorespondersCollection;

    /**
     * @var \Licentia\Equity\Model\SegmentsFactory
     */
    protected $metadataFactory;

    /**
     * @var
     */
    protected $classes;

    /**
     * @var Popups\Condition\CombineFactory
     */
    protected $conditionsCombine;

    /**
     * @var Popups\Action\CollectionFactory
     */
    protected $collectionCombine;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * @var \Licentia\Panda\Helper\DomHelper
     */
    protected $domHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var TagsFactory
     */
    protected $tagsFactory;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $coreSubscribersFactory;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Autoresponders::class);
    }

    /**
     * @var array
     */
    protected $config = [
        'last_activity'     => ['days' => true],
        'product_cycle'     => ['days' => true],
        'customer_birthday' => ['days' => true],
    ];

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface $_cacheState
     */
    protected $_cacheState;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory
     */
    protected $sendersCollection;

    /**
     * Autoresponders constructor.
     *
     * @param \Magento\Framework\App\Cache\StateInterface                        $cacheState
     * @param \Magento\Newsletter\Model\SubscriberFactory                        $coreSubscribersFactory
     * @param TagsFactory                                                        $tagsFactory
     * @param \Magento\Framework\App\Cache\TypeListInterface                     $typeList
     * @param \Magento\Framework\Data\FormFactory                                $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface               $localeDate
     * @param Popups\Condition\CombineFactory                                    $combineFactory
     * @param Popups\Action\CollectionFactory                                    $collectionFactory
     * @param \Magento\Framework\Filesystem                                      $filesystem
     * @param Autoresponders\ConditionFactory                                    $condition
     * @param Autoresponders\CustomerFactory                                     $customer
     * @param Autoresponders\EmailFactory                                        $email
     * @param Autoresponders\NotifyFactory                                       $notify
     * @param Autoresponders\NotifysmsFactory                                    $notifysmsFactory
     * @param Autoresponders\AddtagFactory                                       $addtagFactory
     * @param Autoresponders\RemovetagFactory                                    $removetagFactory
     * @param Autoresponders\AddtosegmentFactory                                 $addtosegmentFactory
     * @param Autoresponders\RemovefromsegmentFactory                            $removefromsegmentFactory
     * @param Autoresponders\SubscribersFactory                                  $subscriber
     * @param Autoresponders\UnsubscribeFactory                                  $unsubscribe
     * @param Autoresponders\WaitFactory                                         $wait
     * @param Autoresponders\WebhookFactory                                      $webhook
     * @param Autoresponders\SmsFactory                                          $sms
     * @param \Magento\Framework\App\Config\ScopeConfigInterface                 $scope
     * @param \Magento\Framework\Model\Context                                   $context
     * @param \Magento\Framework\Registry                                        $registry
     * @param \Licentia\Equity\Model\MetadataFactory                             $metadataFactory
     * @param AutorespondersFactory                                              $autorespondersFactory
     * @param Session                                                            $sessionFactory
     * @param EventsFactory                                                      $eventsFactory
     * @param CampaignsFactory                                                   $campaignsFactory
     * @param ChainsFactory                                                      $chainsFactory
     * @param SubscribersFactory                                                 $subscribersFactory
     * @param TemplatesFactory                                                   $templatesFactory
     * @param ServiceFactory                                                     $serviceFactory
     * @param CancellationFactory                                                $cancellationFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date                     $dateFilter
     * @param ResourceModel\Events\CollectionFactory                             $eventsCollection
     * @param ResourceModel\Autoresponders\CollectionFactory                     $autorespondersCollection
     * @param ResourceModel\Subscribers\CollectionFactory                        $subscribersCollection
     * @param ResourceModel\Chains\CollectionFactory                             $chainsCollection
     * @param ResourceModel\Chainsedit\CollectionFactory                         $chainseditCollection
     * @param ResourceModel\Campaigns\CollectionFactory                          $campaignsCollection
     * @param ResourceModel\Links\CollectionFactory                              $linksCollection
     * @param \Licentia\Panda\Helper\Data                                        $newsletterData
     * @param \Licentia\Panda\Helper\DomHelper                                   $domHelper
     * @param \Magento\Store\Model\StoreManagerInterface                         $storeManagerInterface
     * @param \Magento\Checkout\Model\Session                                    $checkout
     * @param \Magento\Customer\Model\Session                                    $customerSession
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory   $customerCollection
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory         $quoteCollection
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory         $orderCollection
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollection
     * @param \Magento\Catalog\Api\ProductRepositoryInterface                    $productRepository
     * @param \Magento\Checkout\Model\Cart                                       $cart
     * @param \Magento\Sales\Model\Order\ConfigFactory                           $configFactory
     * @param \Magento\Search\Helper\Data                                        $searchHelper
     * @param \Magento\Customer\Model\CustomerFactory                            $customerFactory
     * @param ResourceModel\Cancellation\CollectionFactory                       $cancellationCollection
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null       $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null                 $resourceCollection
     * @param array                                                              $data
     */
    public function __construct(
        \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory $sendersCollection,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Newsletter\Model\SubscriberFactory $coreSubscribersFactory,
        TagsFactory $tagsFactory,
        \Magento\Framework\App\Cache\TypeListInterface $typeList,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Licentia\Panda\Model\Popups\Condition\CombineFactory $combineFactory,
        \Licentia\Panda\Model\Popups\Action\CollectionFactory $collectionFactory,
        \Magento\Framework\Filesystem $filesystem,
        Autoresponders\ConditionFactory $condition,
        Autoresponders\CustomerFactory $customer,
        Autoresponders\EmailFactory $email,
        Autoresponders\NotifyFactory $notify,
        Autoresponders\NotifysmsFactory $notifysmsFactory,
        Autoresponders\AddtagFactory $addtagFactory,
        Autoresponders\RemovetagFactory $removetagFactory,
        Autoresponders\AddtosegmentFactory $addtosegmentFactory,
        Autoresponders\RemovefromsegmentFactory $removefromsegmentFactory,
        Autoresponders\SubscribersFactory $subscriber,
        Autoresponders\UnsubscribeFactory $unsubscribe,
        Autoresponders\WaitFactory $wait,
        Autoresponders\WebhookFactory $webhook,
        Autoresponders\SmsFactory $sms,
        \Magento\Framework\App\Config\ScopeConfigInterface $scope,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Licentia\Equity\Model\MetadataFactory $metadataFactory,
        AutorespondersFactory $autorespondersFactory,
        Session $sessionFactory,
        EventsFactory $eventsFactory,
        CampaignsFactory $campaignsFactory,
        ChainsFactory $chainsFactory,
        SubscribersFactory $subscribersFactory,
        TemplatesFactory $templatesFactory,
        ServiceFactory $serviceFactory,
        CancellationFactory $cancellationFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        ResourceModel\Events\CollectionFactory $eventsCollection,
        ResourceModel\Autoresponders\CollectionFactory $autorespondersCollection,
        ResourceModel\Subscribers\CollectionFactory $subscribersCollection,
        ResourceModel\Chains\CollectionFactory $chainsCollection,
        ResourceModel\Chainsedit\CollectionFactory $chainseditCollection,
        ResourceModel\Campaigns\CollectionFactory $campaignsCollection,
        ResourceModel\Links\CollectionFactory $linksCollection,
        \Licentia\Panda\Helper\Data $newsletterData,
        \Licentia\Panda\Helper\DomHelper $domHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Checkout\Model\Session $checkout,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection,
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollection,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollection,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Sales\Model\Order\ConfigFactory $configFactory,
        \Magento\Search\Helper\Data $searchHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        ResourceModel\Cancellation\CollectionFactory $cancellationCollection,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);

        $this->sendersCollection = $sendersCollection;

        $this->_cacheState = $cacheState;

        $this->coreSubscribersFactory = $coreSubscribersFactory;

        $this->dateFilter = $dateFilter;

        $this->domHelper = $domHelper;

        $this->cacheTypeList = $typeList;

        $this->collectionCombine = $collectionFactory;

        $this->conditionsCombine = $combineFactory;

        $this->filesystem = $filesystem;

        $this->checkoutSession = $checkout;

        $this->metadataFactory = $metadataFactory;

        $this->scopeConfig = $scope;

        $this->autorespondersFactory = $autorespondersFactory;

        $this->eventsFactory = $eventsFactory;

        $this->eventsCollection = $eventsCollection;

        $this->chainsCollection = $chainsCollection;

        $this->chainseditCollection = $chainseditCollection;

        $this->subscribersFactory = $subscribersFactory;

        $this->helperData = $newsletterData;

        $this->templatesFactory = $templatesFactory;

        $this->chainsFactory = $chainsFactory;

        $this->subscribersCollection = $subscribersCollection;

        $this->cancellationCollection = $cancellationCollection;

        $this->cancellationFactory = $cancellationFactory;

        $this->serviceFactory = $serviceFactory;

        $this->campaignsCollection = $campaignsCollection;

        $this->linksCollection = $linksCollection;

        $this->campaignsFactory = $campaignsFactory;

        $this->customerSession = $customerSession;

        $this->quoteCollection = $quoteCollection;

        $this->customerCollection = $customerCollection;

        $this->orderCollection = $orderCollection;

        $this->customerFactory = $customerFactory;

        $this->invoiceCollection = $invoiceCollection;

        $this->cart = $cart;

        $this->configFactory = $configFactory;

        $this->storeManager = $storeManagerInterface;

        $this->searchHelper = $searchHelper;

        $this->productRepository = $productRepository;

        $this->autorespondersCollection = $autorespondersCollection;

        $this->pandaSession = $sessionFactory;

        $this->timezone = $localeDate;

        $this->tagsFactory = $tagsFactory;

        $this->classes['condition'] = $condition;
        $this->classes['customer'] = $customer;
        $this->classes['email'] = $email;
        $this->classes['notify'] = $notify;
        $this->classes['subscriber'] = $subscriber;
        $this->classes['unsubscribe'] = $unsubscribe;
        $this->classes['wait'] = $wait;
        $this->classes['webhook'] = $webhook;
        $this->classes['notifysms'] = $notifysmsFactory;
        $this->classes['sms'] = $sms;
        $this->classes['addtag'] = $addtagFactory;
        $this->classes['removetag'] = $removetagFactory;
        $this->classes['addtosegment'] = $addtosegmentFactory;
        $this->classes['removefromsegment'] = $removefromsegmentFactory;
    }

    /**
     *
     * @param $event
     *
     * @return array
     */
    public function getConfigInfo($event = null)
    {

        if (!$event) {
            $event = $this->getEvent();
        }

        if (!$event && $this->getId()) {
            $eventModel = $this->autorespondersFactory->create()->load($this->getId());
            $event = $eventModel->getEvent();
        }

        if (isset($this->config[$event])) {
            return $this->config[$event];
        }

        return [];
    }

    /**
     * @return array
     */
    public function getAutorespondersTriggersDetails()
    {

        $status = $this->configFactory->create()->getStatuses();
        $return = [
            'campaign_open'         => [
                'name'        => __('Campaign - Open'),
                'description' => __(
                    'This trigger will fire when a subscriber clicks on any link in a specific campaign he received.'
                ),
            ],
            'campaign_click'        => [
                'name'        => __('Campaign - Clicked Any Campaign Link'),
                'description' => __(
                    'This trigger will fire when a subscriber clicks on any link in a campaign he received. ' .
                    'Any campaign.'
                ),
            ],
            'campaign_link'         => [
                'name'        => __('Campaign - Clicked Specific Campaign Link'),
                'description' => __(
                    'This trigger will fire when a subscriber clicks on a specific link in a campaign he received.'
                ),
            ],
            'new_search'            => [
                'name'        => __('Search - New'),
                'description' => __('When a user makes a search'),
            ],
            'new_review'            => [
                'name'        => __('Review - New'),
                'description' => __('When a user makes a new review'),
            ],
            'new_review_approved'   => [
                'name'        => __('Review - Status Changes to Approved'),
                'description' => __('When the review status changes to approved'),
            ],
            'new_review_self'       => [
                'name'        => __('Review - On a Bought Product'),
                'description' => __(
                    'When the subscriber makes a review on a product he has purchased'
                ),
            ],
            'product_cycle'         => [
                'name'        => __('Product - Expected Lifespan Reached (X days before)'),
                'description' => __(
                    "When the product consumption lifespan is approaching, this event will be fired X " .
                    "days before that happens (X is defined in the autoresponder details)"
                ),
            ],
            'new_abandoned'         => [
                'name'        => __('Shopping Cart - New Abandoned Cart Appears'),
                'description' => __('When a new abandoned cart appears'),
            ],
            'new_account'           => [
                'name'        => __('Customer - Signup'),
                'description' => __('When a new account is created'),
            ],
            'new_login'             => [
                'name'        => __('Customer - Login'),
                'description' => __('When the user logins to your store'),
            ],
            'last_activity'         => [
                'name'        => __('Customer - Last Activity'),
                'description' => __('The last activity recorded for the customer in your store'),
            ],
            'customer_birthday'     => [
                'name'        => __('Customer - Birthday'),
                'description' => __(
                    'This trigger will fire X days before the customer birthday ' .
                    '(X is defined in the autoresponder details)'
                ),
            ],
            'customer_group'        => [
                'name'        => __('Customer - Group Changes'),
                'description' => __('When the customer is moved to a new customer group'),
            ],
            'customer_attribute'    => [
                'name'        => __('Customer - Attribute Value Changes'),
                'description' => __('When the customer attribute changes'),
            ],
            'shipment_new_track'    =>
                [
                    'name'        => __(
                        'Shipment - Tracking Added To Shipment / New Ship. W/ Tracking)'
                    ),
                    'description' => __(
                        'When a new track is added to an existing shipment, or a new shipment with tracking is added'
                    ),
                ],
            'shipment_new_no_track' => [
                'name'        => __('Shipment - New Shipment (NO tracking number)'),
                'description' => __('When a new shipment is registered with no track attached to it'),
            ],
            'order_new'             => [
                'name'        => __('Order - New Order'),
                'description' => __('When a new order is made'),
            ],
            'order_product'         => [
                'name'        => __('Order - Bought Specific Product'),
                'description' => __('When the customer buys a specific product'),
            ],
            'new_form_entry'        => [
                'name'        => __('Forms - New Entry'),
                'description' => __('When a new entry is added to a form'),
            ],
            'utm_campaign'          => [
                'name'        => __('UTM Campaigns'),
                'description' => __('When a URL param originates from an external Marketing Campaign'),
            ],
            'internal_event'        => [
                'name'        => __('Magento - Internal Event'),
                'description' => __('When one of Magento internal events is dispatched'),
            ],
        ];

        foreach ($status as $key => $value) {
            $return['order_status_' . $key] = [
                'name'        => __('Order - Status Changes To ') . $value,
                'description' => __('When the order status changes to ' . $value),
            ];
        }

        return $return;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {

        $status = $this->getAutorespondersTriggersDetails();
        $return = [];

        foreach ($status as $key => $value) {
            $return[$key] = $value['name'];
        }

        return $return;
    }

    /**
     * @return array
     */
    public function getActionsList()
    {

        $emailSenders = $this->sendersCollection->create()->getSenders('email')->getSize();
        $smsSenders = $this->sendersCollection->create()->getSenders('sms')->getSize();

        $actions = [];
        if ($emailSenders > 0) {
            $actions[] = [
                'id'          => 'notify',
                'name'        => 'Email Notification',
                'description' => 'Get an email notification when some event occurs',
            ];
        }
        if ($smsSenders > 0) {
            $actions[] = [
                'id'          => 'notifysms',
                'name'        => 'SMS Notification',
                'description' => 'Get an SMS notification when some event occurs',
            ];
        }
        if ($emailSenders > 0) {
            $actions[] = ['id' => 'email', 'name' => 'Email Campaign', 'description' => 'Send an email'];
        }
        if ($smsSenders > 0) {
            $actions[] = ['id' => 'sms', 'name' => 'SMS Campaign', 'description' => 'Send an SMS'];
        }
        $actions[] = [
            'id'          => 'wait',
            'name'        => 'Wait',
            'description' => 'Wait a specific amount of time before step into the next action',
        ];
        $actions[] = ['id' => 'condition', 'name' => 'Condition', 'description' => 'Build a condition to be checked'];
        $actions[] = ['id' => 'customer', 'name' => 'Customer', 'description' => 'Update Customer Information'];
        $actions[] = ['id' => 'subscriber', 'name' => 'Subscriber', 'description' => 'Update Subscriber Information'];
        $actions[] = [
            'id'          => 'unsubscribe',
            'name'        => 'Unsubscribe',
            'description' => "Cancel Subscriber's Subscription",
        ];
        $actions[] = ['id' => 'webhook', 'name' => 'Web hook', 'description' => 'Call an external service'];
        $actions[] = [
            'id'          => 'addtosegment',
            'name'        => 'Add to Segment',
            'description' => 'Add the Customer to a Segment',
        ];
        $actions[] = [
            'id'          => 'removefromsegment',
            'name'        => 'Remove from Segment',
            'description' => 'Remove the Customer from a Segment',
        ];
        $actions[] = [
            'id'          => 'addtag',
            'name'        => 'Tag Subscriber',
            'description' => 'Assigns the specified tag(s) to the subscriber',
        ];
        $actions[] = [
            'id'          => 'removetag',
            'name'        => 'Remove Tag',
            'description' => 'Removes the specified tag(s) from the subscriber',
        ];

        return $actions;
    }

    /**
     * @return array
     */
    public function toOptionValues()
    {

        $options = $this->toOptionArray();
        $return = [];

        unset($options['new_search']);
        unset($options['order_product']);
        unset($options['campaign_link']);
        unset($options['product_cycle']);
        unset($options['customer_birthday']);

        foreach ($options as $value => $label) {
            $return[] = ['label' => $label, 'value' => $value];
        }

        return $return;
    }

    /**
     * @return array
     */
    public function toOptionValuesAll()
    {

        $options = $this->toOptionArray();
        $return = [];

        foreach ($options as $value => $label) {
            $return[] = ['label' => $label, 'value' => $value];
        }

        return $return;
    }

    /**
     * @return mixed
     */
    public function getConditionsInstance()
    {

        return $this->conditionsCombine->create();
    }

    /**
     * @return mixed
     */
    public function getActionsInstance()
    {

        return $this->collectionCombine->create();
    }

    /**
     *
     * @return bool|\Magento\Rule\Model\AbstractModel
     */
    public function beforeSave()
    {

        if (!$this->getData('controller_panda')) {
            return false;
        }

        return parent::beforeSave();
    }

    /**
     * @return \Magento\Rule\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateBeforeSave()
    {

        $date = $this->helperData->gmtDate('Y-m-d');

        if ($this->getFromDate() && $this->getToDate()) {
            try {
                $inputFilter = new \Zend_Filter_Input(
                    ['to_date' => $this->dateFilter, 'from_date' => $this->dateFilter],
                    [],
                    $this->getData()
                );
                $data = $inputFilter->getUnescaped();
                $this->addData($data);
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid date format'));
            }

            try {
                $this->timezone->formatDate($this->getFromDate());
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid date in From Date'));
            }

            try {
                $this->timezone->formatDate($this->getToDate());
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid date in To Date'));
            }

            if ($this->getFromDate() > $this->getToDate()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The end date cannot be earlier than start date')
                );
            }

            if ($this->getToDate() < $date) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The end date cannot be earlier than today')
                );
            }
        }

        return parent::validateBeforeSave();
    }

    /**
     *
     */
    public function detectAbandoned()
    {

        $this->getResource()
             ->clearAbandonedEvents(
                 $this,
                 $this->helperData->gmtDate(),
                 $this->subscribersFactory
             );
        $abandonedDate = new \DateTime($this->helperData->gmtDate());
        $abandonedDate->sub(new \DateInterval('PT' . $this->getAbandonedCartMinutes() . 'M'));

        $real = $this->helperData->gmtDate();
        //New
        $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'new_abandoned');

        /** @var self $autoresponder */
        foreach ($autoresponders as $autoresponder) {
            /** @var Chains $chain */
            $chain = $this->chainsCollection->create()
                                            ->addFieldToFilter('autoresponder_id', $autoresponder->getId())
                                            ->addFieldToFilter('parent_id', 0)
                                            ->getFirstItem();

            $abandonedDate->sub(new \DateInterval('PT30M'));
            $realOld = $abandonedDate->format('Y-m-d H:i:s');

            $cQuote = $this->quoteCollection->create();
            $cQuote->addFieldToFilter('items_count', ['neq' => '0'])
                   ->addFieldToFilter('main_table.is_active', '1')
                   ->addFieldToFilter('main_table.subtotal', ['gt' => 0]);

            $cQuote->addFieldToSelect('entity_id');

            $cQuote->addFieldToFilter('main_table.updated_at', ['lt' => $real]);
            $cQuote->addFieldToFilter('main_table.updated_at', ['gt' => $realOld]);

            $resource = $this->getResource();
            $cQuote->getSelect()
                   ->joinLeft(
                       ['a' => $resource->getTable('quote_address')],
                       'main_table.entity_id=a.quote_id AND a.address_type="billing"',
                       [
                           'email' => new \Zend_Db_Expr(
                               'IF(CHAR_LENGTH(main_table.customer_email) > 6,main_table.customer_email, a.email)'
                           ),
                       ]
                   )
                   ->where('CHAR_LENGTH(main_table.customer_email) > 6 OR CHAR_LENGTH(a.email) > 6')
                   ->group('main_table.entity_id');

            foreach ($cQuote as $cart) {
                /** @var Subscribers $subscriber */
                $subscriber = $this->loadSubscriber($autoresponder, $cart->getEmail());

                if (!$subscriber) {
                    continue;
                }

                $events = $this->eventsCollection->create()
                                                 ->addFieldToFilter('subscriber_id', $subscriber->getId())
                                                 ->addFieldToFilter('autoresponder_id', $autoresponder->getId())
                                                 ->addFieldToFilter('data_object_id', $cart->getId())
                    #->addFieldToFilter('executed', 0)
                                                 ->addFieldToFilter('chain_id', $chain->getId());

                if ($events->getSize() == 0) {
                    $autoresponder->setDataObjectId($cart->getId());
                    $this->_insertData($autoresponder, $subscriber, $chain);
                }
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function detectBirthdays()
    {

        $year = $this->_localeDate->date()->format('Y');
        $resource = $this->getResource();
        $connection = $resource->getConnection();

        $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'customer_birthday');

        /** @var Autoresponders $autoresponder */
        foreach ($autoresponders as $autoresponder) {
            $collection = $this->customerCollection->create();

            $collection->getSelect()
                       ->where(
                           'DATE_FORMAT(dob, "%m-%d")=?',
                           date('m-d', time() + $autoresponder->getDaysBefore() * 86400)
                       );

            /** @var \Magento\Customer\Model\Customer $customer */
            foreach ($collection as $customer) {
                $subscriber = $this->loadSubscriber($autoresponder, $customer->getEmail());

                if (!$subscriber) {
                    continue;
                }

                $alreadySent = $connection->fetchOne(
                    $connection->select()
                               ->from($resource->getTable('panda_birthdays_logs'), ['email'])
                               ->where('email=?', $subscriber->getEmail())
                               ->where('year=?', $year)
                );

                if ($alreadySent) {
                    continue;
                }

                $connection->insert(
                    $resource->getTable('panda_birthdays_logs'),
                    [
                        'email' => $subscriber->getEmail(),
                        'year'  => $year,
                    ]
                );

                $this->_insertData($autoresponder, $subscriber);
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function detectActivity()
    {

        $resource = $this->getResource();
        $read = $resource->getConnection();
        $abandonedDate = new \DateTime($this->helperData->gmtDate());
        $abandonedDate->sub(new \DateInterval('PT' . $this->getAbandonedCartMinutes() . 'M'));
        $real = $abandonedDate->format('Y-m-d');

        $autorespondersCancel = $read->select()
                                     ->from(
                                         $resource->getTable('panda_autoresponders_cancellation_events'),
                                         ['autoresponder_id']
                                     )
                                     ->where('event=?', 'last_activity')
                                     ->distinct();
        $cancelAR = $read->fetchCol($autorespondersCancel);

        //Clear events by activity
        $select = $read->select()
                       ->from($resource->getTable('panda_customers_kpis'), ['customer_id'])
                       ->where("last_activity_date > ? ", $real);

        $activityCollection = $read->fetchCol($select);
        $table = $resource->getTable('panda_autoresponders_events');

        $resource->getConnection()->delete(
            $table,
            [
                'autoresponder_id IN(?)' => $cancelAR,
                'customer_id IN (?)'     => $activityCollection,
                'executed=?'             => '0',
            ]
        );
    }

    /**
     * @throws \Exception
     */
    public function detectActivityDaily()
    {

        $resource = $this->getResource();
        $read = $resource->getConnection();

        //Schedule new events
        $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'last_activity');

        /** @var self $autoresponder */
        foreach ($autoresponders as $autoresponder) {

            /** @var Chains $chain */
            $chain = $this->chainsCollection->create()
                                            ->addFieldToFilter('autoresponder_id', $autoresponder->getId())
                                            ->addFieldToFilter('parent_id', 0)
                                            ->getFirstItem();

            $abandonedDate = new \DateTime($this->helperData->gmtDate());
            $abandonedDate->sub(new \DateInterval('P' . (int) $chain->getData('days') . 'D'));
            $real = $abandonedDate->format('Y-m-d');
            $realOld = $abandonedDate->format('Y-m-d');

            $select = $read->select()
                           ->from($resource->getTable('panda_customers_kpis'), ['customer_id'])
                           ->where("last_activity_date BETWEEN '$realOld' AND '$real'");

            $activityCollection = $read->fetchCol($select);

            foreach ($activityCollection as $customerId) {
                /** @var Subscribers $subscriber */
                $subscriber = $this->loadSubscriber($autoresponder, $customerId);

                if (!$subscriber) {
                    continue;
                }

                $events = $this->eventsCollection->create()
                                                 ->addFieldToFilter('subscriber_id', $subscriber->getId())
                                                 ->addFieldToFilter('autoresponder_id', $autoresponder->getId())
                                                 ->addFieldToFilter('data_object_id', $customerId)
                                                 ->addFieldToFilter('chain_id', $chain->getId());

                if ($events->getSize() == 0) {
                    $autoresponder->setDataObjectId($customerId);
                    $this->_insertData($autoresponder, $subscriber, $chain);
                }
            }
        }
    }

    /**
     *
     * @param $event
     * @param $customerId
     *
     * @return bool
     */
    public function cancelEvents($event, $customerId = null)
    {

        /** @var Subscribers $subscriber */
        $subscriber = $this->loadSubscriberFromSession($customerId);

        if (!$subscriber) {
            return false;
        }

        $subscriberId = $subscriber->getId();

        $resource = $this->getResource();
        $read = $resource->getConnection();

        $autorespondersCancel = $read->select()
                                     ->from(
                                         $resource->getTable('panda_autoresponders_cancellation_events'),
                                         ['autoresponder_id']
                                     )
                                     ->where('event=?', $event)
                                     ->distinct();
        $cancelAR = $read->fetchCol($autorespondersCancel);

        $table = $resource->getTable('panda_autoresponders_events');

        $events = $this->eventsCollection->create()
                                         ->addFieldToFilter('autoresponder_id', ['in' => $cancelAR])
                                         ->addFieldToFilter('subscriber_id', $subscriberId);

        $campaigns = $this->campaignsCollection->create()
                                               ->addFieldToFilter('status', ['neq' => 'finished'])
                                               ->addFieldToFilter(
                                                   'autoresponder_event_id',
                                                   ['in' => $events->getAllIds('event_id')]
                                               );

        /** @var Campaigns $campaign */
        foreach ($campaigns as $campaign) {
            $campaign->setStatus('finished')
                     ->save();
        }

        $this->getResource()
             ->getConnection()
             ->delete(
                 $table,
                 [
                     'autoresponder_id IN(?)' => $cancelAR,
                     'subscriber_id =?'       => $subscriberId,
                     'executed=?'             => '0',
                 ]
             );

        return true;
    }

    /**
     * @param            $event
     * @param bool|false $orderForced
     *
     * @return bool|Autoresponders
     * @throws \Exception
     */
    public function changeStatus($event, $orderForced = false)
    {

        if ($orderForced) {
            $order = $orderForced;
        } else {
            /** @var  \Magento\Sales\Model\Order $order */
            $order = $event->getEvent()->getOrder();
        }
        $newStatus = $order->getData('status');
        $olderStatus = $order->getOrigData('status');

        if ($newStatus == $olderStatus && !$orderForced) {
            return false;
        }

        $email = $order->getCustomerEmail();

        $this->cancelEvents('order_status_' . $newStatus, $order->getCustomerId());

        $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'order_status_' . $newStatus);

        if ($autoresponders->count() == 0) {
            return false;
        }

        /** @var Autoresponders $autoresponder */
        foreach ($autoresponders as $autoresponder) {
            if ($autoresponder->getShippingMethod()) {
                $allow = explode(',', $autoresponder->getShippingMethod());
                $shippingMethod = $order->getData('shipping_method');
                if (!in_array($shippingMethod, $allow)) {
                    continue;
                }
            }
            if ($autoresponder->getPaymentMethod()) {
                $allow = explode(',', $autoresponder->getPaymentMethod());
                $paymentMethod = $order->getPayment()->getMethod();

                if (!in_array($paymentMethod, $allow)) {
                    continue;
                }
            }

            if ($autoresponder->getOrderStatusPrevious() &&
                $autoresponder->getOrderStatusPrevious() != $olderStatus
            ) {
                continue;
            }

            if ($autoresponder->getOrderStatusPrevious() &&
                $autoresponder->getOrderStatusTime() &&
                $autoresponder->getOrderStatusPrevious() == $olderStatus
            ) {
                $history = $order->getAllStatusHistory();
                $totalHistory = count($history);

                if ($totalHistory < 2) {
                    continue;
                }

                $dateTime = new \DateTime();
                foreach ($history as $item) {
                    if ($item->getData('status') == $olderStatus) {
                        $dateTime = $item->getData('created_at');
                        break;
                    }
                }

                $start_date = new \DateTime($history[0]->getData('created_at'));
                $since_start = $start_date->diff(new \DateTime($dateTime));

                $minutes = $since_start->days * 24 * 60;
                $minutes += $since_start->h * 60;
                $minutes += $since_start->i;

                if ($minutes > $autoresponder->getOrderStatusTime()) {
                    continue;
                }
            }
            /** @var Subscribers $subscriber */
            $subscriber = $this->loadSubscriber($autoresponder, $email);

            if (!$subscriber) {
                continue;
            }

            $autoresponder->setDataObjectId($order->getId());
            $this->_insertData($autoresponder, $subscriber);
        }

        return $this;
    }

    /**
     * @param $event
     *
     * @return $this|bool
     * @throws \Exception
     */
    public function customerChangeGroup(\Magento\Framework\Event\Observer $event)
    {

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $event->getEvent()->getCustomer();

        $newGroupId = $customer->getGroupId();
        $oldGroupId = $this->customerFactory->create()->load($customer->getId())->getGroupId();

        if ($newGroupId && $oldGroupId && $oldGroupId != $newGroupId) {
            $autoresponders = $this->getActiveCollection()
                                   ->addFieldToFilter('event', 'customer_group')
                                   ->addFieldToFilter('old_customer_group_id', ['finset' => $oldGroupId])
                                   ->addFieldToFilter('new_customer_group_id', ['finset' => $newGroupId]);

            if ($autoresponders->count() == 0) {
                return false;
            }

            foreach ($autoresponders as $autoresponder) {

                /** @var Subscribers $subscriber */
                $subscriber = $this->loadSubscriber($autoresponder, $customer->getEmail());

                if (!$subscriber) {
                    continue;
                }

                $this->_insertData($autoresponder, $subscriber);
            }
        }

        return $this;
    }

    /**
     * @param $event
     *
     * @return $this|bool
     * @throws \Exception
     */
    public function customerChangeAttribute(\Magento\Framework\Event\Observer $event)
    {

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $event->getEvent()->getCustomer();

        /** @var \Magento\Customer\Model\Customer $oldCustomer */
        $oldCustomer = $this->customerFactory->create()->load($customer->getId());

        $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'customer_attribute');

        if ($autoresponders->count() == 0) {
            return false;
        }

        /** @var Autoresponders $autoresponder */
        foreach ($autoresponders as $autoresponder) {
            $newValue = $customer->getData($autoresponder->getCustomerAttribute(), null);
            $oldValue = $oldCustomer->getData($autoresponder->getCustomerAttribute(), null);

            $condition = $autoresponder->getCustomerAttributeFire();

            if (($condition == 'new_not_empty' && $newValue) ||
                ($condition == 'new_not_empty_old_not_empty' && $newValue && $oldValue) ||
                ($condition == 'new_not_empty_old_empty' && $newValue && !$oldValue) ||
                ($condition == 'new_empty' && !$newValue) ||
                ($condition == 'new_empty_old_not_empty' && $newValue && $oldValue) ||
                ($condition == 'new_empty_old_empty' && !$newValue && $oldValue)
            ) {

                /** @var Subscribers $subscriber */
                $subscriber = $this->loadSubscriber($autoresponder, $customer->getEmail());

                if (!$subscriber) {
                    continue;
                }

                $this->_insertData($autoresponder, $subscriber);
            }
        }

        return $this;
    }

    /**
     *
     * @param $event
     *
     * @return bool|Autoresponders
     * @throws \Exception
     */
    public function newLogin(\Magento\Framework\Event\Observer $event)
    {

        //No login event at registration
        if ($this->_registry->registry('panda_new_customer')) {
            return false;
        }

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $event->getCustomer();
        $email = $customer->getEmail();

        $this->cancelEvents('new_login', $customer->getId());

        $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'new_login');

        if ($autoresponders->count() == 0) {
            return false;
        }

        foreach ($autoresponders as $autoresponder) {
            /** @var Subscribers $subscriber */
            $subscriber = $this->loadSubscriber($autoresponder, $email);

            if (!$subscriber) {
                continue;
            }

            $this->_insertData($autoresponder, $subscriber);
        }

        return $this;
    }

    /**
     * @param \Licentia\Forms\Model\FormEntries $entry
     * @param                                   $email
     *
     * @return $this|bool
     */
    public function newFormEntry(\Licentia\Forms\Model\FormEntries $entry, $email)
    {

        $this->cancelEvents('new_form_entry', $email);

        $autoresponders = $this->getActiveCollection()
                               ->addFieldToFilter('form_id', $entry->getFormId())
                               ->addFieldToFilter('event', 'new_form_entry');

        if ($autoresponders->count() == 0) {
            return false;
        }

        /** @var self $autoresponder */
        foreach ($autoresponders as $autoresponder) {
            /** @var Subscribers $subscriber */
            $subscriber = $this->loadSubscriber($autoresponder, $email);

            if (!$subscriber) {
                continue;
            }

            $autoresponder->setDataObjectId($entry->getId());
            $this->_insertData($autoresponder, $subscriber);
        }

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function newSearch()
    {

        $this->cancelEvents('new_search');

        $query = $this->searchHelper->getEscapedQueryText();
        $email = $this->helperData->getCustomerEmail();

        $session = $this->customerSession;
        if ($session->getPandaSearch() && $session->getPandaSearch() == $query) {
            return $this;
        } else {
            $session->setPandaSearch($query);
        }

        $subscriber = $this->subscribersFactory->create()
                                               ->loadSubscriber(
                                                   $email,
                                                   $this->storeManager->getStore()
                                                                      ->getId()
                                               );

        if (!$subscriber->getId()) {
            return $this;
        }

        $this->metadataFactory->create()->searchRelated($email, $this->helperData->getCustomerId(), $query);

        $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'new_search');

        if ($autoresponders->count() == 0) {
            return $this;
        }
        /** @var Autoresponders $autoresponder */
        foreach ($autoresponders as $autoresponder) {
            /** @var Subscribers $subscriber */
            $subscriber = $this->loadSubscriber($autoresponder, $email);

            if (!$subscriber) {
                continue;
            }

            $search = str_getcsv($autoresponder->getSearch());

            foreach ($search as $string) {
                if ($autoresponder->getSearchOption() == 'eq' && strtolower($query) == strtolower($string)) {
                    $this->_insertData($autoresponder, $subscriber);
                }

                if ($autoresponder->getSearchOption() == 'like' && stripos($query, $string) !== false) {
                    $this->_insertData($autoresponder, $subscriber);
                }
            }
        }

        return $this;
    }

    /**
     *
     * @param $event
     *
     * @return bool|Autoresponders
     * @throws \Exception
     */
    public function newCustomer(\Magento\Framework\Event\Observer $event)
    {

        $this->_registry->register('panda_new_customer', true, true);

        $this->cancelEvents('new_account');

        $customer = $event->getEvent()->getCustomer();
        $email = $customer->getEmail();

        $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'new_account');

        if ($autoresponders->count() == 0) {
            return false;
        }
        /** @var Autoresponders $autoresponder */
        foreach ($autoresponders as $autoresponder) {
            /** @var Subscribers $subscriber */
            $subscriber = $this->loadSubscriber($autoresponder, $email);

            if (!$subscriber) {
                continue;
            }
            $this->_insertData($autoresponder, $subscriber);
        }

        return $this;
    }

    /**
     *
     * @param $event
     *
     * @return bool|Autoresponders
     * @throws \Exception
     */
    public function newReviewSelf(\Magento\Framework\Event\Observer $event)
    {

        $review = $event->getObject();
        $productId = $review->getEntityPkValue();
        $customerId = $review->getCustomerId();

        if (!$customerId) {
            return false;
        }

        $orders = $this->orderCollection->create()->addFieldToFilter('customer_id', $customerId);

        $return = true;
        foreach ($orders as $order) {
            $items = $order->getAllItems();
            foreach ($items as $item) {
                if ($item->getProductId() == $productId) {
                    $return = false;
                    break 2;
                }
            }
        }

        if ($return) {
            return false;
        }

        $customer = $this->customerFactory->create()->load($customerId);
        $email = $customer->getEmail();

        $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'new_review_self');

        if ($autoresponders->count() == 0) {
            return false;
        }
        /** @var Autoresponders $autoresponder */
        foreach ($autoresponders as $autoresponder) {
            /** @var Subscribers $subscriber */
            $subscriber = $this->loadSubscriber($autoresponder, $email);

            if (!$subscriber) {
                continue;
            }

            $autoresponder->setDataObjectId($review->getId());
            $this->_insertData($autoresponder, $subscriber);
        }

        return $this;
    }

    /**
     *
     * @param $event
     *
     * @return bool|Autoresponders
     * @throws \Exception
     */
    public function newReview(\Magento\Framework\Event\Observer $event)
    {

        $this->cancelEvents('new_review');

        $customer = $this->helperData->getCustomer();
        $review = $event->getObject();
        if (!$customer && !$review->getCustomerId()) {
            return false;
        }

        $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'new_review');

        if ($autoresponders->count() == 0) {
            return false;
        }

        /** @var Autoresponders $autoresponder */
        foreach ($autoresponders as $autoresponder) {
            /** @var Subscribers $subscriber */
            $subscriber = $this->loadSubscriber($autoresponder, $review->getCustomerId());

            if (!$subscriber) {
                continue;
            }

            $autoresponder->setDataObjectId($review->getId());
            $this->_insertData($autoresponder, $subscriber);
        }

        return $this;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     *
     * @return $this|bool
     * @throws \Exception
     */
    public function newReviewApproved(\Magento\Framework\Event\Observer $event)
    {

        /** @var \Magento\Review\Model\Review $review */
        $review = $event->getEvent()->getReview();
        $review = $event->getObject();

        $customer = $this->helperData->getCustomer();
        if (!$customer && !$review->getCustomerId()) {
            return false;
        }

        if ($review->getStatusId() == \Magento\Review\Model\Review::STATUS_APPROVED &&
            $review->getOrigData('status_id') != \Magento\Review\Model\Review::STATUS_APPROVED
        ) {
            $this->cancelEvents('new_review_approved', $review->getCustomerId());

            $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'new_review_approved');

            if ($autoresponders->count() == 0) {
                return false;
            }

            /** @var Autoresponders $autoresponder */
            foreach ($autoresponders as $autoresponder) {
                /** @var Subscribers $subscriber */
                $subscriber = $this->loadSubscriber($autoresponder, $review->getCustomerId());

                if (!$subscriber) {
                    continue;
                }

                $autoresponder->setDataObjectId($review->getId());
                $this->_insertData($autoresponder, $subscriber);
            }
        }

        return $this;
    }

    /**
     *
     * @param $event
     *
     * @return bool|Autoresponders
     * @throws \Exception
     * @internal param $order
     *
     * @internal param $event
     */
    public function newOrder(\Magento\Framework\Event\Observer $event)
    {

        /** @var  \Magento\Sales\Model\Order $order */
        $order = $event->getEvent()->getOrder();
        $email = $order->getCustomerEmail();

        $this->cancelEvents('order_new', $order->getCustomerId());

        $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'order_new');

        if ($autoresponders->count() == 0) {
            return false;
        }

        /** @var Autoresponders $autoresponder */
        foreach ($autoresponders as $autoresponder) {
            if ($autoresponder->getShippingMethod()) {
                $allow = explode(',', $autoresponder->getShippingMethod());
                $shippingMethod = $order->getData('shipping_method');
                if (!in_array($shippingMethod, $allow)) {
                    continue;
                }
            }

            if ($autoresponder->getPaymentMethod()) {
                $allow = explode(',', $autoresponder->getPaymentMethod());
                $paymentMethod = $order->getPayment()->getMethod();

                if (!in_array($paymentMethod, $allow)) {
                    continue;
                }
            }

            /** @var Subscribers $subscriber */
            $subscriber = $this->loadSubscriber($autoresponder, $email);

            if (!$subscriber) {
                continue;
            }

            $autoresponder->setDataObjectId($order->getId());
            $this->_insertData($autoresponder, $subscriber);
        }

        return $this;
    }

    /**
     * @param $event
     *
     * @return $this|bool
     */
    public function boughtProduct(\Magento\Framework\Event\Observer $event)
    {

        /** @var  \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $event->getEvent()->getInvoice();

        /** @var  \Magento\Sales\Model\Order $order */
        $order = $event->getEvent()->getOrder();

        $email = $order->getCustomerEmail();

        $this->cancelEvents('order_new', $order->getCustomerId());

        $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'order_product');

        if ($autoresponders->count() == 0) {
            return false;
        }

        /** @var Autoresponders $autoresponder */
        foreach ($autoresponders as $autoresponder) {
            $items = $invoice->getAllItems();

            $ok = false;
            /** @var \Magento\Sales\Model\Order\Invoice\Item $item */
            foreach ($items as $item) {
                $products = explode("\n", $autoresponder->getProduct());
                $products = array_map('trim', $products);

                if (in_array(
                    $item->getOrderItem()
                         ->getProduct()
                         ->getSku(),
                    $products
                )) {
                    $ok = $item->getProductId();
                    break;
                }
            }

            if ($ok === false) {
                continue;
            }

            if ($autoresponder->getShippingMethod()) {
                $allow = explode(',', $autoresponder->getShippingMethod());
                $shippingMethod = $invoice->getData('shipping_method');
                if (!in_array($shippingMethod, $allow)) {
                    continue;
                }
            }
            if ($autoresponder->getPaymentMethod()) {
                $allow = explode(',', $autoresponder->getPaymentMethod());
                $paymentMethod = $invoice->getPayment()->getMethod();

                if (!in_array($paymentMethod, $allow)) {
                    continue;
                }
            }
            /** @var Subscribers $subscriber */
            $subscriber = $this->loadSubscriber($autoresponder, $email);

            if (!$subscriber) {
                continue;
            }

            $autoresponder->setDataObjectId($ok);
            $this->_insertData($autoresponder, $subscriber);
        }

        return $this;
    }

    /**
     *
     * @param $event
     *
     * @return bool|Autoresponders
     * @throws \Exception
     */
    public function newShipmentTrack(\Magento\Framework\Event\Observer $event)
    {

        /** @var \Magento\Sales\Model\Order\Shipment\Track $track */
        $track = $event->getEvent()->getTrack();

        /** @var  \Magento\Sales\Model\Order $order */
        $order = $track->getShipment()->getOrder();
        $email = $order->getCustomerEmail();

        $this->cancelEvents('shipment_new_track', $order->getCustomerId());

        $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'shipment_new_track');

        if ($autoresponders->count() == 0) {
            return false;
        }

        /** @var Autoresponders $autoresponder */
        foreach ($autoresponders as $autoresponder) {
            /** @var Subscribers $subscriber */
            $subscriber = $this->loadSubscriber($autoresponder, $email);

            if (!$subscriber) {
                continue;
            }

            $autoresponder->setDataObjectId($track->getId());
            $this->_insertData($autoresponder, $subscriber);
        }

        return $this;
    }

    /**
     *
     * @param $event
     *
     * @return bool|Autoresponders
     * @throws \Exception
     */
    public function newShipmentNoTrack(\Magento\Framework\Event\Observer $event)
    {

        /** @var  \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $event->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $email = $order->getCustomerEmail();

        if (count($shipment->getAllTracks()) > 0) {
            return false;
        }

        $this->cancelEvents('shipment_new_no_track', $order->getCustomerId());

        $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'shipment_new_no_track');

        if ($autoresponders->count() == 0) {
            return false;
        }

        /** @var self $autoresponder */
        foreach ($autoresponders as $autoresponder) {
            /** @var Subscribers $subscriber */
            $subscriber = $this->loadSubscriber($autoresponder, $email);

            if (!$subscriber) {
                continue;
            }

            $autoresponder->setDataObjectId($shipment->getId());
            $this->_insertData($autoresponder, $subscriber);
        }

        return $this;
    }

    /**
     * @param Subscribers $subscriber
     * @param Campaigns   $campaign
     *
     * @return bool|Autoresponders
     * @throws \Exception
     */
    public function newView(Subscribers $subscriber, Campaigns $campaign)
    {

        $this->cancelEvents('campaign_open');

        $autoresponders = $this->getActiveCollection()
                               ->addFieldToFilter('event', 'campaign_open')
                               ->addFieldToFilter('campaign_id', $campaign->getId());

        if ($autoresponders->count() == 0) {
            return false;
        }

        foreach ($autoresponders as $autoresponder) {
            $this->_insertData($autoresponder, $subscriber);
        }

        return $this;
    }

    /**
     * @param Subscribers $subscriber
     * @param Campaigns   $campaign
     *
     * @return bool|Autoresponders
     * @throws \Exception
     */
    public function newClick(Subscribers $subscriber, Campaigns $campaign)
    {

        $this->cancelEvents('campaign_click', $subscriber->getCustomerId());
        $this->cancelEvents('last_activity');

        $autoresponders = $this->getActiveCollection()
                               ->addFieldToFilter('event', ['in' => ['campaign_link', 'campaign_click']])
                               ->addFieldToFilter('campaign_id', $campaign->getId());

        if ($autoresponders->count() == 0) {
            return false;
        }

        /** @var Autoresponders $autoresponder */
        foreach ($autoresponders as $autoresponder) {
            if ($autoresponder->getEvent() == 'campaign_link') {
                $linkOpen = $this->_registry->registry('panda_open_url');

                $links = $this->linksCollection->create()->addFieldToFilter('link_id', $autoresponder->getLinkId());

                if ($links->count() != 1) {
                    break;
                }

                $link = $links->getFirstItem()->getLink();

                if (stripos($linkOpen, $link) === false) {
                    break;
                }
            }

            $this->_insertData($autoresponder, $subscriber);
        }

        return $this;
    }

    /**
     *
     * @param Autoresponders $autoresponder
     * @param                $email
     *
     * @return boolean|Subscribers
     */
    public function loadSubscriber(Autoresponders $autoresponder, $email)
    {

        if (is_numeric($email)) {
            $customer = $this->customerFactory->create()->load($email);
            $email = $customer->getEmail();
        }

        /** @var Subscribers $subscriber */
        $subscriber = $this->subscribersFactory->create()->loadByEmail($email);

        if (!$subscriber->getId()) {
            if (!$this->scopeConfig->isSetFlag('panda_nuntius/info/auto')) {
                return false;
            }

            $this->subscribersFactory->create()->subscribe($email);
            $subscriber = $this->subscribersFactory->create()->loadByEmail($email);

            if (!$subscriber || !$subscriber->getId() || !$subscriber->isSubscribed()) {
                return false;
            }
        }

        if ($subscriber->getId()) {
            if (!$subscriber->isSubscribed() && $autoresponder->getPreviousCustomers() != 1) {
                return false;
            }

            $subscribers = $this->subscribersCollection->create()
                                                       ->addSegments($autoresponder->getSegmentsIds())
                                                       ->addStoreIds($autoresponder->getStoreId())
                                                       ->addFieldToFilter(
                                                           'main_table.subscriber_id',
                                                           $subscriber->getId()
                                                       );
            if ($autoresponder->getPreviousCustomers() == 1) {
                $subscribers->addFieldToFilter('previous_customer', 1);
            }
            if ($subscribers->count() == 0) {
                return false;
            }
        }

        return $subscriber;
    }

    /**
     * @param Autoresponders $autoresponder
     * @param Chains         $chain
     * @param null           $existingDate
     *
     * @return string
     */
    public function calculateSendDate(Autoresponders $autoresponder, Chains $chain, $existingDate = null)
    {

        if ($autoresponder->getCustomDate()) {
            $current = new \DateTime($autoresponder->getCustomDate());
        } elseif ($existingDate) {
            $current = new \DateTime($existingDate);
        } else {
            $current = new \DateTime($this->helperData->gmtDate());
        }

        if ($chain->getExtraData()) {
            $extra = json_decode($chain->getExtraData(), true);

            if ($extra === false || !isset($extra['days'])) {
                return $current->format('Y-m-d H:i:s');
            }

            $calDate = new \DateInterval(
                'P' . (int) $extra['days'] . 'DT' . (int) $extra['hours'] . 'H' . (int) $extra['minutes'] . 'M'
            );

            $current->add($calDate);

            if (isset($extra['skip_days']) && is_array($extra['skip_days'])) {
                $skip = explode(',', $extra['skip_days']);
                $weekDay = $current->format('N');
                if ($weekDay == 7) {
                    $weekDay = 1;
                } else {
                    $weekDay++;
                }
                if (in_array($weekDay, $skip) && count($skip) < 7) {
                    for ($i = 1; $i < 7; $i++) {
                        $nextDay = $weekDay + $i;
                        if ($nextDay > 7) {
                            $nextDay = $nextDay - 7;
                        }
                        if (!in_array($nextDay, $skip)) {
                            break;
                        }
                    }
                    $current->add(new \DateInterval('P' . $i . 'D'));
                }
            }
        }
        $date = $current->format('Y-m-d H:i:s');

        return $date;
    }

    /**
     * @throws \Exception
     */
    public function cron()
    {

        $date = $this->helperData->gmtDate();

        $emails = $this->eventsCollection->create()
                                         ->addFieldToFilter('executed', 0)
                                         ->addFieldToFilter('execute_at', ['lteq' => $date]);

        /** @var Events $event */
        foreach ($emails as $event) {
            /** @var Autoresponders $autoresponder */
            $autoresponder = $this->autorespondersFactory->create()->load($event->getAutoresponderId());

            /** @var Subscribers $subscriber */
            $subscriber = $this->subscribersFactory->create()->load($event->getSubscriberId());

            /** @var Chains $chain */
            $chain = $this->chainsFactory->create()->load($event->getChainId());

            $isAutoresponderEnable = $this->getActiveCollection()
                                          ->addFieldToFilter('autoresponder_id', $autoresponder->getId())
                                          ->getSize();

            if ($isAutoresponderEnable != 1 || !$autoresponder->getId() || !$subscriber->getId() ||
                !$chain->getId() || !$this->loadSubscriber($autoresponder, $subscriber->getEmail())
            ) {
                $event->setExecuted(1)
                      ->save();
                continue;
            }

            $nextChain = false;
            if ($chain->getEvent()) {
                $nextChain = $this->classes[$chain->getEvent()]->create()
                                                               ->run($autoresponder, $subscriber, $event, $chain);
            }

            $event->setExecuted(1)
                  ->setExecutedAt($date)
                  ->save();

            if (!is_int($nextChain)) {
                $next = $this->chainsCollection->create()
                                               ->addFieldToFilter('autoresponder_id', $autoresponder->getId())
                                               ->addFieldToFilter('parent_id', $chain->getId())
                                               ->setOrder('sort_order', 'ASC')
                                               ->setPageSize(1);

                if ($next->count() != 1) {
                    continue;
                }

                $next = $next->getFirstItem();
            } else {
                $next = $this->chainsFactory->create()->load($nextChain);
            }

            if ($next->getId()) {
                $newCron = $event->getData();
                unset($newCron['event_id']);
                $newCron['event'] = $next->getEvent();
                $newCron['execute_at'] = $this->calculateSendDate($autoresponder, $chain, $event->getCreatedAt());
                $newCron['executed_at'] = null;
                $newCron['created_at_grid'] = $date;
                $newCron['executed'] = 0;
                $newCron['chain_id'] = $next->getId();
                $this->eventsFactory->create()
                                    ->setData($newCron)
                                    ->save();
            }
        }
    }

    /**
     * @param Autoresponders $autoresponder
     * @param Subscribers    $subscriber
     * @param null           $chain
     *
     * @return bool|Events
     */
    protected function _insertData(Autoresponders $autoresponder, Subscribers $subscriber, $chain = null)
    {

        $isValid = $autoresponder->validate(
            $this->checkoutSession->getQuote()
                                  ->getShippingAddress()
        );

        if (!$isValid) {
            return false;
        }

        if (!$chain) {

            /** @var Chains $chain */
            $chain = $this->chainsCollection->create()
                                            ->addFieldToFilter('autoresponder_id', $autoresponder->getId())
                                            ->addFieldToFilter('parent_id', 0)
                                            ->getFirstItem();

            if (!$chain->getId()) {
                return false;
            }
        }

        if ($autoresponder->getSendOnce() == 1) {

            /** @var \Licentia\Panda\Model\ResourceModel\Events\Collection $events */
            $events = $this->eventsCollection->create()
                                             ->addFieldToFilter('autoresponder_id', $autoresponder->getId())
                                             ->addFieldToFilter('subscriber_id', $subscriber->getId());

            if ($events->getSize() != 0) {
                return false;
            }
        }

        /** @var \Licentia\Panda\Model\ResourceModel\Events\Collection $exists */
        $exists = $this->eventsCollection->create()
                                         ->addFieldToFilter('autoresponder_id', $autoresponder->getId())
                                         ->addFieldToFilter('executed', 0)
                                         ->addFieldToFilter('chain_id', $chain->getId())
                                         ->addFieldToFilter('subscriber_id', $subscriber->getId());

        $exists->walk('delete');

        $now = $this->helperData->gmtDate();
        if ($autoresponder->getCustomDate()) {
            $createdDate = $autoresponder->getCustomDate();
        } else {
            $createdDate = $now;
        }

        $data = [];
        $data['execute_at'] = $this->calculateSendDate($autoresponder, $chain, $createdDate);
        $data['autoresponder_id'] = $autoresponder->getId();
        $data['customer_id'] = $subscriber->getCustomerId();
        $data['chain_id'] = $chain->getId();
        $data['subscriber_id'] = $subscriber->getId();
        $data['subscriber_firstname'] = $subscriber->getFirstname();
        $data['subscriber_lastname'] = $subscriber->getLastname();
        $data['subscriber_email'] = $subscriber->getEmail();
        $data['event'] = $autoresponder->getEvent();
        $data['created_at'] = $createdDate;
        $data['created_at_grid'] = $this->helperData->gmtDate();
        $data['executed'] = 0;
        $data['data_object_id'] = $autoresponder->getDataObjectId();

        if ($data['execute_at'] < $now) {
            return false;
        }

        return $this->eventsFactory->create()
                                   ->setData($data)
                                   ->save();
    }

    /**
     *
     * @return array
     */
    public function toFormValues()
    {

        $return = [];
        $collection = $this->autorespondersCollection->create()
                                                     ->addFieldToSelect('name')
                                                     ->addFieldToSelect('autoresponder_id')
                                                     ->setOrder('name', 'ASC');

        /** @var self $autoresponder */
        foreach ($collection as $autoresponder) {
            $return[$autoresponder->getId()] =
                $autoresponder->getName() . ' (ID:' . $autoresponder->getId() . ')';
        }

        return $return;
    }

    /**
     *
     * @return \Licentia\Panda\Model\ResourceModel\Autoresponders\Collection
     */
    protected function getActiveCollection()
    {

        $date = $this->helperData->gmtDate();

        $return = $this->autorespondersCollection->create()->addFieldToFilter('main_table.is_active', 1);

        $return->addFieldToFilter(
            'main_table.from_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $date],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ],
            ]
        )->addFieldToFilter(
            'main_table.to_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $date],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ],
            ]
        );

        return $return;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     *
     * @return $this|bool
     * @throws \Exception
     */
    public function internalEvent(\Magento\Framework\Event\Observer $event)
    {

        $eventName = $event->getEventName();
        $subscriber = $this->loadSubscriberFromSession();

        if (!$subscriber) {
            return false;
        }

        $this->cancelEvents('internal_event');

        $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'internal_event');

        if ($autoresponders->count() == 0) {
            return false;
        }

        foreach ($autoresponders as $autoresponder) {
            $events = explode(',', $autoresponders->getEvents());
            $events = array_map('trim', $events);

            if (!in_array($eventName, $events)) {
                continue;
            }

            $this->_insertData($autoresponder, $subscriber);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalUtmCampaigns()
    {

        return $this->getActiveCollection()->addFieldToFilter('event', 'utm_campaign')->count();
    }

    /**
     * @param array $params
     *
     * @return $this|bool
     * @throws \Exception
     */
    public function utmCampaign(array $params)
    {

        if (isset($params['panda_acquisition_campaign']) && strlen($params['panda_acquisition_campaign']) > 0) {
            $this->pandaSession->setPandaAcquisitionCampaign($params['panda_acquisition_campaign']);
        }

        $ok = false;
        $cache = [];
        foreach ($params as $key => $param) {
            if (stripos($key, 'utm_') !== false) {
                $ok = true;
                $cache[$key] = [$param];
            }
        }

        if ($ok && $this->pandaSession->getData('autoresponder_' . sha1(json_encode($cache)))) {
            return false;
        }

        if (!$ok) {
            return false;
        }

        $subscriber = $this->loadSubscriberFromSession();

        if (!$subscriber) {
            return false;
        }

        $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'utm_campaign');

        if ($autoresponders->count() == 0) {
            return false;
        }

        /** @var Autoresponders $autoresponder */
        foreach ($autoresponders as $autoresponder) {
            if ($autoresponder->getUtm()) {
                $utm = json_decode($autoresponder->getUtm(), true);

                $insert = true;
                $cycle = false;
                foreach ($utm['utm_parameter'] as $key => $param) {
                    if (!array_key_exists($key, self::UTM_URL_PARAMS)) {
                        continue;
                    }

                    if (isset($params[$param])) {
                        $cycle = true;

                        $exps = explode(',', $utm['utm_match'][$key]);
                        $exps = array_filter($exps);

                        foreach ($exps as $exp) {
                            if ($utm['utm_condition'][$key] == 'is' &&
                                strtolower($exp) != strtolower($params[$param])
                            ) {
                                $insert = false;
                            } elseif ($utm['utm_condition'][$key] == 'starts' &&
                                      substr($params[$param], 0, strlen($exp)) !== $exp
                            ) {
                                $insert = false;
                            } elseif ($utm['utm_condition'][$key] == 'ends' &&
                                      substr($params[$param], -strlen($exp)) !== $exp
                            ) {
                                $insert = false;
                            } elseif ($utm['utm_condition'][$key] == 'contains' &&
                                      strpos($exp, $params[$param]) === false
                            ) {
                                $insert = false;
                            } elseif ($utm['utm_condition'][$key] == 'doesnotcontain' &&
                                      strpos($exp, $params[$param]) !== 0
                            ) {
                                $insert = false;
                            } elseif ($utm['utm_condition'][$key] == 'wildcard' &&
                                      !preg_match(
                                          '/' . str_replace('*', '(.*)?', $exp) . '/i',
                                          $params[$param]
                                      )
                            ) {
                                $insert = false;
                            }
                        }
                    }
                }

                if ($insert && $cycle) {
                    $this->_insertData($autoresponder, $subscriber);
                    $this->cancelEvents('utm_campaign');
                    $this->pandaSession->setData('autoresponder_' . sha1(json_encode($cache)), true);
                }
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function afterCommitCallback()
    {

        if ($this->getData('controller_panda') &&
            $this->getEvent() == 'internal_event' &&
            $this->getOrigData('observers') != $this->getObservers()
        ) {
            $arrays = explode(',', $this->getObservers());
            $arrays = array_map('trim', $arrays);
            $arrays = array_combine($arrays, $arrays);

            $id = $this->getId();

            $this->domHelper->autoresponderEventSave($arrays, $id);

            if ($this->_cacheState->isEnabled('config')) {
                $this->cacheTypeList->invalidate('config');
            }
        }
        parent::afterCommitCallback();

        return $this;
    }

    /**
     * @return $this
     */
    public function afterDeleteCommit()
    {

        if ($this->getEvent() == 'internal_event') {
            $id = $this->getId();
            $this->domHelper->autoresponderEventDelete($id);
            if ($this->_cacheState->isEnabled('config')) {
                $this->cacheTypeList->invalidate('config');
            }
        }

        parent::afterDeleteCommit();

        return $this;
    }

    /**
     * @return \Magento\Rule\Model\AbstractModel
     */
    public function afterSave()
    {

        if ($this->getData('controller_panda')) {
            if ($this->getId()) {
                $current = $this->chainseditCollection->create()->addFieldToFilter('autoresponder_id', $this->getId());

                $old = $this->chainsCollection->create()->addFieldToFilter('autoresponder_id', $this->getId());

                /** @var Chains $item */
                foreach ($old as $item) {
                    $item->delete();
                }

                foreach ($current as $item) {
                    $data = $item->getData();
                    $this->getResource()
                         ->getConnection()
                         ->insert(
                             $this->getResource()
                                  ->getTable('panda_autoresponders_chains'),
                             $data
                         );
                }
            }

            $cancellations = $this->getData('cancellation');

            $remove = $this->cancellationCollection->create()->addFieldToFilter('autoresponder_id', $this->getId());

            foreach ($remove as $item) {
                $item->delete();
            }

            if (is_array($cancellations)) {
                $i = 1;
                foreach ($cancellations as $cancellation) {
                    $data = [];
                    $data['autoresponder_id'] = $this->getId();
                    $data['event'] = $cancellation;
                    $this->cancellationFactory->create()
                                              ->setData($data)
                                              ->save();
                    $i++;
                }
            }
        }

        return parent::afterSave();
    }

    /**
     * @param $campaign
     * @param $subscriber
     *
     * @return bool
     */
    public function loadCart(Campaigns $campaign, Subscribers $subscriber)
    {

        if (!$campaign->getId() || !$subscriber->getId() || !$campaign->getAutoresponderId()) {
            return false;
        }

        $cart = $this->cart;
        if ($cart->getItemsCount() > 0) {
            return false;
        }

        $quote = $this->helperData->getSubscriberQuote($subscriber->getEmail());

        if (!$quote) {
            return false; //No products
        }

        $cart->setQuote($quote);
        $cart->save();

        return true;
    }

    /**
     * @return \Magento\Rule\Model\AbstractModel
     */
    public function afterDelete()
    {

        $this->tagsFactory->create()->updateTags('autoresponders', $this, []);

        return parent::afterDelete();
    }

    /**
     *
     * @return Autoresponders
     */
    public function afterLoad()
    {

        parent::afterLoad();

        if ($this->getId()) {
            $chains = $this->chainsCollection->create()
                                             ->addFieldToFilter('autoresponder_id', $this->getId())
                                             ->setOrder('sort_order', 'ASC');

            $this->setData('chains', $chains->getData());

            $cancellations = $this->cancellationCollection->create()
                                                          ->addFieldToFilter('autoresponder_id', $this->getId());

            $this->setData('cancellation', $cancellations->getAllIds('event'));

            $tags = $this->tagsFactory->create()->getTagsHash('autoresponders', $this->getId());

            $this->setData('tags', array_flip($tags));
        }

        return $this;
    }

    /**
     *
     * @param $customerId
     *
     * @return mixed|null
     */
    public function loadSubscriberFromSession($customerId = null)
    {

        if ($this->pandaSession->getPandaSubscriber()) {
            return $this->subscribersFactory->create()->load($this->pandaSession->getPandaSubscriber());
        }

        if ($this->_registry->registry('panda_subscriber')) {
            return $this->_registry->registry('panda_subscriber');
        }

        if (!$customerId) {
            $customerId = $this->customerSession->getId();
        }

        $storeId = $this->storeManager->getStore()->getId();
        if ($customerId) {
            $collection = $this->subscribersCollection->create()
                                                      ->addFieldToFilter('customer_id', $customerId)
                                                      ->addFieldToFilter('store_id', $storeId);

            if ($collection->count() == 1) {
                return $collection->getFirstItem();
            }
        }

        return null;
    }

    /**
     * @param $event
     *
     * @return bool
     * @throws \Exception
     */
    public function buildCycles(\Magento\Framework\Event\Observer $event)
    {

        $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'product_cycle');

        if ($autoresponders->count() == 0) {
            return false;
        }

        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $event->getInvoice();

        /** @var Subscribers $subscriber */
        $subscriber = $this->subscribersFactory->create()
                                               ->loadSubscriber(
                                                   $invoice->getOrder()
                                                           ->getCustomerEmail(),
                                                   $invoice->getStoreId()
                                               );

        if (!$subscriber) {
            return false;
        }

        $items = $invoice->getAllItems();
        /** @var \Magento\Sales\Model\Order\Invoice\Item $item */
        foreach ($items as $item) {
            if (!isset($products[$item->getId()])) {
                $product = $this->productRepository->getById($item->getProductId());

                $cancelPendingEvents = $this->eventsCollection->create()
                                                              ->addFieldToFilter('event', 'product_cycle')
                                                              ->addFieldToFilter('subscriber_id', $subscriber->getId())
                                                              ->addFieldToFilter('data_object_id', $product->getId())
                                                              ->addFieldToFilter('executed', 0);

                $cancelPendingEvents->walk('delete');

                $daysInCycle = $subscriber->getDaysCycle($product);

                if ($daysInCycle) {
                    $date = new \DateTime($invoice->getCreatedAt());
                    $date->add(new \DateInterval('P' . $daysInCycle . 'D'));

                    $this->newCycle($product, $subscriber->getEmail(), $date);

                    $products[$subscriber->getId()] = true;
                }
            }
        }

        return true;
    }

    /**
     * @param $product
     * @param $email
     * @param $date
     *
     * @return $this
     */
    public function newCycle(\Magento\Catalog\Model\Product $product, $email, \DateTime $date)
    {

        $autoresponders = $this->getActiveCollection()->addFieldToFilter('event', 'product_cycle');

        /** @var Autoresponders $autoresponder */
        foreach ($autoresponders as $autoresponder) {
            $ok = true;

            if ($autoresponder->getProducts()) {
                $ok = false;
                $products = explode("\n", $autoresponder->getProducts());
                $products = array_map('trim', $products);
                if (in_array($product->getSku(), $products)) {
                    $ok = true;
                }
            }

            if ($autoresponder->getCategories()) {
                $ok = false;
                $categories = explode(',', $autoresponder->getCategories());
                if (array_intersect($product->getCategoryIds(), $categories)) {
                    $ok = true;
                }
            }

            /** @var Subscribers $subscriber */
            $subscriber = $this->loadSubscriber($autoresponder, $email);
            if (!$subscriber) {
                break;
            }

            if ($ok) {
                $date->sub(new \DateInterval('P' . (int) $autoresponder->getDaysBefore() . 'D'));

                $autoresponder->setCustomDate($date->format('Y-m-d H:i:s'));
                $autoresponder->setDataObjectId($product->getId());
                $this->_insertData($autoresponder, $subscriber);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAbandonedCartMinutes()
    {

        $minutes = (int) $this->scopeConfig->getValue(
            'panda_nuntius/info/abandoned',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );

        return $minutes > 0 ? $minutes : 30;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function _convertFlatToRecursive(array $data)
    {

        $arr = [];
        foreach ($data as $key => $value) {
            if (($key === 'conditions' || $key === 'actions') && is_array($value)) {
                foreach ($value as $id => $data) {
                    $path = explode('--', $id);
                    $node = &$arr;
                    for ($i = 0, $l = sizeof($path); $i < $l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = [];
                        }
                        $node = &$node[$key][$path[$i]];
                    }
                    foreach ($data as $k => $v) {
                        $node[$k] = $v;
                    }
                }
            }
        }

        return $arr;
    }

    /**
     * @param $autoresponderId
     *
     * @return $this
     */
    public function setAutoresponderId($autoresponderId)
    {

        return $this->setData('autoresponder_id', $autoresponderId);
    }

    /**
     * @param $senderId
     *
     * @return $this
     */
    public function setSenderId($senderId)
    {

        return $this->setData('sender_id', $senderId);
    }

    /**
     * @param $segmentsIds
     *
     * @return $this
     */
    public function setSegmentsIds($segmentsIds)
    {

        return $this->setData('segments_ids', $segmentsIds);
    }

    /**
     * @param $event
     *
     * @return $this
     */
    public function setEvent($event)
    {

        return $this->setData('event', $event);
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {

        return $this->setData('name', $name);
    }

    /**
     * @param $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {

        return $this->setData('store_id', $storeId);
    }

    /**
     * @param $campaignId
     *
     * @return $this
     */
    public function setCampaignId($campaignId)
    {

        return $this->setData('campaign_id', $campaignId);
    }

    /**
     * @param $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {

        return $this->setData('is_active', $isActive);
    }

    /**
     * @param $linkId
     *
     * @return $this
     */
    public function setLinkId($linkId)
    {

        return $this->setData('link_id', $linkId);
    }

    /**
     * @param $product
     *
     * @return $this
     */
    public function setProduct($product)
    {

        return $this->setData('product', $product);
    }

    /**
     * @param $subject
     *
     * @return $this
     */
    public function setSubject($subject)
    {

        return $this->setData('subject', $subject);
    }

    /**
     * @param $message
     *
     * @return $this
     */
    public function setMessage($message)
    {

        return $this->setData('message', $message);
    }

    /**
     * @param $totalMessages
     *
     * @return $this
     */
    public function setTotalMessages($totalMessages)
    {

        return $this->setData('total_messages', $totalMessages);
    }

    /**
     * @param $sendOnce
     *
     * @return $this
     */
    public function setSendOnce($sendOnce)
    {

        return $this->setData('send_once', $sendOnce);
    }

    /**
     * @param $search
     *
     * @return $this
     */
    public function setSearch($search)
    {

        return $this->setData('search', $search);
    }

    /**
     * @param $searchOption
     *
     * @return $this
     */
    public function setSearchOption($searchOption)
    {

        return $this->setData('search_option', $searchOption);
    }

    /**
     * @param $orderStatus
     *
     * @return $this
     */
    public function setOrderStatus($orderStatus)
    {

        return $this->setData('order_status', $orderStatus);
    }

    /**
     * @param $fromDate
     *
     * @return $this
     */
    public function setFromDate($fromDate)
    {

        return $this->setData('from_date', $fromDate);
    }

    /**
     * @param $toDate
     *
     * @return $this
     */
    public function setToDate($toDate)
    {

        return $this->setData('to_date', $toDate);
    }

    /**
     * @param $cancelIfOrder
     *
     * @return $this
     */
    public function setCancelIfOrder($cancelIfOrder)
    {

        return $this->setData('cancel_if_order', $cancelIfOrder);
    }

    /**
     * @param $skipDays
     *
     * @return $this
     */
    public function setSkipDays($skipDays)
    {

        return $this->setData('skip_days', $skipDays);
    }

    /**
     * @param $conversionsNumber
     *
     * @return $this
     */
    public function setConversionsNumber($conversionsNumber)
    {

        return $this->setData('conversions_number', $conversionsNumber);
    }

    /**
     * @param $conversionsAmount
     *
     * @return $this
     */
    public function setConversionsAmount($conversionsAmount)
    {

        return $this->setData('conversions_amount', $conversionsAmount);
    }

    /**
     * @param $conversionsAverage
     *
     * @return $this
     */
    public function setConversionsAverage($conversionsAverage)
    {

        return $this->setData('conversions_average', $conversionsAverage);
    }

    /**
     * @param $products
     *
     * @return $this
     */
    public function setProducts($products)
    {

        return $this->setData('products', $products);
    }

    /**
     * @param $categories
     *
     * @return $this
     */
    public function setCategories($categories)
    {

        return $this->setData('categories', $categories);
    }

    /**
     * @param $previousCustomers
     *
     * @return $this
     */
    public function setPreviousCustomers($previousCustomers)
    {

        return $this->setData('previous_customers', $previousCustomers);
    }

    /**
     * @param $autologin
     *
     * @return $this
     */
    public function setAutologin($autologin)
    {

        return $this->setData('autologin', $autologin);
    }

    /**
     * @param $orderStatusPrevious
     *
     * @return $this
     */
    public function setOrderStatusPrevious($orderStatusPrevious)
    {

        return $this->setData('order_status_previous', $orderStatusPrevious);
    }

    /**
     * @param $orderStatusTime
     *
     * @return $this
     */
    public function setOrderStatusTime($orderStatusTime)
    {

        return $this->setData('order_status_time', $orderStatusTime);
    }

    /**
     * @param $clicks
     *
     * @return $this
     */
    public function setClicks($clicks)
    {

        return $this->setData('clicks', $clicks);
    }

    /**
     * @param $uniqueClicks
     *
     * @return $this
     */
    public function setUniqueClicks($uniqueClicks)
    {

        return $this->setData('unique_clicks', $uniqueClicks);
    }

    /**
     * @param $views
     *
     * @return $this
     */
    public function setViews($views)
    {

        return $this->setData('views', $views);
    }

    /**
     * @param $uniqueViews
     *
     * @return $this
     */
    public function setUniqueViews($uniqueViews)
    {

        return $this->setData('unique_views', $uniqueViews);
    }

    /**
     * @param $track
     *
     * @return $this
     */
    public function setTrack($track)
    {

        return $this->setData('track', $track);
    }

    /**
     * @param $paymentMethod
     *
     * @return $this
     */
    public function setPaymentMethod($paymentMethod)
    {

        return $this->setData('payment_method', $paymentMethod);
    }

    /**
     * @param $shippingMethod
     *
     * @return $this
     */
    public function setShippingMethod($shippingMethod)
    {

        return $this->setData('shipping_method', $shippingMethod);
    }

    /**
     * @param $daysBefore
     *
     * @return $this
     */
    public function setDaysBefore($daysBefore)
    {

        return $this->setData('days_before', $daysBefore);
    }

    /**
     * @param $observers
     *
     * @return $this
     */
    public function setObservers($observers)
    {

        return $this->setData('observers', $observers);
    }

    /**
     * @param $globalTemplateId
     *
     * @return $this
     */
    public function setGlobalTemplateId($globalTemplateId)
    {

        return $this->setData('global_template_id', $globalTemplateId);
    }

    /**
     * @param $conditionsSerialized
     *
     * @return $this
     */
    public function setConditionsSerialized($conditionsSerialized)
    {

        return $this->setData('conditions_serialized', $conditionsSerialized);
    }

    /**
     * @param $formId
     *
     * @return $this
     */
    public function setFormId($formId)
    {

        return $this->setData('form_id', $formId);
    }

    /**
     * @param $utm
     *
     * @return $this
     */
    public function setUtm($utm)
    {

        return $this->setData('utm', $utm);
    }

    /**
     * @param $oldCustomerGroupId
     *
     * @return $this
     */
    public function setOldCustomerGroupId($oldCustomerGroupId)
    {

        return $this->setData('old_customer_group_id', $oldCustomerGroupId);
    }

    /**
     * @param $newCustomerGroupId
     *
     * @return $this
     */
    public function setNewCustomerGroupId($newCustomerGroupId)
    {

        return $this->setData('new_customer_group_id', $newCustomerGroupId);
    }

    /**
     * @param $customerAttributeFire
     *
     * @return $this
     */
    public function setCustomerAttributeFire($customerAttributeFire)
    {

        return $this->setData('customer_attribute_fire', $customerAttributeFire);
    }

    /**
     * @param $customerAttribute
     *
     * @return $this
     */
    public function setCustomerAttribute($customerAttribute)
    {

        return $this->setData('customer_attribute', $customerAttribute);
    }

    /**
     * @return mixed
     */
    public function getAutoresponderId()
    {

        return $this->getData('autoresponder_id');
    }

    /**
     * @return mixed
     */
    public function getSenderId()
    {

        return $this->getData('sender_id');
    }

    /**
     * @return mixed
     */
    public function getSegmentsIds()
    {

        return $this->getData('segments_ids');
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {

        return $this->getData('event');
    }

    /**
     * @return mixed
     */
    public function getName()
    {

        return $this->getData('name');
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {

        return $this->getData('store_id');
    }

    /**
     * @return mixed
     */
    public function getCampaignId()
    {

        return $this->getData('campaign_id');
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {

        return $this->getData('is_active');
    }

    /**
     * @return mixed
     */
    public function getLinkId()
    {

        return $this->getData('link_id');
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {

        return $this->getData('product');
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {

        return $this->getData('subject');
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {

        return $this->getData('message');
    }

    /**
     * @return mixed
     */
    public function getTotalMessages()
    {

        return $this->getData('total_messages');
    }

    /**
     * @return mixed
     */
    public function getSendOnce()
    {

        return $this->getData('send_once');
    }

    /**
     * @return mixed
     */
    public function getSearch()
    {

        return $this->getData('search');
    }

    /**
     * @return mixed
     */
    public function getSearchOption()
    {

        return $this->getData('search_option');
    }

    /**
     * @return mixed
     */
    public function getOrderStatus()
    {

        return $this->getData('order_status');
    }

    /**
     * @return mixed
     */
    public function getFromDate()
    {

        return $this->getData('from_date');
    }

    /**
     * @return mixed
     */
    public function getToDate()
    {

        return $this->getData('to_date');
    }

    /**
     * @return mixed
     */
    public function getCancelIfOrder()
    {

        return $this->getData('cancel_if_order');
    }

    /**
     * @return mixed
     */
    public function getSkipDays()
    {

        return $this->getData('skip_days');
    }

    /**
     * @return mixed
     */
    public function getConversionsNumber()
    {

        return $this->getData('conversions_number');
    }

    /**
     * @return mixed
     */
    public function getConversionsAmount()
    {

        return $this->getData('conversions_amount');
    }

    /**
     * @return mixed
     */
    public function getConversionsAverage()
    {

        return $this->getData('conversions_average');
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {

        return $this->getData('products');
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {

        return $this->getData('categories');
    }

    /**
     * @return mixed
     */
    public function getPreviousCustomers()
    {

        return $this->getData('previous_customers');
    }

    /**
     * @return mixed
     */
    public function getAutologin()
    {

        return $this->getData('autologin');
    }

    /**
     * @return mixed
     */
    public function getOrderStatusPrevious()
    {

        return $this->getData('order_status_previous');
    }

    /**
     * @return mixed
     */
    public function getOrderStatusTime()
    {

        return $this->getData('order_status_time');
    }

    /**
     * @return mixed
     */
    public function getClicks()
    {

        return $this->getData('clicks');
    }

    /**
     * @return mixed
     */
    public function getUniqueClicks()
    {

        return $this->getData('unique_clicks');
    }

    /**
     * @return mixed
     */
    public function getViews()
    {

        return $this->getData('views');
    }

    /**
     * @return mixed
     */
    public function getUniqueViews()
    {

        return $this->getData('unique_views');
    }

    /**
     * @return mixed
     */
    public function getTrack()
    {

        return $this->getData('track');
    }

    /**
     * @return mixed
     */
    public function getPaymentMethod()
    {

        return $this->getData('payment_method');
    }

    /**
     * @return mixed
     */
    public function getShippingMethod()
    {

        return $this->getData('shipping_method');
    }

    /**
     * @return mixed
     */
    public function getDaysBefore()
    {

        return $this->getData('days_before');
    }

    /**
     * @return mixed
     */
    public function getObservers()
    {

        return $this->getData('observers');
    }

    /**
     * @return mixed
     */
    public function getGlobalTemplateId()
    {

        return $this->getData('global_template_id');
    }

    /**
     * @return mixed
     */
    public function getConditionsSerialized()
    {

        return $this->getData('conditions_serialized');
    }

    /**
     * @return mixed
     */
    public function getFormId()
    {

        return $this->getData('form_id');
    }

    /**
     * @return mixed
     */
    public function getUtm()
    {

        return $this->getData('utm');
    }

    /**
     * @return mixed
     */
    public function getOldCustomerGroupId()
    {

        return $this->getData('old_customer_group_id');
    }

    /**
     * @return mixed
     */
    public function getNewCustomerGroupId()
    {

        return $this->getData('new_customer_group_id');
    }

    /**
     * @return mixed
     */
    public function getCustomerAttributeFire()
    {

        return $this->getData('customer_attribute_fire');
    }

    /**
     * @return mixed
     */
    public function getCustomerAttribute()
    {

        return $this->getData('customer_attribute');
    }

}
