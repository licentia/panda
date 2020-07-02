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

use Licentia\Equity\Model\Kpis;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class Subscribers
 *
 * @package Licentia\Panda\Model
 */
class Subscribers extends \Magento\Framework\Model\AbstractModel
    implements \Licentia\Panda\Api\Data\SubscribersInterface
{

    const AVAILABLE_IMPORT_FIELDS = [
        'customer_id',
        'store_id',
        'code',
        'firstname',
        'lastname',
        'email',
        'cellphone',
        'created_at',
        'dob',
        'status',
        'bounces',
        'sent',
        'views',
        'clicks',
        'conversions_number',
        'conversions_amount',
        'conversions_average',
        'send_time',
        'previous_customer',
        'gender',
        'last_message_sent_at',
        'last_message_open_at',
        'last_conversion_at',
        'last_message_click_at',
        'last_open_campaign_id',
        'last_click_campaign_id',
        'last_conversion_campaign_id',
        'unsubscribed_at',
    ];

    /**
     *
     */
    const MAX_NUMBER_EXTRA_FIELDS = 25;

    /**
     *
     */
    const STATUS_SUBSCRIBED = 1;

    /**
     *
     */
    const STATUS_NOT_ACTIVE = 2;

    /**
     *
     */
    const STATUS_UNSUBSCRIBED = 3;

    /**
     *
     */
    const STATUS_UNCONFIRMED = 4;

    const AVAILABLE_STATUS = [
        self::STATUS_NOT_ACTIVE   => 'Not Active',
        self::STATUS_SUBSCRIBED   => 'Subscribed',
        self::STATUS_UNCONFIRMED  => 'Unconfirmed',
        self::STATUS_UNSUBSCRIBED => 'Unsubscribed',
    ];

    const GENDER_LIST = [
        1 => 'Male',
        2 => 'Female',
    ];

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_subscriber';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'panda_subscriber';

    /**
     * @var
     */
    protected $subscribersCollection;

    /**
     * @var \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory
     */
    protected $newsletterFactory;

    /**
     * @var ResourceModel\Stats\CollectionFactory
     */
    protected $statsCollection;

    /**
     * @var ResourceModel\Conversions\CollectionFactory
     */
    protected $conversionsFactory;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscriberCoreFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var SubscribersFactory
     */
    protected $subscribersFactory;

    /**
     * @var ResourceModel\Subscribers\CollectionFactory
     */
    protected $subscriberCollection;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollection;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $salesCollection;

    /**
     * @var ExtraFieldsFactory
     */
    protected $extraFieldsFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Kpis
     */
    protected $kpis;

    /**
     * @var TagsFactory
     */
    protected $tagsFactory;

    /**
     * @var TagsRelationsFactory
     */
    protected $tagsRelationsFactory;

    /**
     * @var \Licentia\Equity\Model\MetadataFactory
     */
    protected $metadataFactory;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var
     */
    protected $kpisFactory;

    /**
     * @var array
     */
    protected $extraFieldsSubscriber;

    /**
     * @var \Licentia\Equity\Model\Segments\ListSegmentsFactory
     */
    protected $listSegmentsFactory;

    /**
     * Subscribers constructor.
     *
     * @param \Magento\Framework\UrlInterface                                      $url
     * @param \Licentia\Equity\Model\MetadataFactory                               $metadataFactory
     * @param \Licentia\Equity\Model\KpisFactory                                   $kpisFactory
     * @param TagsFactory                                                          $tagsFactory
     * @param TagsRelationsFactory                                                 $tagsRelationsFactory
     * @param ExtraFieldsFactory                                                   $extraFieldsFactory
     * @param CustomerRepositoryInterface                                          $customerRepository
     * @param \Magento\Framework\Model\Context                                     $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface                   $scope
     * @param \Magento\Framework\Registry                                          $registry
     * @param \Licentia\Panda\Helper\Data                                          $pandaHelper
     * @param \Magento\Store\Model\StoreManagerInterface                           $storeManager
     * @param \Magento\Framework\App\RequestInterface                              $request
     * @param ResourceModel\Subscribers\CollectionFactory                          $subscriberCollection
     * @param ResourceModel\Conversions\CollectionFactory                          $conversionsFactory
     * @param ResourceModel\Stats\CollectionFactory                                $statsFactory
     * @param SubscribersFactory                                                   $subscribersFactory
     * @param \Magento\Newsletter\Model\SubscriberFactory                          $subscriberCore
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory     $customerCollection
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory           $salesFactory
     * @param \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory $newsletterFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null         $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null                   $resourceCollection
     * @param array                                                                $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Licentia\Equity\Model\MetadataFactory $metadataFactory,
        \Licentia\Equity\Model\KpisFactory $kpisFactory,
        TagsFactory $tagsFactory,
        TagsRelationsFactory $tagsRelationsFactory,
        ExtraFieldsFactory $extraFieldsFactory,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scope,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Equity\Model\Segments\ListSegmentsFactory $listSegmentsFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        ResourceModel\Subscribers\CollectionFactory $subscriberCollection,
        ResourceModel\Conversions\CollectionFactory $conversionsFactory,
        ResourceModel\Stats\CollectionFactory $statsFactory,
        SubscribersFactory $subscribersFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberCore,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesFactory,
        \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory $newsletterFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->listSegmentsFactory = $listSegmentsFactory;
        $this->urlBuilder = $url;
        $this->metadataFactory = $metadataFactory;
        $this->tagsFactory = $tagsFactory;
        $this->tagsRelationsFactory = $tagsRelationsFactory;
        $this->kpisFactory = $kpisFactory;
        $this->customerRepository = $customerRepository;
        $this->subscriberCollection = $subscriberCollection;
        $this->customerCollection = $customerCollection;
        $this->salesCollection = $salesFactory;
        $this->newsletterFactory = $newsletterFactory;
        $this->conversionsFactory = $conversionsFactory;
        $this->statsCollection = $statsFactory;
        $this->subscriberCoreFactory = $subscriberCore;
        $this->pandaHelper = $pandaHelper;
        $this->scopeConfig = $scope;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->subscribersFactory = $subscribersFactory;
        $this->extraFieldsFactory = $extraFieldsFactory;

        $this->loadSubscriberExtraFields();
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Subscribers::class);
    }

    /**
     *
     */
    public function loadSubscriberExtraFields()
    {

        $extraFields = $this->extraFieldsFactory->create()->getCollection();

        /** @var ExtraFields $extraField */
        foreach ($extraFields as $extraField) {
            $this->extraFieldsSubscriber[$extraField->getEntryCode()] = $extraField->getName();
        }

    }

    /**
     * @param bool|false $final
     *
     * @return mixed
     */
    public function getUnsubscriptionLink($final = false)
    {

        $params = [
            'id'     => $this->getId(),
            'code'   => $this->getCode(),
            '_nosid' => true,
        ];
        /** @var Campaigns $campaign */
        $campaign = $this->_registry->registry('panda_campaign');
        if ($campaign) {
            $params['c'] = $campaign->getId();
        }

        if ($final) {
            $params['final'] = '1';
        }

        $url = $this->urlBuilder
            ->setScope($this->getStoreId())
            ->getUrl('panda/subscriber/unsubscribe', $params);

        return $url;
    }

    /**
     *
     * @return string
     */
    public function getOnlineViewCampaignLink()
    {

        /** @var Campaigns $campaign */
        $campaign = $this->_registry->registry('panda_campaign');

        if (!$campaign) {
            return '';
        }

        $url = $this->storeManager->getStore($this->getStoreId())
                                  ->getBaseUrl();

        return $url . 'panda/campaign/view/c/' . $campaign->getId() . '/u/' . $this->getCode();
    }

    /**
     * @return string
     */
    public function getName()
    {

        $name = $this->getFirstname() . ' ' . $this->getLastname();

        if (strlen($name) == 1) {
            $name = substr($this->getEmail(), 0, stripos($this->getEmail(), '@'));
        }

        return $name;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {

        return $this->getData('firstname');
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {

        return $this->getData('lastname');
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {

        return $this->getData('email');
    }

    /**
     * @param        $value
     * @param string $attribute
     *
     * @return \Magento\Framework\DataObject|\Magento\Customer\Model\Customer
     */
    public function findCustomer($value, $attribute = 'entity_id')
    {

        $billing = false;
        $cellphoneField = $this->scopeConfig->getValue(
            'panda_nuntius/info/cellphone',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (substr($cellphoneField, 0, 3) == 'ac_') {
            $cellphoneField = substr($cellphoneField, 2);
        } elseif (substr($cellphoneField, 0, 3) == 'ad_') {
            $billing = true;
            $cellphoneField = substr($cellphoneField, 3);
        } else {
            $cellphoneField = false;
        }

        $customers = $this->customerCollection->create()
                                              ->addAttributeToSelect('firstname')
                                              ->addAttributeToSelect('lastname')
                                              ->addAttributeToSelect('store_id')
                                              ->addAttributeToSelect('dob')
                                              ->addAttributeToSelect('gender')
                                              ->addAttributeToFilter($attribute, $value)
                                              ->joinAttribute(
                                                  'country_id',
                                                  'customer_address/country_id',
                                                  'default_billing',
                                                  null,
                                                  'left'
                                              );

        if ($cellphoneField) {
            $customers->addAttributeToSelect($cellphoneField);
        }

        if ($billing) {
            $customers->joinAttribute(
                $cellphoneField,
                'customer_address/' . $cellphoneField,
                'default_billing',
                null,
                'left'
            );
        }

        if ($customers->count() == 1) {
            $customer = $customers->getFirstItem();
            if (strlen($customer->getData($cellphoneField)) > 5) {
                $customer->setData(
                    'cellphone',
                    \Licentia\Panda\Helper\Data::getPrefixForCountry($customer->getCountryId()) . '-' . preg_replace(
                        '/\D/',
                        '',
                        $customer->getData($cellphoneField)
                    )
                );
            }

            return $customer;
        }

        return new \Magento\Framework\DataObject();
    }

    /**
     * @param            $email
     * @param bool|false $alsoInactive
     * @param bool       $storeId
     *
     * @return null
     */
    public function getSubscriber($email, $alsoInactive = false, $storeId = null)
    {

        if ($storeId === null) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $collection = $this->subscriberCollection->create()
                                                 ->addFieldToFilter('store_id', $storeId)
                                                 ->addFieldToFilter('email', $email);

        if ($alsoInactive === false) {
            $collection->addFieldToFilter('status', self::STATUS_SUBSCRIBED);
        }

        if ($collection->count() == 1) {
            return $collection->getFirstItem();
        }

        return null;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importCoreNewsletterSubscribers()
    {

        $news = $this->newsletterFactory->create();

        $this->_registry->register('panda_core_first', true);

        $i = 0;
        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
        foreach ($news as $subscriber) {
            if (!$subscriber->getStoreId()) {
                continue;
            }

            $data = [];
            if ($this->loadSubscriber($subscriber->getEmail(), $subscriber->getStoreId())) {
                continue;
            }

            $data['email'] = $subscriber->getEmail();
            $data['status'] = $subscriber->getStatus() == self::STATUS_SUBSCRIBED ? self::STATUS_SUBSCRIBED : self::STATUS_UNSUBSCRIBED;
            $data['store_id'] = $subscriber->getStoreId();

            if ($subscriber->getCustomerId()) {
                $data['customer_id'] = $subscriber->getCustomerId();
            }

            $panda = $this->subscribersFactory->create();

            try {
                $panda->addData($data)
                      ->save();
                $i++;
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Cannot import subscriber %1', $data['email'])
                );
            }
        }

        return $i;
    }

    /**
     * @param      $id
     *
     * @return $this
     */
    public function loadById($id)
    {

        return $this->load($id);
    }

    /**
     * @param      $code
     * @param null $store
     *
     * @return Subscribers
     */
    public function loadByCode($code, $store = null)
    {

        return $this->loadSubscriber($code, $store, 'code');
    }

    /**
     * @param      $email
     * @param null $store
     *
     * @return $this
     */
    public function loadByEmail($email, $store = null)
    {

        return $this->loadSubscriber($email, $store, 'email');
    }

    /**
     * @param        $value
     * @param null   $storeId
     * @param string $field
     *
     * @return $this
     */
    public function loadSubscriber($value, $storeId = null, $field = 'email')
    {

        if (!$this->scopeConfig->isSetFlag('panda_nuntius/info/enabled')) {
            if ($field == 'email') {
                return $this->subscriberCoreFactory->create()->loadByEmail($value);
            }
            if ($field == 'code') {
                return $this->subscriberCoreFactory->create()->load($value, 'subscriber_confirm_code');
            }
        }

        $model = $this->subscriberCollection->create();

        if ($storeId instanceof \Magento\Store\Model\StoreManagerInterface) {
            $storeId = $storeId->getStore()->getId();
        }

        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $model->addFieldToFilter('store_id', ['in' => [0, $storeId]]);
        $model->addFieldToFilter($field, $value);

        return $this->load($model->getFirstItem()->getId());
    }

    /**
     * @return $this|\Magento\Framework\Model\AbstractModel
     */
    public function save()
    {

        if (!$this->scopeConfig->isSetFlag('panda_nuntius/info/enabled')) {
            return $this;
        }

        return parent::save();
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function afterSave()
    {

        if ($this->getSubscriberId() &&
            $this->getStatus() &&
            $this->getOrigData('status') &&
            $this->getOrigData('status') == self::STATUS_SUBSCRIBED &&
            $this->getOrigData('status') != $this->getStatus() &&
            $this->getStatus() != self::STATUS_SUBSCRIBED) {
            $this->getResource()
                 ->getConnection()
                 ->delete($this->getResource()->getTable('panda_messages_queue'),
                     ['subscriber_id=?' => $this->getId()]);
        }

        if ($this->getId()) {
            /** @var \Magento\Newsletter\Model\Subscriber $core */
            $core = $this->subscriberCoreFactory->create()->loadByEmail($this->getEmail());

            if (!$core->getId()) {

                $core->setStatus($this->getStatus())
                     ->setStoreId($this->getStoreId())
                     ->setSubscriberEmail($this->getEmail())
                     ->setSubscriberConfirmCode($this->getCode())
                     ->setImportMode(true)
                     ->setData('in_panda', true)
                     ->save();

            } else {
                $core->setStatus($this->getStatus())
                     ->setStoreId($this->getStoreId())
                     ->setImportMode(true)
                     ->setData('in_panda', true)
                     ->save();
            }

        }

        return parent::afterSave();
    }

    /**
     *
     */
    public function getSubscriberSegments()
    {

        return $this->listSegmentsFactory->create()->getSubscriberSegments($this->getId());

    }

    /**
     * @return mixed
     */
    public function getSubscriberSegmentsAsArray()
    {

        return $this->getSubscriberSegments()->toOptionHash();
    }

    /**
     * @return bool|\Magento\Framework\Model\AbstractModel
     */
    public function beforeSave()
    {

        if (!$this->scopeConfig->isSetFlag('panda_nuntius/info/enabled')) {
            return $this;
        }

        if (!$this->getEmail()) {
            return parent::beforeSave();
        }

        if (!$this->getId()) {
            $exists = $this->loadSubscriber($this->getEmail(), $this->getStoreId());

            if ($exists && $exists->getId()) {
                $this->setId($exists->getId());
            }
        }

        if (!$this->getCustomerId()) {
            $customer = $this->findCustomer($this->getEmail(), 'email');

            if ($customer->getId()) {
                $this->setCustomerId($customer->getId());
                $this->addData($customer->getData());
            }
        }

        if (strlen($this->getCode()) == 0) {
            $this->setCode(sha1($this->getId() . $this->getEmail()));
        }

        if (!$this->getCreatedAt()) {
            $this->setCreatedAt($this->pandaHelper->gmtDate());
        }

        if ($this->getGender() == 1) {
            $this->setGender('male');
        }
        if ($this->getGender() == 2) {
            $this->setGender('female');
        }

        if ($this->getCustomerId()) {
            $this->metadataFactory->create()->updateSegments($this->getCustomerId(), 'subscriber');
        }

        return parent::beforeSave();
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function getSubscribersIdFromQuote($data)
    {

        $ids = [];

        if (!is_array($data)) {
            return $ids;
        }

        foreach ($data as $item) {
            $ids[] = $this->loadSubscriber($item['email'], $data['store_id']);
        }

        return array_filter($ids);
    }

    /**
     *
     * @param $customerId
     *
     * @return boolean
     */
    public function addCustomerToList($customerId)
    {

        try {
            $customer = $this->findCustomer($customerId);

            if (!$customer->getId()) {
                return false;
            }

            $this->loadSubscriber($customer->getEmail(), $customer->getStoreId());
            if (!$this->getId()) {
                $this->loadSubscriber($customer->getId(), $customer->getStoreId(), 'customer_id');
            }

            if (!$this->getId()) {
                if ($this->scopeConfig->isSetFlag('panda_nuntius/info/auto')) {
                    $this->subscribe($customer->getEmail());
                }
            }
        } catch (\Exception $e) {
        }

        return $this;
    }

    /**
     *
     * @return bool
     */
    public function isSubscribed()
    {

        return $this->subscriberCoreFactory->create()->loadByEmail($this->getEmail())->isSubscribed();
    }

    /**
     * @return $this
     */
    public function updateSendTime()
    {

        $subscribers = $this->subscriberCollection->create()
                                                  ->addFieldToSelect('subscriber_id')
                                                  ->addFieldToSelect('email');

        /** @var  Subscribers $subscriber */
        foreach ($subscribers as $subscriber) {
            $conv = $this->conversionsFactory->create()
                                             ->addFieldToSelect('created_at')
                                             ->addTimeToSelect()
                                             ->setOrder('count_created_at', 'DESC')
                                             ->setPageSize(2)
                                             ->addFieldToFilter('subscriber_id', $subscriber->getId());

            $hour = false;
            /** @var Conversions $c */
            foreach ($conv as $c) {
                if ($c->getCreatedAt() == 0) {
                    continue;
                }
                $hour = $c->getCreatedAt();
                break;
            }

            if ($hour === false) {
                $orders = $this->salesCollection->create()
                                                ->addFieldToSelect('created_at')
                                                ->addFieldToFilter('customer_email', $subscriber->getEmail())
                                                ->addFieldToFilter('state', 'complete')
                                                ->setOrder('created_at', 'DESC')
                                                ->setPageSize(1);

                if ($orders->count() > 0) {
                    /** @var  \Magento\Sales\Model\Order $order */
                    $order = $orders->getFirstItem();
                    $datetime = new \DateTime($order->getCreatedAt());
                    $hour = $datetime->format('%H');
                }
            }

            if ($hour === false) {
                $stats = $this->statsCollection->create()
                                               ->addFieldToFilter('subscriber_id', $subscriber->getId())
                                               ->addFieldToSelect('event_at')
                                               ->addTimeToSelect()
                                               ->setOrder('count_event_at', 'DESC')
                                               ->setPageSize(2);

                $hour = false;
                foreach ($stats as $c) {
                    if ($c->getData('event_at') == 0) {
                        continue;
                    }
                    $hour = $c->getData('event_at');
                    break;
                }
            }

            if ($hour === false) {
                $hour = -1;
            }

            $subscriber->setSendTime($hour)
                       ->save();
        }

        return $this;
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function delete()
    {

        $email = $this->getEmail();

        if (!$this->getEmail()) {
            $this->load($this->getId());
            $email = $this->getEmail();
        }

        $this->subscriberCoreFactory->create()->loadByEmail($email)->delete();

        return parent::delete();
    }

    /**
     * @param $product
     *
     * @return float
     */
    public function getDaysCycle($product)
    {

        $attribute = $this->scopeConfig->getValue(
            'panda_nuntius/info/attribute',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );

        $orders = $this->salesCollection->create()
                                        ->addFieldToFilter('customer_email', $this->getEmail())
                                        ->addFieldToFilter('state', 'complete')
                                        ->setOrder('created_at', 'DESC');

        $data = [];
        $previousDate = $this->pandaHelper->gmtDate();

        /** @var \Magento\Sales\model\Order $order */
        foreach ($orders as $order) {
            $items = $order->getItems();

            foreach ($items as $item) {
                if ($item->getProductId() == $product->getId()) {
                    $datetime1 = new \DateTime($order->getCreatedAt());
                    $datetime2 = new \DateTime($previousDate);
                    $interval = $datetime1->diff($datetime2);
                    $data[] = $interval->format('%a');
                }
            }

            $previousDate = $order->getCreatedAt();
        }

        if (count($data) > 1) {
            return round(array_sum($data) / count($data));
        }

        if (!$attribute) {
            return false;
        }

        return $product->getData($attribute);
    }

    /**
     * @param $email
     *
     * @return bool
     */
    public function unsubscribe($email = null)
    {

        if (!$email) {
            $email = $this->getEmail();
        }

        $result = $this->subscriberCoreFactory->create()->loadByEmail($email);
        if ($result->getId()) {
            $result->unsubscribe();
        } else {
            if ($this->getStatus() != self::STATUS_UNSUBSCRIBED) {
                $result = $this->setStatus(self::STATUS_UNSUBSCRIBED)->save();
            } else {
                $result = $this;
            }
        }

        return $result->getStatus() == self::STATUS_UNSUBSCRIBED ? true : false;
    }

    /**
     * @param $email
     *
     * @return $this|bool
     */
    public function subscribe($email = null)
    {

        if (!$email) {
            $email = $this->getEmail();
        }
        if (!$email) {
            return false;
        }

        return $this->subscriberCoreFactory->create()->subscribe($email);
    }

    /**
     * @param $customerId
     *
     * @return Subscribers|\Magento\Framework\DataObject
     */
    public function loadByCustomerId($customerId)
    {

        if (!$this->scopeConfig->isSetFlag('panda_nuntius/info/enabled')) {
            return $this->subscriberCoreFactory->create()->loadByCustomerId($customerId);
        }

        $storeId = $this->storeManager->getStore()->getId();

        $result = $this->subscriberCollection->create()
                                             ->addFieldToFilter('store_id', ['in' => [0, $storeId]])
                                             ->addFieldToFilter('customer_id', $customerId);

        if ($result->getSize() != 1) {
            return new \Magento\Framework\DataObject;
        }

        return $this->loadById($result->getFirstItem()->getId());
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     */
    public function checkCookieCustomerLogin(\Magento\Framework\Event\Observer $event)
    {

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $event->getCustomer();

        if ($subscriberCode = $this->pandaHelper->getIdentifierValueFromCode('subscriber_code')) {
            $subscriber = $this->loadByCode($subscriberCode);

            if ($subscriber->getId() && $subscriber->getCustomerId() != $customer->getId()) {
                $this->pandaHelper->updateIdentifierValue($subscriberCode, 'subscriber_code', $subscriber->getCode());
            }
        } else {
            $subscriber = $this->loadByCustomerId($customer->getId());

            if ($subscriber->getId()) {
                $this->pandaHelper->addIdentifierValueFromArea('subscriber_code', $subscriber->getCode());
            }
        }
    }

    /**
     * @return $this
     */
    public function syncCustomerSubscriberData()
    {

        $collection = $this->getCollection()->addFieldToFilter('customer_id', ['gt' => 0]);

        $collection->getSelect()->where('LENGTH(firstname) = 0 OR firstname IS NULL');

        /** @var Subscribers $subscriber */
        foreach ($collection as $subscriber) {
            $customer = $this->findCustomer($subscriber->getCustomerId());

            if ($customer->getId()) {
                try {
                    $data['email'] = $customer->getData('email');
                    $data['dob'] = $customer->getData('dob');
                    $data['firstname'] = $customer->getData('firstname');
                    $data['lastname'] = $customer->getData('lastname');
                    $data['gender'] = $customer->getData('gender');

                    $subscriber->addData($data)
                               ->save();
                } catch (\Exception $e) {
                }
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getFieldsForAutoresponder()
    {

        $init = [
            'email'              => __('Email'),
            'firstname'          => __('First Name'),
            'lastname'           => __('Last name'),
            'cellphone'          => __('Cellphone'),
            'dob'                => __('Birth Date'),
            'conversions_number' => __('Conversions Number'),
            'conversions_amount' => __('Conversions Amount'),
        ];

        return $this->extraFieldsFactory->create()
                                        ->toOptionHash() + $init;
    }

    /**
     * @return mixed|string
     */
    public function getCellphone()
    {

        if ($this->getData('cellphone')) {
            return $this->getData('cellphone');
        }

        if ($this->getCustomerId() && $customer = $this->findCustomer($this->getCustomerId())) {
            if ($customer->getData('cellphone')) {
                $this->setData('cellphone', $customer->getData('cellphone'))
                     ->save();

                return $this->getData('cellphone');
            }
        }

        return '';
    }

    /**
     * @param Subscribers $subscriber
     * @param             $tagId
     *
     * @return $this
     */
    public function addTagToSubscriber(Subscribers $subscriber, $tagId)
    {

        $tags = $this->getSubscriberTags();
        $tags[] = $tagId;
        $tags = array_unique($tags);

        $this->tagsFactory->create()->updateTags('subscribers', $subscriber, $tags);

        return $this;
    }

    /**
     * @param Subscribers $subscriber
     * @param             $tagId
     *
     * @return $this
     */
    public function removeTagFromSubscriber(Subscribers $subscriber, $tagId)
    {

        $tags = $this->getSubscriberTags();
        unset($tags[$tagId]);
        $tags = array_unique($tags);

        $this->tagsFactory->create()->updateTags('subscribers', $subscriber, $tags);

        return $this;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     *
     * @return $this
     */
    public function afterOrderEvent(\Magento\Framework\Event\Observer $event)
    {

        /** @var  \Magento\Sales\Model\Order $order */
        $order = $event->getEvent()->getOrder();

        /** @var Subscribers $subscriber */
        $subscriber = $this->loadSubscriber($order->getCustomerEmail(), $order->getStoreId(), 'email');

        if (!$subscriber->getId() && !$this->scopeConfig->isSetFlag('panda_nuntius/info/auto')) {
            return $this;
        }

        $this->subscribe($order->getCustomerEmail());

        $subscriber->loadByEmail($order->getCustomerEmail());

        if ($subscriber && $subscriber->getId() && $subscriber->isSubscribed()) {
            $subscriber->setSendTime(date('H', strtotime($order->getCreatedAt())));

            $data = [
                'store_id'          => $order->getStoreId(),
                'email'             => $order->getCustomerEmail(),
                'firstname'         => $order->getCustomerFirstname(),
                'lastname'          => $order->getCustomerLastname(),
                'previous_customer' => 1,
            ];

            $subscriber->addData($data)
                       ->save();
        }

        return $this;
    }

    /**
     * @return Kpis
     */
    public function getKpis()
    {

        if (!$this->kpis) {
            $this->kpis = $this->kpisFactory->create()->loadByEmail($this->getEmail());
        }

        return $this->kpis;
    }

    /**
     * @return $this
     */
    public function afterLoad()
    {

        parent::afterLoad();

        if ($this->getId()) {
            $tags = $this->getSubscriberTags();

            $this->setData('tags', array_flip($tags));
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function afterDelete()
    {

        parent::afterDelete();

        $core = $this->subscriberCoreFactory->create()->loadByEmail($this->getEmail());

        if ($core->getId()) {
            $core->delete();
        }

        $this->tagsFactory->create()->updateTags('subscribers', $this, []);

        return $this;
    }

    /**
     * @return array
     */
    public function getSubscriberTags()
    {

        return $this->tagsFactory->create()->getTagsHash('subscribers', $this->getId());
    }

    /**
     * @param $subscriberId
     *
     * @return $this
     */
    public function setSubscriberId($subscriberId)
    {

        return $this->setData('subscriber_id', $subscriberId);
    }

    /**
     * @param $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {

        return $this->setData('customer_id', $customerId);
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
     * @param $code
     *
     * @return $this
     */
    public function setCode($code)
    {

        return $this->setData('code', $code);
    }

    /**
     * @param $firstname
     *
     * @return $this
     */
    public function setFirstname($firstname)
    {

        return $this->setData('firstname', $firstname);
    }

    /**
     * @param $lastname
     *
     * @return $this
     */
    public function setLastname($lastname)
    {

        return $this->setData('lastname', $lastname);
    }

    /**
     * @param $email
     *
     * @return $this
     */
    public function setEmail($email)
    {

        return $this->setData('email', $email);
    }

    /**
     * @param $cellphone
     *
     * @return $this
     */
    public function setCellphone($cellphone)
    {

        return $this->setData('cellphone', $cellphone);
    }

    /**
     * @param $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {

        return $this->setData('created_at', $createdAt);
    }

    /**
     * @param $dob
     *
     * @return $this
     */
    public function setDob($dob)
    {

        return $this->setData('dob', $dob);
    }

    /**
     * @param $status
     *
     * @return $this
     */
    public function setStatus($status)
    {

        return $this->setData('status', $status);
    }

    /**
     * @param $bounces
     *
     * @return $this
     */
    public function setBounces($bounces)
    {

        return $this->setData('bounces', $bounces);
    }

    /**
     * @param $sent
     *
     * @return $this
     */
    public function setSent($sent)
    {

        return $this->setData('sent', $sent);
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
     * @param $clicks
     *
     * @return $this
     */
    public function setClicks($clicks)
    {

        return $this->setData('clicks', $clicks);
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
     * @param $sendTime
     *
     * @return $this
     */
    public function setSendTime($sendTime)
    {

        return $this->setData('send_time', $sendTime);
    }

    /**
     * @param $previousCustomer
     *
     * @return $this
     */
    public function setPreviousCustomer($previousCustomer)
    {

        return $this->setData('previous_customer', $previousCustomer);
    }

    /**
     * @param $gender
     *
     * @return $this
     */
    public function setGender($gender)
    {

        return $this->setData('gender', $gender);
    }

    /**
     * @param $lastMessageSentAt
     *
     * @return $this
     */
    public function setLastMessageSentAt($lastMessageSentAt)
    {

        return $this->setData('last_message_sent_at', $lastMessageSentAt);
    }

    /**
     * @param $lastMessageOpenAt
     *
     * @return $this
     */
    public function setLastMessageOpenAt($lastMessageOpenAt)
    {

        return $this->setData('last_message_open_at', $lastMessageOpenAt);
    }

    /**
     * @param $lastConversionAt
     *
     * @return $this
     */
    public function setLastConversionAt($lastConversionAt)
    {

        return $this->setData('last_conversion_at', $lastConversionAt);
    }

    /**
     * @param $lastMessageClickAt
     *
     * @return $this
     */
    public function setLastMessageClickAt($lastMessageClickAt)
    {

        return $this->setData('last_message_click_at', $lastMessageClickAt);
    }

    /**
     * @param $lastOpenCampaignId
     *
     * @return $this
     */
    public function setLastOpenCampaignId($lastOpenCampaignId)
    {

        return $this->setData('last_open_campaign_id', $lastOpenCampaignId);
    }

    /**
     * @param $lastClickCampaignId
     *
     * @return $this
     */
    public function setLastClickCampaignId($lastClickCampaignId)
    {

        return $this->setData('last_click_campaign_id', $lastClickCampaignId);
    }

    /**
     * @param $lastConversionCampaignId
     *
     * @return $this
     */
    public function setLastConversionCampaignId($lastConversionCampaignId)
    {

        return $this->setData('last_conversion_campaign_id', $lastConversionCampaignId);
    }

    /**
     * @param $unsubscribedAt
     *
     * @return $this
     */
    public function setUnsubscribedAt($unsubscribedAt)
    {

        return $this->setData('unsubscribed_at', $unsubscribedAt);
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
     * @return mixed
     */
    public function getSubscriberId()
    {

        return $this->getData('subscriber_id');
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {

        return $this->getData('customer_id');
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
    public function getCode()
    {

        return $this->getData('code');
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {

        return $this->getData('created_at');
    }

    /**
     * @return mixed
     */
    public function getDob()
    {

        return $this->getData('dob');
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {

        return $this->getData('status');
    }

    /**
     * @return mixed
     */
    public function getBounces()
    {

        return $this->getData('bounces');
    }

    /**
     * @return mixed
     */
    public function getSent()
    {

        return $this->getData('sent');
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
    public function getClicks()
    {

        return $this->getData('clicks');
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
    public function getSendTime()
    {

        return $this->getData('send_time');
    }

    /**
     * @return mixed
     */
    public function getPreviousCustomer()
    {

        return $this->getData('previous_customer');
    }

    /**
     * @return mixed
     */
    public function getGender()
    {

        return $this->getData('gender');
    }

    /**
     * @return mixed
     */
    public function getLastMessageSentAt()
    {

        return $this->getData('last_message_sent_at');
    }

    /**
     * @return mixed
     */
    public function getLastMessageOpenAt()
    {

        return $this->getData('last_message_open_at');
    }

    /**
     * @return mixed
     */
    public function getLastConversionAt()
    {

        return $this->getData('last_conversion_at');
    }

    /**
     * @return mixed
     */
    public function getLastMessageClickAt()
    {

        return $this->getData('last_message_click_at');
    }

    /**
     * @return mixed
     */
    public function getLastOpenCampaignId()
    {

        return $this->getData('last_open_campaign_id');
    }

    /**
     * @return mixed
     */
    public function getLastClickCampaignId()
    {

        return $this->getData('last_click_campaign_id');
    }

    /**
     * @return mixed
     */
    public function getLastConversionCampaignId()
    {

        return $this->getData('last_conversion_campaign_id');
    }

    /**
     * @return mixed
     */
    public function getUnsubscribedAt()
    {

        return $this->getData('unsubscribed_at');
    }

    /**
     * @return mixed
     */
    public function getFormId()
    {

        return $this->getData('form_id');
    }

    /**
     * @param $field1
     *
     * @return $this
     */
    public function setField1($field1)
    {

        return $this->setData('field1', $field1);
    }

    /**
     * @param $field2
     *
     * @return $this
     */
    public function setField2($field2)
    {

        return $this->setData('field2', $field2);
    }

    /**
     * @param $field3
     *
     * @return $this
     */
    public function setField3($field3)
    {

        return $this->setData('field3', $field3);
    }

    /**
     * @param $field4
     *
     * @return $this
     */
    public function setField4($field4)
    {

        return $this->setData('field4', $field4);
    }

    /**
     * @param $field5
     *
     * @return $this
     */
    public function setField5($field5)
    {

        return $this->setData('field5', $field5);
    }

    /**
     * @param $field6
     *
     * @return $this
     */
    public function setField6($field6)
    {

        return $this->setData('field6', $field6);
    }

    /**
     * @param $field7
     *
     * @return $this
     */
    public function setField7($field7)
    {

        return $this->setData('field7', $field7);
    }

    /**
     * @param $field8
     *
     * @return $this
     */
    public function setField8($field8)
    {

        return $this->setData('field8', $field8);
    }

    /**
     * @param $field9
     *
     * @return $this
     */
    public function setField9($field9)
    {

        return $this->setData('field9', $field9);
    }

    /**
     * @param $field10
     *
     * @return $this
     */
    public function setField10($field10)
    {

        return $this->setData('field10', $field10);
    }

    /**
     * @param $field11
     *
     * @return $this
     */
    public function setField11($field11)
    {

        return $this->setData('field11', $field11);
    }

    /**
     * @param $field12
     *
     * @return $this
     */
    public function setField12($field12)
    {

        return $this->setData('field12', $field12);
    }

    /**
     * @param $field13
     *
     * @return $this
     */
    public function setField13($field13)
    {

        return $this->setData('field13', $field13);
    }

    /**
     * @param $field14
     *
     * @return $this
     */
    public function setField14($field14)
    {

        return $this->setData('field14', $field14);
    }

    /**
     * @param $field15
     *
     * @return $this
     */
    public function setField15($field15)
    {

        return $this->setData('field15', $field15);
    }

    /**
     * @return mixed
     */
    public function getField1()
    {

        return $this->getData('field1');
    }

    /**
     * @return mixed
     */
    public function getField2()
    {

        return $this->getData('field2');
    }

    /**
     * @return mixed
     */
    public function getField3()
    {

        return $this->getData('field3');
    }

    /**
     * @return mixed
     */
    public function getField4()
    {

        return $this->getData('field4');
    }

    /**
     * @return mixed
     */
    public function getField5()
    {

        return $this->getData('field5');
    }

    /**
     * @return mixed
     */
    public function getField6()
    {

        return $this->getData('field6');
    }

    /**
     * @return mixed
     */
    public function getField7()
    {

        return $this->getData('field7');
    }

    /**
     * @return mixed
     */
    public function getField8()
    {

        return $this->getData('field8');
    }

    /**
     * @return mixed
     */
    public function getField9()
    {

        return $this->getData('field9');
    }

    /**
     * @return mixed
     */
    public function getField10()
    {

        return $this->getData('field10');
    }

    /**
     * @return mixed
     */
    public function getField11()
    {

        return $this->getData('field11');
    }

    /**
     * @return mixed
     */
    public function getField12()
    {

        return $this->getData('field12');
    }

    /**
     * @return mixed
     */
    public function getField13()
    {

        return $this->getData('field13');
    }

    /**
     * @return mixed
     */
    public function getField14()
    {

        return $this->getData('field14');
    }

    /**
     * @return mixed
     */
    public function getField15()
    {

        return $this->getData('field15');
    }

    /**
     * @return string|void
     */
    public function getField1Name()
    {

        return isset($this->extraFieldsSubscriber[1]) ? $this->extraFieldsSubscriber[1] : null;
    }

    /**
     * @return string|void
     */
    public function getField2Name()
    {

        return isset($this->extraFieldsSubscriber[2]) ? $this->extraFieldsSubscriber[2] : null;
    }

    /**
     * @return string|void
     */
    public function getField3Name()
    {

        return isset($this->extraFieldsSubscriber[3]) ? $this->extraFieldsSubscriber[3] : null;
    }

    /**
     * @return string|void
     */
    public function getField4Name()
    {

        return isset($this->extraFieldsSubscriber[4]) ? $this->extraFieldsSubscriber[4] : null;
    }

    /**
     * @return string|void
     */
    public function getField5Name()
    {

        return isset($this->extraFieldsSubscriber[5]) ? $this->extraFieldsSubscriber[5] : null;
    }

    /**
     * @return string|void
     */
    public function getField6Name()
    {

        return isset($this->extraFieldsSubscriber[6]) ? $this->extraFieldsSubscriber[6] : null;
    }

    /**
     * @return string|void
     */
    public function getField7Name()
    {

        return isset($this->extraFieldsSubscriber[7]) ? $this->extraFieldsSubscriber[7] : null;
    }

    /**
     * @return string|void
     */
    public function getField8Name()
    {

        return isset($this->extraFieldsSubscriber[8]) ? $this->extraFieldsSubscriber[8] : null;
    }

    /**
     * @return string|void
     */
    public function getField9Name()
    {

        return isset($this->extraFieldsSubscriber[9]) ? $this->extraFieldsSubscriber[9] : null;
    }

    /**
     * @return string|void
     */
    public function getField10Name()
    {

        return isset($this->extraFieldsSubscriber[10]) ? $this->extraFieldsSubscriber[10] : null;
    }

    /**
     * @return string|void
     */
    public function getField11Name()
    {

        return isset($this->extraFieldsSubscriber[11]) ? $this->extraFieldsSubscriber[11] : null;
    }

    /**
     * @return string|void
     */
    public function getField12Name()
    {

        return isset($this->extraFieldsSubscriber[12]) ? $this->extraFieldsSubscriber[12] : null;
    }

    /**
     * @return string|void
     */
    public function getField13Name()
    {

        return isset($this->extraFieldsSubscriber[13]) ? $this->extraFieldsSubscriber[13] : null;
    }

    /**
     * @return string|void
     */
    public function getField14Name()
    {

        return isset($this->extraFieldsSubscriber[14]) ? $this->extraFieldsSubscriber[14] : null;
    }

    /**
     * @return string|void
     */
    public function getField15Name()
    {

        return isset($this->extraFieldsSubscriber[15]) ? $this->extraFieldsSubscriber[15] : null;
    }
}
