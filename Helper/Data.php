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

namespace Licentia\Panda\Helper;

use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 *
 * @package Licentia\Panda\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     *
     */
    const GEO_LOCATION_URL_LOCATION = 'https://pro.ip-api.com/json/';

    /**
     *
     */
    const PANDA_COOKIE_NAME = 'psinfo';

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata
     */
    protected $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected \Magento\Framework\Registry $registry;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Cron\Model\Schedule
     */
    protected $scheduleFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Newsletter\Model\Template\Filter
     */
    protected $templateFilter;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $quoteSession;

    /**
     * @var \Magento\Framework\View\DesignInterface
     */
    protected \Magento\Framework\View\DesignInterface $design;

    /**
     * @var \Licentia\Equity\Model\ResourceModel\Segments\ListSegments\CollectionFactory
     */
    protected \Licentia\Equity\Model\ResourceModel\Segments\ListSegments\CollectionFactory $listSegmentsCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected \Magento\Store\Model\StoreManagerInterface $storeManager;

    /**
     * @var \Magento\Reports\Model\ResourceModel\Quote\CollectionFactory
     */
    protected \Magento\Reports\Model\ResourceModel\Quote\CollectionFactory $quoteCollection;

    /**
     * @var \Magento\Persistent\Helper\Session
     */
    protected $persistentHelper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollection;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory
     */
    protected $eavAttributeCollection;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected \Magento\Framework\ObjectManagerInterface $objectManager;

    /**
     * @var \Licentia\Equity\Model\ResourceModel\Index\CollectionFactory
     */
    protected $pricesCollection;

    /**
     * @var \Licentia\Reports\Model\ResourceModel\Indexer\CollectionFactory
     */
    protected \Licentia\Reports\Model\ResourceModel\Indexer\CollectionFactory $indexerCollection;

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected \Licentia\Panda\Model\SubscribersFactory $subscribersFactory;

    /**
     * @var \Licentia\Panda\Model\Session
     */
    protected $pandaSession;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected \Magento\Framework\Encryption\EncryptorInterface $encryptor;

    /**
     * @var \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory
     */
    protected \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory $sendersCollection;

    /**
     * @var \Licentia\Panda\Model\SendersFactory
     */
    protected \Licentia\Panda\Model\SendersFactory $sendersFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected \Magento\Catalog\Model\ProductFactory $productFactory;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected \Magento\Cms\Model\PageFactory $pageFactory;

    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected \Magento\Cms\Model\BlockFactory $blockFactory;

    /**
     * @var \Magento\Framework\App\Cache\Manager
     */
    protected \Magento\Framework\App\Cache\Manager $cacheManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected \Magento\Framework\Stdlib\DateTime\DateTime $dateTime;

    /**
     * @var null
     */
    protected ?string $customerEmail = null;

    /**
     * @var null
     */
    protected $customerId = null;

    /**
     * @var null
     */
    protected $subscriberCache = null;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected \Magento\Framework\HTTP\Client\Curl $curl;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface $_cacheState
     */
    protected \Magento\Framework\App\Cache\StateInterface $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList;

    /**
     * @var \Licentia\Panda\Model\ExceptionsFactory
     */
    protected \Licentia\Panda\Model\ExceptionsFactory $exceptionsFactory;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Magento\Framework\Model\ResourceModel\AbstractResource|\Magento\Framework\Model\ResourceModel\Db\AbstractDb|null
     */
    protected $resource;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Cache\TypeListInterface                               $typeList
     * @param \Magento\Framework\App\Cache\StateInterface                                  $cacheState
     * @param \Magento\Framework\HTTP\Client\Curl                                          $curl
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                                  $dateTime
     * @param \Magento\Framework\App\Cache\Manager                                         $cacheManager
     * @param Context                                                                      $context
     * @param \Magento\Cms\Model\PageFactory                                               $pageFactory
     * @param \Magento\Cms\Model\BlockFactory                                              $blockFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface                         $timezone
     * @param \Magento\Framework\Registry                                                  $coreRegistry
     * @param \Magento\Catalog\Model\CategoryFactory                                       $category
     * @param \Magento\Catalog\Model\ProductFactory                                        $productFactory
     * @param \Magento\Cron\Model\ScheduleFactory                                          $cron
     * @param \Magento\Customer\Api\CustomerRepositoryInterface                            $customer
     * @param \Magento\Framework\View\DesignInterface                                      $designInterface
     * @param \Magento\Store\Model\StoreManagerInterface                                   $storeManagerInterface
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory     $attrCollection
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory   $eavAttributeCollection
     * @param \Licentia\Equity\Model\ResourceModel\Segments\ListSegments\CollectionFactory $listSegmentsCollection
     * @param \Licentia\Equity\Model\ResourceModel\Prices\CollectionFactory                $pricesCollection
     * @param \Licentia\Reports\Model\ResourceModel\Indexer\CollectionFactory              $indexerCollection
     * @param \Magento\Reports\Model\ResourceModel\Quote\CollectionFactory                 $quoteCollection
     * @param \Magento\Framework\Encryption\EncryptorInterface                             $encryptorInterface
     * @param \Magento\Framework\ObjectManagerInterface                                    $objectManager
     * @param \Licentia\Panda\Model\SubscribersFactory                                     $subscribersFactory
     * @param \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory                $sendersCollection
     * @param \Licentia\Panda\Model\ExceptionsFactory                                      $exceptionsFactory
     * @param \Licentia\Panda\Model\SendersFactory                                         $sendersFactory
     */
    public function __construct(
        \Magento\Framework\App\Cache\TypeListInterface $typeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\App\Cache\Manager $cacheManager,
        Context $context,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\CategoryFactory $category,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Cron\Model\ScheduleFactory $cron,
        \Magento\Customer\Api\CustomerRepositoryInterface $customer,
        \Magento\Framework\View\DesignInterface $designInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attrCollection,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $eavAttributeCollection,
        \Licentia\Equity\Model\ResourceModel\Segments\ListSegments\CollectionFactory $listSegmentsCollection,
        \Licentia\Equity\Model\ResourceModel\Prices\CollectionFactory $pricesCollection,
        \Licentia\Reports\Model\ResourceModel\Indexer\CollectionFactory $indexerCollection,
        \Magento\Reports\Model\ResourceModel\Quote\CollectionFactory $quoteCollection,
        \Magento\Framework\Encryption\EncryptorInterface $encryptorInterface,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        \Licentia\Panda\Model\ResourceModel\Senders\CollectionFactory $sendersCollection,
        \Licentia\Panda\Model\ExceptionsFactory $exceptionsFactory,
        \Licentia\Panda\Model\SendersFactory $sendersFactory
    ) {

        parent::__construct($context);

        $this->objectManager = $objectManager;

        if (stripos(php_sapi_name(), "cli") === false) {
            $httpContext = $this->objectManager->get('\Magento\Framework\App\Http\Context');
            $customerSession = $this->objectManager->get('\Magento\Customer\Model\Session');
            $persistentHelper = $this->objectManager->get('\Magento\Persistent\Helper\Session');
            $quoteSession = $this->objectManager->get('\Magento\Backend\Model\Session\Quote');
            $cookieManager = $this->objectManager->get('\Magento\Framework\Stdlib\CookieManagerInterface');
            $cookieMetadata = $this->objectManager->get('\Magento\Framework\Stdlib\Cookie\PublicCookieMetadata');
            $session = $this->objectManager->get('\Licentia\Panda\Model\Session');
            $templateFilter = $this->objectManager->create('\Magento\Newsletter\Model\Template\Filter');
        } else {
            $httpContext = null;
            $customerSession = null;
            $persistentHelper = null;
            $quoteSession = null;
            $cookieManager = null;
            $cookieMetadata = null;
            $session = null;
            $templateFilter = null;
        }

        $this->httpContext = $httpContext;
        $this->cacheManager = $cacheManager;
        $this->timezone = $timezone;
        $this->design = $designInterface;
        $this->quoteSession = $quoteSession;
        $this->listSegmentsCollection = $listSegmentsCollection;
        $this->pricesCollection = $pricesCollection;
        $this->indexerCollection = $indexerCollection;
        $this->storeManager = $storeManagerInterface;
        $this->attributeCollection = $attrCollection;
        $this->eavAttributeCollection = $eavAttributeCollection;
        $this->dateTime = $dateTime;

        $this->registry = $coreRegistry;
        $this->customerRepository = $customer;
        $this->scheduleFactory = $cron;
        $this->categoryFactory = $category;
        $this->customerSession = $customerSession;
        $this->quoteCollection = $quoteCollection;
        $this->templateFilter = $templateFilter;
        $this->persistentHelper = $persistentHelper;
        $this->subscribersFactory = $subscribersFactory;
        $this->pandaSession = $session;
        $this->encryptor = $encryptorInterface;
        $this->sendersCollection = $sendersCollection;
        $this->sendersFactory = $sendersFactory;
        $this->exceptionsFactory = $exceptionsFactory;
        $this->productFactory = $productFactory;
        $this->pageFactory = $pageFactory;
        $this->blockFactory = $blockFactory;

        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadata;
        $this->curl = $curl;

        $this->cacheTypeList = $typeList;
        $this->_cacheState = $cacheState;

        $this->resource = $productFactory->create()->getResource();
        $this->connection = $this->resource->getConnection();

    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\AbstractResource|\Magento\Framework\Model\ResourceModel\Db\AbstractDb|null
     */
    public function getResource()
    {

        return $this->resource;
    }

    /**
     * @return false|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    public function getConnection()
    {

        return $this->connection;
    }

    /**
     * @return \Magento\Newsletter\Model\Template\Filter
     */
    public function getTemplateProcessor()
    {

        return $this->templateFilter;
    }

    /**
     * @param bool $area
     *
     * @return array|bool
     */
    public function getIdentifierValueFromCode($area = null)
    {

        $code = $this->cookieManager->getCookie(self::PANDA_COOKIE_NAME);

        if (!$code) {
            return false;
        }

        $row = $this->connection->fetchRow(
            $this->connection->select()
                             ->from($this->resource->getTable('panda_identifiers'))
                             ->where('code=?', $code)
        );

        return $area !== null ? $row[$area] : $row;
    }

    /**
     * @param $area
     * @param $value
     *
     * @return string
     */
    public function addIdentifierValueFromArea($area, $value)
    {

        $table = $this->resource->getTable('panda_identifiers');

        $code = $this->cookieManager->getCookie(self::PANDA_COOKIE_NAME);

        if (!$code) {
            try {
                $code = self::getToken();
                $this->connection->insert($table, ['code' => $code, $area => $value]);

                $metadata = $this->cookieMetadataFactory->setDuration(3600 * 24 * 7)
                                                        ->setPath('/');

                $this->cookieManager->setPublicCookie(self::PANDA_COOKIE_NAME, $code, $metadata);
            } catch (\Exception $e) {
            }
        } else {
            $result = $this->connection->update($table, [$area => $value], ['code=?' => $code]);

            if ($result == 0) {
                $this->connection->insert($table, ['code' => $code, $area => $value]);
            }
        }

        return $code;
    }

    /**
     * @param $code
     * @param $area
     * @param $value
     *
     * @return string
     */
    public function updateIdentifierValue($code, $area, $value)
    {

        if ($this->scopeConfig->isSetFlag('panda_nuntius/info/enabled')) {
            $table = $this->resource->getTable('panda_identifiers');

            return $this->connection->update($table, [$area => $value], ['code=?' => $code]);
        }

        return false;
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public static function getToken($length = 50)
    {

        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet);

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }

    /**
     * @return \Magento\Framework\Registry
     */
    public function getRegistry()
    {

        return $this->registry;
    }

    /**
     * @param null $email
     *
     * @return bool|\Magento\Framework\DataObject
     */
    public function getSubscriberQuote($email = null)
    {

        if ($email === null) {
            /** @var \Licentia\Panda\Model\Subscribers $subscriber */
            $subscriber = $this->registry->registry('panda_subscriber');

            if (!$subscriber || !$subscriber->getId()) {
                return false;
            }

            $email = $subscriber->getEmail();
        }

        $collection = $this->quoteCollection->create();
        $collection->addFieldToFilter('main_table.items_count', ['neq' => '0'])
                   ->addFieldToFilter('main_table.is_active', '1');

        $collection->getSelect()
                   ->joinLeft(
                       ['a' => $this->resource->getTable('quote_address')],
                       'main_table.entity_id=a.quote_id AND a.address_type="billing"',
                       []
                   )
                   ->where(' main_table.customer_email=? OR a.email=?', $email)
                   ->order('main_table.updated_at DESC');

        if ($collection->count() == 0) {
            return false; //No products
        }

        return $collection->getFirstItem();
    }

    /**
     * @param \Licentia\Panda\Model\Campaigns   $campaign
     * @param \Licentia\Panda\Model\Subscribers $subscriber
     *
     * @return bool|mixed|string
     * @throws \Exception
     */
    public function getContentFromUrl(
        \Licentia\Panda\Model\Campaigns $campaign,
        \Licentia\Panda\Model\Subscribers $subscriber
    ) {

        if ($campaign->getUrl()) {
            if (stripos($campaign->getUrl(), '{') !== false) {
                $url = str_replace(
                    ['{campaignId}', '{subscriberId}'],
                    [$campaign->getId(), $subscriber->getId()],
                    $campaign->getUrl()
                );

                $this->curl->get($url);
                $message = $this->curl->getBody();

                if ($message === false) {
                    throw new \Exception('Cannot fetch URL');
                }
            } else {
                if ($this->registry->registry('panda_campaign_url_' . $campaign->getId())) {
                    $message = $this->registry->registry('panda_campaign_url_' . $campaign->getId());
                } else {
                    $this->curl->get($campaign->getUrl());
                    $message = $this->curl->getBody();

                    $this->registry->register('panda_campaign_url_' . $campaign->getId(), $message);
                    if ($message === false) {
                        throw new \Exception('Could not fetch URL');
                    }
                }
            }
        } else {
            $message = false;
        }

        return $message;
    }

    /**
     * @return null|string
     */
    public function getCustomerEmail()
    {

        if ($this->customerEmail !== null) {
            return $this->customerEmail;
        }

        try {
            if ($subscriber = $this->getSubscriber()) {
                if ($email = $subscriber->getEmail()) {
                    $this->customerEmail = $email;

                    return $this->customerEmail;
                }
            }

            if ($this->getCustomerId()) {
                $this->customerEmail = $this->customerRepository->getById($this->getCustomerId())
                                                                ->getEmail();

                return $this->customerEmail;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {

        if ($this->customerId !== null) {
            return $this->customerId;
        }

        $customerId = $this->httpContext->getValue(\Licentia\Equity\Model\Customer\Context::CONTEXT_CUSTOMER_ID);
        if ($customerId > 0) {
            $this->customerId = $customerId / 12;
        } elseif (is_numeric($this->registry->registry('current_customer'))) {
            $this->customerId = $this->registry->registry('current_customer');
        } elseif ($this->registry->registry('current_customer') &&
                  $this->registry->registry('current_customer')->getId()) {
            $this->customerId = $this->registry->registry('current_customer')->getId();
        } elseif ($this->customerSession && $this->customerSession->getCustomer()) {
            $this->customerId = $this->customerSession->getCustomer()->getId();
        } elseif ($this->persistentHelper &&
                  $this->persistentHelper->getSession() &&
                  $this->persistentHelper->getSession()->getCustomerId()
        ) {
            $this->customerId = $this->persistentHelper->getSession()->getCustomerId();
        } elseif ($this->design->getArea() == 'adminhtml' &&
                  $this->quoteSession->getCustomer() &&
                  $this->quoteSession->getCustomer()->getId() > 0
        ) {
            $this->customerId = $this->quoteSession->getCustomer()->getId();
        }

        return $this->customerId;
    }

    /**
     * @return \Licentia\Panda\Model\Subscribers
     */
    public function getSubscriber()
    {

        if ($this->subscriberCache !== null && $this->subscriberCache->getId()) {
            return $this->subscriberCache;
        }

        $subscriber = $this->subscribersFactory->create();

        if ($this->registry->registry('panda_subscriber') &&
            $this->registry->registry('panda_subscriber')->getId()
        ) {
            $subscriber = $this->registry->registry('panda_subscriber');
        }

        if (!$subscriber->getId() && $this->getCustomerId()) {
            $subscriber = $subscriber->loadByCustomerId($this->getCustomerId());
        }

        if (!$subscriber->getId() && $this->pandaSession->getPandaSubscriber()) {
            $subscriber = $subscriber->loadById($this->pandaSession->getPandaSubscriber());
        }

        if (!$subscriber->getId() && $subCode = $this->getIdentifierValueFromCode('subscriber_code')) {
            $subscriber = $this->subscribersFactory->create()->loadByCode($subCode);
        }

        $this->subscriberCache = $subscriber;

        return $this->subscriberCache;
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer()
    {

        if (!$this->getCustomerId()) {
            return null;
        }

        return $this->customerRepository->getById($this->getCustomerId());
    }

    /**
     * @return int
     */
    public function getCustomerGroupId()
    {

        return $this->customerSession->getCustomerGroupId();
    }

    /**
     * @return array
     */
    public function getCmsPages()
    {

        $collection = $this->pageFactory->create()
                                        ->getCollection()->setOrder('title', 'asc');

        $res = [];
        /** @var \Magento\Cms\Model\Page $item */
        foreach ($collection as $item) {
            $res[$item->getId()] = $item->getTitle() . ' [ID: ' . $item->getId() . ']';
        }

        return $res;
    }

    /**
     * @return array
     */
    public function getCmsBlocks()
    {

        $collection = $this->blockFactory->create()
                                         ->getCollection()->setOrder('title', 'asc');

        $res = [];
        /** @var \Magento\Cms\Model\Block $item */
        foreach ($collection as $item) {
            $res[$item->getId()] = $item->getTitle() . ' [ID: ' . $item->getId() . ']';
        }

        return $res;
    }

    /**
     * @return array
     */
    public function getCategories()
    {

        $parent = \Magento\Catalog\Model\Category::TREE_ROOT_ID;
        $return = [];

        $categories = $this->categoryFactory->create()->getCategories($parent);

        $t = [];
        $categories = $this->listCategoriesTree($categories, $t, $i);

        foreach ($categories as $key => $category) :
            $return[] = ['label' => $category, 'value' => $key];
        endforeach;

        return $return;
    }

    /**
     * @param     $categories
     * @param     $array
     * @param int $i
     *
     * @return mixed
     */
    public function listCategoriesTree($categories, &$array, &$i = 0)
    {

        /** @var \Magento\Catalog\Model\Category $category */
        foreach ($categories as $category) {
            $i = count(explode('/', $category->getData('path'))) - 1;

            $array [$category->getId()] = str_repeat('|---', $i) . $category->getName();
            if ($category->hasChildren()) {
                $children = $this->categoryFactory->create()->getCategories($category->getId());
                $this->listCategoriesTree($children, $array, $i);
            }
        }

        return $array;
    }

    /**
     * @param      $code
     * @param null $date
     *
     * @return bool
     * @throws \Exception
     */
    public function scheduleEvent($code, $date = null)
    {

        if (null === $date) {
            $date = $this->dateTime->gmtDate();
        }

        /** @var \Magento\Cron\Model\Schedule $cron */
        $cron = $this->scheduleFactory->create()->load($code, 'job_code');

        if ($cron->getId() && $cron->getStatus() == 'pending') {
            return false;
        } else {
            $data['status'] = 'pending';
            $data['job_code'] = $code;
            $data['scheduled_at'] = $date;
            $data['created_at'] = $date;

            $cron->setData($data)->save();
        }

        return true;
    }

    /**
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @param bool                                  $encode
     *
     * @return string
     */
    public function encrypt(\Magento\Customer\Model\Data\Customer $customer, $encode = false)
    {

        //hash
        $id = $customer->getId();
        $email = $customer->getEmail();
        $encrypted = $this->encryptor->encrypt($id . ';' . $email);
        if ($encode) {
            $encrypted = $this->urlEncoder->encode($encrypted);
        }

        return $encrypted;
    }

    /**
     * @param      $d
     * @param bool $decode
     *
     * @return mixed
     */
    public function decrypt($d, $decode = false)
    {

        if ($decode) {
            $d = $this->urlDecoder->decode($d);
        }
        $dec = $this->encryptor->decrypt($d);

        return $dec;
    }

    /**
     * @return \Magento\Framework\Encryption\EncryptorInterface
     */
    public function getEncryptor()
    {

        return $this->encryptor;
    }

    /**
     * @param $start
     * @param $end
     *
     * @return bool|\DateInterval
     */
    public function getDaysBetweenDates($start, $end)
    {

        $dateStart = new \DateTime($start);
        $dateEnd = new \DateTime($end);

        return $dateStart->diff($dateEnd)->days;
    }

    /**
     * @param $segmentId
     *
     * @return bool
     */
    public function isCustomerInSegment($segmentId)
    {

        $customerId = $this->getCustomerId();

        if (!$customerId) {
            return false;
        }

        $seg = $this->listSegmentsCollection->create()
                                            ->addFieldToFilter('customer_id', $customerId)
                                            ->addFieldToFilter('segment_id', $segmentId)
                                            ->setPageSize(1);

        if ($seg->count() != 1) {
            return false;
        }

        return $segmentId;
    }

    /**
     * @param bool $customerId
     *
     * @return bool|int
     */
    public function getCustomerSegmentId($customerId = false)
    {

        if ($customerId === false) {
            $customerId = $this->getCustomerId();
        }

        if (!$customerId) {
            return false;
        }

        if ($this->customerSession && $this->customerSession->getSegmentId()) {
            return (int) $this->customerSession->getSegmentId();
        }

        $seg = $this->listSegmentsCollection->create()
                                            ->addFieldToFilter('customer_id', $customerId)
                                            ->setOrder('priority', 'ASC')
                                            ->setOrder('segment_id', 'ASC')
                                            ->setPageSize(1);

        if ($seg->count() != 1) {
            return false;
        }

        $segmentId = $seg->getFirstItem()->getSegmentId();

        if ($this->customerSession) {
            $this->customerSession->setSegmentId($segmentId);
        }

        return $segmentId;
    }

    /**
     * @param bool $customerId
     * @param bool $forceReload
     *
     * @return array
     */
    public function getCustomerSegmentsIds($customerId = false, $forceReload = false)
    {

        if ($customerId === false) {
            $customerId = $this->getCustomerId();
        }

        if (!$customerId) {
            return [];
        }
        $segmentsIds = [];

        if ($this->customerSession || $forceReload) {

            if ($this->customerSession->getSegmentsIds()) {
                return (array) $this->customerSession->getSegmentsIds();
            }

            $segmentsIds = $this->connection->fetchCol(
                $this->connection->select()
                                 ->from($this->resource->getTable('panda_segments_records'), ['segment_id'])
                                 ->where('email=? OR customer_id=' . (int) $customerId,
                                     $this->customerSession->getCustomer()->getEmail())
                                 ->order('segment_id ASC')
            );

            $this->customerSession->setSegmentsIds($segmentsIds);
        }

        return $segmentsIds;
    }

    /**
     * Returns Product price for segment
     *
     * @param $customerId
     * @param $product
     * @param $previousPrice
     *
     * @return bool
     * @internal param $segmentId
     */
    public function getSegmentPrice($customerId, $product, $previousPrice)
    {

        $customerSegmentsIds = $this->getCustomerSegmentsIds($customerId);

        if (!$customerSegmentsIds) {
            return $previousPrice;
        }

        $websiteId = $this->storeManager->getWebsite()->getId();

        $model = $this->pricesCollection->create()
                                        ->addFieldToFilter('product_id', $product->getId())
                                        ->addFieldToFilter('segment_id', ['in' => $customerSegmentsIds])
                                        ->setOrder('price', 'ASC')
                                        ->setPageSize(1);

        $model->getSelect()->where('website_id=0 OR website_id=?', $websiteId);

        if ($model->count() != 1) {
            return $previousPrice;
        }

        $price = $model->getFirstItem()->getPrice();

        if ($this->scopeConfig->isSetFlag('panda_prices/segments/percentage')) {
            $price = $previousPrice * $price / 100;
        }

        return min($previousPrice, $price);
    }

    /**
     * @param $customerId
     * @param $product
     * @param $previousPrice
     *
     * @return string
     */
    public function getCustomerPrice($customerId, $product, $previousPrice)
    {

        $productPrice = $this->connection->fetchOne(
            $this->connection->select()
                             ->from($this->resource->getTable('panda_customer_prices'), ['price'])
                             ->where('customer_id=?', $customerId)
                             ->where('product_id=?', $product->getId())
        );

        if (!$productPrice || $productPrice <= 0 || $productPrice > $previousPrice) {
            return $previousPrice;
        }

        return $productPrice;

    }

    /**
     * @return array
     */
    public function getAttributes()
    {

        $return = [];
        $productAttrs = $this->attributeCollection->create();

        foreach ($productAttrs as $attribute) {
            if ($attribute->getData('frontend_input') == 'select' ||
                $attribute->getData('frontend_input') == 'multiselect'
            ) {
                $options = $this->eavAttributeCollection->create();
                $values = $options->setAttributeFilter($attribute->getId())
                                  ->setStoreFilter(0)
                                  ->toOptionArray();

                foreach ($values as $option) {
                    if (empty($option['label'])) {
                        continue;
                    }
                    $return[] = [
                        'label' => $attribute->getFrontendLabel() . ' / ' . $option['label'],
                        'value' => $attribute->getId() . '-' . $option['value'],
                    ];
                }
            }
        }

        return $return;
    }

    /**
     * @return array
     */
    public static function getPhonePrefixes()
    {

        $phones = self::phonePrefixesList();

        $return = [];
        $return[''] = __('-- Please Choose --');
        foreach ($phones as $code => $value) {
            $return[$value[2]] = ucwords(strtolower($code)) . ' (+' . $value['code'] . ')';
        }

        asort($return);

        return $return;
    }

    /**
     * @param $countryCode
     *
     * @return string
     */
    public static function getPrefixForCountry($countryCode)
    {

        $phones = self::phonePrefixesList();

        if (strlen($countryCode) == 2) {
            return $phones[$countryCode]['code'];
        }

        foreach ($phones as $code => $phone) {
            if ($code == $countryCode) {
                return $phone['code'];
            }
        }

        return '';
    }

    /**
     * @return array
     */
    public static function getCountries()
    {

        $return = [];
        $phones = self::phonePrefixesList();
        foreach ($phones as $code => $data) {
            $return[$code] = $data['name'];
        }

        asort($return);

        return $return;
    }

    /**
     * @return array
     */
    public static function phonePrefixesList()
    {

        return [
            'AD' => ['name' => 'ANDORRA', 'code' => '376'],
            'AE' => ['name' => 'UNITED ARAB EMIRATES', 'code' => '971'],
            'AF' => ['name' => 'AFGHANISTAN', 'code' => '93'],
            'AG' => ['name' => 'ANTIGUA AND BARBUDA', 'code' => '1268'],
            'AI' => ['name' => 'ANGUILLA', 'code' => '1264'],
            'AL' => ['name' => 'ALBANIA', 'code' => '355'],
            'AM' => ['name' => 'ARMENIA', 'code' => '374'],
            'AN' => ['name' => 'NETHERLANDS ANTILLES', 'code' => '599'],
            'AO' => ['name' => 'ANGOLA', 'code' => '244'],
            'AQ' => ['name' => 'ANTARCTICA', 'code' => '672'],
            'AR' => ['name' => 'ARGENTINA', 'code' => '54'],
            'AS' => ['name' => 'AMERICAN SAMOA', 'code' => '1684'],
            'AT' => ['name' => 'AUSTRIA', 'code' => '43'],
            'AU' => ['name' => 'AUSTRALIA', 'code' => '61'],
            'AW' => ['name' => 'ARUBA', 'code' => '297'],
            'AZ' => ['name' => 'AZERBAIJAN', 'code' => '994'],
            'BA' => ['name' => 'BOSNIA AND HERZEGOVINA', 'code' => '387'],
            'BB' => ['name' => 'BARBADOS', 'code' => '1246'],
            'BD' => ['name' => 'BANGLADESH', 'code' => '880'],
            'BE' => ['name' => 'BELGIUM', 'code' => '32'],
            'BF' => ['name' => 'BURKINA FASO', 'code' => '226'],
            'BG' => ['name' => 'BULGARIA', 'code' => '359'],
            'BH' => ['name' => 'BAHRAIN', 'code' => '973'],
            'BI' => ['name' => 'BURUNDI', 'code' => '257'],
            'BJ' => ['name' => 'BENIN', 'code' => '229'],
            'BL' => ['name' => 'SAINT BARTHELEMY', 'code' => '590'],
            'BM' => ['name' => 'BERMUDA', 'code' => '1441'],
            'BN' => ['name' => 'BRUNEI DARUSSALAM', 'code' => '673'],
            'BO' => ['name' => 'BOLIVIA', 'code' => '591'],
            'BR' => ['name' => 'BRAZIL', 'code' => '55'],
            'BS' => ['name' => 'BAHAMAS', 'code' => '1242'],
            'BT' => ['name' => 'BHUTAN', 'code' => '975'],
            'BW' => ['name' => 'BOTSWANA', 'code' => '267'],
            'BY' => ['name' => 'BELARUS', 'code' => '375'],
            'BZ' => ['name' => 'BELIZE', 'code' => '501'],
            'CA' => ['name' => 'CANADA', 'code' => '1'],
            'CC' => ['name' => 'COCOS (KEELING) ISLANDS', 'code' => '61'],
            'CD' => ['name' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'code' => '243'],
            'CF' => ['name' => 'CENTRAL AFRICAN REPUBLIC', 'code' => '236'],
            'CG' => ['name' => 'CONGO', 'code' => '242'],
            'CH' => ['name' => 'SWITZERLAND', 'code' => '41'],
            'CI' => ['name' => 'COTE D IVOIRE', 'code' => '225'],
            'CK' => ['name' => 'COOK ISLANDS', 'code' => '682'],
            'CL' => ['name' => 'CHILE', 'code' => '56'],
            'CM' => ['name' => 'CAMEROON', 'code' => '237'],
            'CN' => ['name' => 'CHINA', 'code' => '86'],
            'CO' => ['name' => 'COLOMBIA', 'code' => '57'],
            'CR' => ['name' => 'COSTA RICA', 'code' => '506'],
            'CU' => ['name' => 'CUBA', 'code' => '53'],
            'CV' => ['name' => 'CAPE VERDE', 'code' => '238'],
            'CX' => ['name' => 'CHRISTMAS ISLAND', 'code' => '61'],
            'CY' => ['name' => 'CYPRUS', 'code' => '357'],
            'CZ' => ['name' => 'CZECH REPUBLIC', 'code' => '420'],
            'DE' => ['name' => 'GERMANY', 'code' => '49'],
            'DJ' => ['name' => 'DJIBOUTI', 'code' => '253'],
            'DK' => ['name' => 'DENMARK', 'code' => '45'],
            'DM' => ['name' => 'DOMINICA', 'code' => '1767'],
            'DO' => ['name' => 'DOMINICAN REPUBLIC', 'code' => '1809'],
            'DZ' => ['name' => 'ALGERIA', 'code' => '213'],
            'EC' => ['name' => 'ECUADOR', 'code' => '593'],
            'EE' => ['name' => 'ESTONIA', 'code' => '372'],
            'EG' => ['name' => 'EGYPT', 'code' => '20'],
            'ER' => ['name' => 'ERITREA', 'code' => '291'],
            'ES' => ['name' => 'SPAIN', 'code' => '34'],
            'ET' => ['name' => 'ETHIOPIA', 'code' => '251'],
            'FI' => ['name' => 'FINLAND', 'code' => '358'],
            'FJ' => ['name' => 'FIJI', 'code' => '679'],
            'FK' => ['name' => 'FALKLAND ISLANDS (MALVINAS)', 'code' => '500'],
            'FM' => ['name' => 'MICRONESIA, FEDERATED STATES OF', 'code' => '691'],
            'FO' => ['name' => 'FAROE ISLANDS', 'code' => '298'],
            'FR' => ['name' => 'FRANCE', 'code' => '33'],
            'GA' => ['name' => 'GABON', 'code' => '241'],
            'GB' => ['name' => 'UNITED KINGDOM', 'code' => '44'],
            'GD' => ['name' => 'GRENADA', 'code' => '1473'],
            'GE' => ['name' => 'GEORGIA', 'code' => '995'],
            'GH' => ['name' => 'GHANA', 'code' => '233'],
            'GI' => ['name' => 'GIBRALTAR', 'code' => '350'],
            'GL' => ['name' => 'GREENLAND', 'code' => '299'],
            'GM' => ['name' => 'GAMBIA', 'code' => '220'],
            'GN' => ['name' => 'GUINEA', 'code' => '224'],
            'GQ' => ['name' => 'EQUATORIAL GUINEA', 'code' => '240'],
            'GR' => ['name' => 'GREECE', 'code' => '30'],
            'GT' => ['name' => 'GUATEMALA', 'code' => '502'],
            'GU' => ['name' => 'GUAM', 'code' => '1671'],
            'GW' => ['name' => 'GUINEA-BISSAU', 'code' => '245'],
            'GY' => ['name' => 'GUYANA', 'code' => '592'],
            'HK' => ['name' => 'HONG KONG', 'code' => '852'],
            'HN' => ['name' => 'HONDURAS', 'code' => '504'],
            'HR' => ['name' => 'CROATIA', 'code' => '385'],
            'HT' => ['name' => 'HAITI', 'code' => '509'],
            'HU' => ['name' => 'HUNGARY', 'code' => '36'],
            'ID' => ['name' => 'INDONESIA', 'code' => '62'],
            'IE' => ['name' => 'IRELAND', 'code' => '353'],
            'IL' => ['name' => 'ISRAEL', 'code' => '972'],
            'IM' => ['name' => 'ISLE OF MAN', 'code' => '44'],
            'IN' => ['name' => 'INDIA', 'code' => '91'],
            'IQ' => ['name' => 'IRAQ', 'code' => '964'],
            'IR' => ['name' => 'IRAN, ISLAMIC REPUBLIC OF', 'code' => '98'],
            'IS' => ['name' => 'ICELAND', 'code' => '354'],
            'IT' => ['name' => 'ITALY', 'code' => '39'],
            'JM' => ['name' => 'JAMAICA', 'code' => '1876'],
            'JO' => ['name' => 'JORDAN', 'code' => '962'],
            'JP' => ['name' => 'JAPAN', 'code' => '81'],
            'KE' => ['name' => 'KENYA', 'code' => '254'],
            'KG' => ['name' => 'KYRGYZSTAN', 'code' => '996'],
            'KH' => ['name' => 'CAMBODIA', 'code' => '855'],
            'KI' => ['name' => 'KIRIBATI', 'code' => '686'],
            'KM' => ['name' => 'COMOROS', 'code' => '269'],
            'KN' => ['name' => 'SAINT KITTS AND NEVIS', 'code' => '1869'],
            'KP' => ['name' => 'KOREA DEMOCRATIC PEOPLES REPUBLIC OF', 'code' => '850'],
            'KR' => ['name' => 'KOREA REPUBLIC OF', 'code' => '82'],
            'KW' => ['name' => 'KUWAIT', 'code' => '965'],
            'KY' => ['name' => 'CAYMAN ISLANDS', 'code' => '1345'],
            'KZ' => ['name' => 'KAZAKSTAN', 'code' => '7'],
            'LA' => ['name' => 'LAO PEOPLES DEMOCRATIC REPUBLIC', 'code' => '856'],
            'LB' => ['name' => 'LEBANON', 'code' => '961'],
            'LC' => ['name' => 'SAINT LUCIA', 'code' => '1758'],
            'LI' => ['name' => 'LIECHTENSTEIN', 'code' => '423'],
            'LK' => ['name' => 'SRI LANKA', 'code' => '94'],
            'LR' => ['name' => 'LIBERIA', 'code' => '231'],
            'LS' => ['name' => 'LESOTHO', 'code' => '266'],
            'LT' => ['name' => 'LITHUANIA', 'code' => '370'],
            'LU' => ['name' => 'LUXEMBOURG', 'code' => '352'],
            'LV' => ['name' => 'LATVIA', 'code' => '371'],
            'LY' => ['name' => 'LIBYAN ARAB JAMAHIRIYA', 'code' => '218'],
            'MA' => ['name' => 'MOROCCO', 'code' => '212'],
            'MC' => ['name' => 'MONACO', 'code' => '377'],
            'MD' => ['name' => 'MOLDOVA, REPUBLIC OF', 'code' => '373'],
            'ME' => ['name' => 'MONTENEGRO', 'code' => '382'],
            'MF' => ['name' => 'SAINT MARTIN', 'code' => '1599'],
            'MG' => ['name' => 'MADAGASCAR', 'code' => '261'],
            'MH' => ['name' => 'MARSHALL ISLANDS', 'code' => '692'],
            'MK' => ['name' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'code' => '389'],
            'ML' => ['name' => 'MALI', 'code' => '223'],
            'MM' => ['name' => 'MYANMAR', 'code' => '95'],
            'MN' => ['name' => 'MONGOLIA', 'code' => '976'],
            'MO' => ['name' => 'MACAU', 'code' => '853'],
            'MP' => ['name' => 'NORTHERN MARIANA ISLANDS', 'code' => '1670'],
            'MR' => ['name' => 'MAURITANIA', 'code' => '222'],
            'MS' => ['name' => 'MONTSERRAT', 'code' => '1664'],
            'MT' => ['name' => 'MALTA', 'code' => '356'],
            'MU' => ['name' => 'MAURITIUS', 'code' => '230'],
            'MV' => ['name' => 'MALDIVES', 'code' => '960'],
            'MW' => ['name' => 'MALAWI', 'code' => '265'],
            'MX' => ['name' => 'MEXICO', 'code' => '52'],
            'MY' => ['name' => 'MALAYSIA', 'code' => '60'],
            'MZ' => ['name' => 'MOZAMBIQUE', 'code' => '258'],
            'NA' => ['name' => 'NAMIBIA', 'code' => '264'],
            'NC' => ['name' => 'NEW CALEDONIA', 'code' => '687'],
            'NE' => ['name' => 'NIGER', 'code' => '227'],
            'NG' => ['name' => 'NIGERIA', 'code' => '234'],
            'NI' => ['name' => 'NICARAGUA', 'code' => '505'],
            'NL' => ['name' => 'NETHERLANDS', 'code' => '31'],
            'NO' => ['name' => 'NORWAY', 'code' => '47'],
            'NP' => ['name' => 'NEPAL', 'code' => '977'],
            'NR' => ['name' => 'NAURU', 'code' => '674'],
            'NU' => ['name' => 'NIUE', 'code' => '683'],
            'NZ' => ['name' => 'NEW ZEALAND', 'code' => '64'],
            'OM' => ['name' => 'OMAN', 'code' => '968'],
            'PA' => ['name' => 'PANAMA', 'code' => '507'],
            'PE' => ['name' => 'PERU', 'code' => '51'],
            'PF' => ['name' => 'FRENCH POLYNESIA', 'code' => '689'],
            'PG' => ['name' => 'PAPUA NEW GUINEA', 'code' => '675'],
            'PH' => ['name' => 'PHILIPPINES', 'code' => '63'],
            'PK' => ['name' => 'PAKISTAN', 'code' => '92'],
            'PL' => ['name' => 'POLAND', 'code' => '48'],
            'PM' => ['name' => 'SAINT PIERRE AND MIQUELON', 'code' => '508'],
            'PN' => ['name' => 'PITCAIRN', 'code' => '870'],
            'PR' => ['name' => 'PUERTO RICO', 'code' => '1'],
            'PT' => ['name' => 'PORTUGAL', 'code' => '351'],
            'PW' => ['name' => 'PALAU', 'code' => '680'],
            'PY' => ['name' => 'PARAGUAY', 'code' => '595'],
            'QA' => ['name' => 'QATAR', 'code' => '974'],
            'RO' => ['name' => 'ROMANIA', 'code' => '40'],
            'RS' => ['name' => 'SERBIA', 'code' => '381'],
            'RU' => ['name' => 'RUSSIAN FEDERATION', 'code' => '7'],
            'RW' => ['name' => 'RWANDA', 'code' => '250'],
            'SA' => ['name' => 'SAUDI ARABIA', 'code' => '966'],
            'SB' => ['name' => 'SOLOMON ISLANDS', 'code' => '677'],
            'SC' => ['name' => 'SEYCHELLES', 'code' => '248'],
            'SD' => ['name' => 'SUDAN', 'code' => '249'],
            'SE' => ['name' => 'SWEDEN', 'code' => '46'],
            'SG' => ['name' => 'SINGAPORE', 'code' => '65'],
            'SH' => ['name' => 'SAINT HELENA', 'code' => '290'],
            'SI' => ['name' => 'SLOVENIA', 'code' => '386'],
            'SK' => ['name' => 'SLOVAKIA', 'code' => '421'],
            'SL' => ['name' => 'SIERRA LEONE', 'code' => '232'],
            'SM' => ['name' => 'SAN MARINO', 'code' => '378'],
            'SN' => ['name' => 'SENEGAL', 'code' => '221'],
            'SO' => ['name' => 'SOMALIA', 'code' => '252'],
            'SR' => ['name' => 'SURINAME', 'code' => '597'],
            'ST' => ['name' => 'SAO TOME AND PRINCIPE', 'code' => '239'],
            'SV' => ['name' => 'EL SALVADOR', 'code' => '503'],
            'SY' => ['name' => 'SYRIAN ARAB REPUBLIC', 'code' => '963'],
            'SZ' => ['name' => 'SWAZILAND', 'code' => '268'],
            'TC' => ['name' => 'TURKS AND CAICOS ISLANDS', 'code' => '1649'],
            'TD' => ['name' => 'CHAD', 'code' => '235'],
            'TG' => ['name' => 'TOGO', 'code' => '228'],
            'TH' => ['name' => 'THAILAND', 'code' => '66'],
            'TJ' => ['name' => 'TAJIKISTAN', 'code' => '992'],
            'TK' => ['name' => 'TOKELAU', 'code' => '690'],
            'TL' => ['name' => 'TIMOR-LESTE', 'code' => '670'],
            'TM' => ['name' => 'TURKMENISTAN', 'code' => '993'],
            'TN' => ['name' => 'TUNISIA', 'code' => '216'],
            'TO' => ['name' => 'TONGA', 'code' => '676'],
            'TR' => ['name' => 'TURKEY', 'code' => '90'],
            'TT' => ['name' => 'TRINIDAD AND TOBAGO', 'code' => '1868'],
            'TV' => ['name' => 'TUVALU', 'code' => '688'],
            'TW' => ['name' => 'TAIWAN, PROVINCE OF CHINA', 'code' => '886'],
            'TZ' => ['name' => 'TANZANIA, UNITED REPUBLIC OF', 'code' => '255'],
            'UA' => ['name' => 'UKRAINE', 'code' => '380'],
            'UG' => ['name' => 'UGANDA', 'code' => '256'],
            'US' => ['name' => 'UNITED STATES', 'code' => '1'],
            'UY' => ['name' => 'URUGUAY', 'code' => '598'],
            'UZ' => ['name' => 'UZBEKISTAN', 'code' => '998'],
            'VA' => ['name' => 'HOLY SEE (VATICAN CITY STATE)', 'code' => '39'],
            'VC' => ['name' => 'SAINT VINCENT AND THE GRENADINES', 'code' => '1784'],
            'VE' => ['name' => 'VENEZUELA', 'code' => '58'],
            'VG' => ['name' => 'VIRGIN ISLANDS, BRITISH', 'code' => '1284'],
            'VI' => ['name' => 'VIRGIN ISLANDS, U.S.', 'code' => '1340'],
            'VN' => ['name' => 'VIET NAM', 'code' => '84'],
            'VU' => ['name' => 'VANUATU', 'code' => '678'],
            'WF' => ['name' => 'WALLIS AND FUTUNA', 'code' => '681'],
            'WS' => ['name' => 'SAMOA', 'code' => '685'],
            'XK' => ['name' => 'KOSOVO', 'code' => '381'],
            'YE' => ['name' => 'YEMEN', 'code' => '967'],
            'YT' => ['name' => 'MAYOTTE', 'code' => '262'],
            'ZA' => ['name' => 'SOUTH AFRICA', 'code' => '27'],
            'ZM' => ['name' => 'ZAMBIA', 'code' => '260'],
            'ZW' => ['name' => 'ZIMBABWE', 'code' => '263'],
        ];
    }

    /**
     * @return string
     */
    public function getPlatform()
    {

        $useragent = $this->_getRequest()->getServer('HTTP_USER_AGENT');

        $mobile = preg_match(
            '/(android|bb\d+|meego).+mobile|bada\/|blackberry|ip(hone|od|ad)|opera m(ob|in)i|phone|android|p(ixi|re)' .
            '\/|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',
            $useragent
        );

        return $mobile ? 'mobile' : 'desktop';
    }

    /**
     * @param null $ip
     *
     * @return mixed|string
     */
    public function getCountryName($ip = null)
    {

        $country = 'Unknown';
        $info = $this->getCountryInformation($ip);
        if (isset($info['country'])) {
            $country = $info['country'];
        }

        return $country;
    }

    /**
     * @param null $ip
     *
     * @return array|mixed
     */
    public function getCountryInformation($ip = null)
    {

        if (!$this->scopeConfig->isSetFlag('panda_general/geo/enabled')) {
            return [];
        }

        try {
            if ($ip === null && isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            } elseif ($ip === null) {
                return [];
            }

            if ($geo = $this->getIdentifierValueFromCode('geo')) {
                $cookie = \Zend_Json_Decoder::decode($geo);
                if (is_array($cookie) && isset($cookie['countryCode'])) {
                    return $cookie;
                }
            }
            if ($this->customerSession) {
                $session = $this->customerSession->getData('panda_ip');

                if ($session) {
                    $session = \Zend_Json_Decoder::decode($session);
                    if (is_array($session) && isset($session['countryCode'])) {
                        return $session;
                    }
                }
            }

            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                try {
                    $accessKey = $this->scopeConfig->getValue('panda_general/geo/apikey');
                    $url = self::GEO_LOCATION_URL_LOCATION . $ip . "?key=" . $accessKey;

                    $this->curl->get($url);
                    $remote = $this->curl->getBody();
                    $panda = json_decode($remote, true);

                    if (!is_array($panda) || !isset($panda['countryCode'])) {
                        return [];
                    }

                    if ($this->customerSession) {
                        $this->customerSession->setData('panda_ip', $remote);
                    }
                } catch (\Exception $e) {
                    $this->logWarning($e);

                    return [];
                }
            } else {
                return [];
            }

            $this->addIdentifierValueFromArea('geo', $remote);

            $return = \Zend_Json_Decoder::decode($remote);
        } catch (\Exception $e) {
            return [];
        }

        return $return;
    }

    /**
     * @param null $ip
     *
     * @return mixed|string
     */
    public function getCountryCode($ip = null)
    {

        $country = '';
        $info = $this->getCountryInformation($ip);
        if (isset($info['countryCode'])) {
            $country = $info['countryCode'];
        }

        return $country;
    }

    /**
     * @param null $ip
     *
     * @return mixed|string
     */
    public function getCountryRegionCode($ip = null)
    {

        $region = '';
        $info = $this->getCountryInformation($ip);
        if (isset($info['region'])) {
            $region = $info['region'];
        }

        return $region;
    }

    /**
     * @param null $ip
     *
     * @return mixed|string
     */
    public function getCountryRegion($ip = null)
    {

        $region = '';
        $info = $this->getCountryInformation($ip);
        if (isset($info['regionName'])) {
            $region = $info['regionName'];
        }

        return $region;
    }

    /**
     * @param null $ip
     *
     * @return mixed|string
     */
    public function getCountryCity($ip = null)
    {

        $region = '';
        $info = $this->getCountryInformation($ip);
        if (isset($info['city'])) {
            $region = $info['city'];
        }

        return $region;
    }

    /**
     *
     */
    public function registerCurrentScope()
    {

        if (!$uri = $this->_request->getParam('identifier')) {
            return;
        }

        $uri = ltrim($uri, '/');
        $type = false;

        if (stripos($uri, '.html') === false && stripos($uri, '/') === false && stripos($uri, ' ') === false) {
            $select = $this->connection->select()
                                       ->from($this->resource->getTable('catalog_product_entity'))
                                       ->where('sku=?', $uri)
                                       ->limit(1);

            $row = $this->connection->fetchRow($select);

            if ($row) {
                $type = 'product';
                $id = $row['entity_id'];
            }
        } else {
            $select = $this->connection
                ->select()
                ->from($this->resource->getTable('url_rewrite'))
                ->where('request_path=?', $uri)
                ->where('store_id=?', $this->storeManager->getStore()->getId())
                ->limit(1);

            $row = $this->connection->fetchRow($select);

            if ($row) {
                $type = $row['entity_type'];
                $id = $row['entity_id'];
            }
        }

        if ($type && !empty($id)) {
            if ($type == 'product') {
                $product = $this->productFactory->create()->load($id);

                $this->registry->register('current_product', $product, true);
                $this->registry->register('product', $product, true);
            }
            if ($type == 'category') {
                $category = $this->categoryFactory->create()->load($id);

                $this->registry->register('current_category', $category, true);
                $this->registry->register('category', $category, true);
            }
        }
    }

    /**
     * @return string
     */
    public function getCustomerSessionIdentifier()
    {

        return sha1($this->customerSession->getSessionId());
    }

    /**
     * @param string $cache
     *
     * @return mixed
     */
    public function isCacheEnabled($cache = 'full_page')
    {

        if ($cache == 'full_page' && in_array($this->_getRequest()->getModuleName(), ['cms', 'catalog'])) {
            return false;
        }

        return $this->cacheManager->getStatus()[$cache];
    }

    /**
     * Parses a user agent string into its important parts
     *
     * @param string|null $u_agent User agent string to parse or null. Uses $_SERVER['HTTP_USER_AGENT'] on NULL
     *
     * @return string[] an array with browser, version and platform keys
     * @link   http://donatstudios.com/PHP-Parser-HTTP_USER_AGENT
     *
     * @author Jesse G. Donat <donatj@gmail.com>
     * @link   https://github.com/donatj/PhpUserAgent
     */
    public static function parseUserAgent($u_agent = null)
    {

        if (is_null($u_agent)) {
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $u_agent = $_SERVER['HTTP_USER_AGENT'];
            } else {
                return ['platform' => null, 'browser' => null, 'version' => null];
            }
        }
        $platform = null;
        $browser = null;
        $version = null;
        $empty = ['platform' => $platform, 'browser' => $browser, 'version' => $version];
        if (!$u_agent) {
            return $empty;
        }
        if (preg_match('/\((.*?)\)/im', $u_agent, $parent_matches)) {
            preg_match_all(
                '/(?P<platform>BB\d+;|Android|CrOS|Tizen|iPhone|iPad|iPod|Linux|Macintosh|Windows(\ Phone)?|Silk|' .
                'linux-gnu|BlackBerry|PlayBook|X11|(New\ )?Nintendo\ (WiiU?|3?DS)|Xbox(\ One)?)
				(?:\ [^;]*)?
				(?:;|$)/imx',
                $parent_matches[1],
                $result,
                PREG_PATTERN_ORDER
            );
            $priority = ['Xbox One', 'Xbox', 'Windows Phone', 'Tizen', 'Android', 'CrOS', 'X11'];
            $result['platform'] = array_unique($result['platform']);
            if (count($result['platform']) > 1) {
                if ($keys = array_intersect($priority, $result['platform'])) {
                    $platform = reset($keys);
                } else {
                    $platform = $result['platform'][0];
                }
            } elseif (isset($result['platform'][0])) {
                $platform = $result['platform'][0];
            }
        }
        if ($platform == 'linux-gnu' || $platform == 'X11') {
            $platform = 'Linux';
        } elseif ($platform == 'CrOS') {
            $platform = 'Chrome OS';
        }
        preg_match_all(
            '%(?P<browser>Camino|Kindle(\ Fire)?|Firefox|Iceweasel|IceCat|Safari|MSIE|Trident|AppleWebKit|
				TizenBrowser|Chrome|Vivaldi|IEMobile|Opera|OPR|Silk|Midori|Edge|CriOS|UCBrowser|SamsungBrowser|
				Baiduspider|Googlebot|YandexBot|bingbot|Lynx|Version|Wget|curl|
				Valve\ Steam\ Tenfoot|
				NintendoBrowser|PLAYSTATION\ (\d|Vita)+)
				(?:\)?;?)
				(?:(?:[:/ ])(?P<version>[0-9A-Z.]+)|/(?:[A-Z]*))%ix',
            $u_agent,
            $result,
            PREG_PATTERN_ORDER
        );
        // If nothing matched, return null (to avoid undefined index errors)
        if (!isset($result['browser'][0]) || !isset($result['version'][0])) {
            if (preg_match('%^(?!Mozilla)(?P<browser>[A-Z0-9\-]+)(/(?P<version>[0-9A-Z.]+))?%ix', $u_agent, $result)) {
                return [
                    'platform' => $platform ?: null,
                    'browser'  => $result['browser'],
                    'version'  => isset($result['version']) ? $result['version'] ?: null : null,
                ];
            }

            return $empty;
        }
        if (preg_match('/rv:(?P<version>[0-9A-Z.]+)/si', $u_agent, $rv_result)) {
            $rv_result = $rv_result['version'];
        }
        $browser = $result['browser'][0];
        $version = $result['version'][0];
        $lowerBrowser = array_map('strtolower', $result['browser']);
        $find = function ($search, &$key, &$value = null) use ($lowerBrowser) {

            $search = (array) $search;
            foreach ($search as $val) {
                $xkey = array_search(strtolower($val), $lowerBrowser);
                if ($xkey !== false) {
                    $value = $val;
                    $key = $xkey;

                    return true;
                }
            }

            return false;
        };
        $key = 0;
        $val = '';
        if ($browser == 'Iceweasel' || strtolower($browser) == 'icecat') {
            $browser = 'Firefox';
        } elseif ($find('Playstation Vita', $key)) {
            $platform = 'PlayStation Vita';
            $browser = 'Browser';
        } elseif ($find(['Kindle Fire', 'Silk'], $key, $val)) {
            $browser = $val == 'Silk' ? 'Silk' : 'Kindle';
            $platform = 'Kindle Fire';
            if (!($version = $result['version'][$key]) || !is_numeric($version[0])) {
                $version = $result['version'][array_search('Version', $result['browser'])];
            }
        } elseif ($find('NintendoBrowser', $key) || $platform == 'Nintendo 3DS') {
            $browser = 'NintendoBrowser';
            $version = $result['version'][$key];
        } elseif ($find('Kindle', $key, $platform)) {
            $browser = $result['browser'][$key];
            $version = $result['version'][$key];
        } elseif ($find('OPR', $key)) {
            $browser = 'Opera Next';
            $version = $result['version'][$key];
        } elseif ($find('Opera', $key, $browser)) {
            $find('Version', $key);
            $version = $result['version'][$key];
        } elseif ($find(
            ['IEMobile', 'Edge', 'Midori', 'Vivaldi', 'SamsungBrowser', 'Valve Steam Tenfoot', 'Chrome'],
            $key,
            $browser
        )) {
            $version = $result['version'][$key];
        } elseif ($rv_result && $find('Trident', $key)) {
            $browser = 'MSIE';
            $version = $rv_result;
        } elseif ($find('UCBrowser', $key)) {
            $browser = 'UC Browser';
            $version = $result['version'][$key];
        } elseif ($find('CriOS', $key)) {
            $browser = 'Chrome';
            $version = $result['version'][$key];
        } elseif ($browser == 'AppleWebKit') {
            if ($platform == 'Android' && !($key = 0)) {
                $browser = 'Android Browser';
            } elseif (strpos($platform, 'BB') === 0) {
                $browser = 'BlackBerry Browser';
                $platform = 'BlackBerry';
            } elseif ($platform == 'BlackBerry' || $platform == 'PlayBook') {
                $browser = 'BlackBerry Browser';
            } else {
                $find('Safari', $key, $browser) || $find('TizenBrowser', $key, $browser);
            }
            $find('Version', $key);
            $version = $result['version'][$key];
        } elseif ($pKey = preg_grep('/playstation \d/i', array_map('strtolower', $result['browser']))) {
            $pKey = reset($pKey);
            $platform = 'PlayStation ' . preg_replace('/[^\d]/i', '', $pKey);
            $browser = 'NetFront';
        }

        return ['platform' => $platform ?: null, 'browser' => $browser ?: null, 'version' => $version ?: null];
    }

    /**
     * @param $senderId
     *
     * @return \Licentia\Panda\Model\Senders
     */
    public function getSender($senderId)
    {

        return $this->sendersFactory->create()->load($senderId);

    }

    /**
     * @param $sender
     *
     * @return bool|\Laminas\Mail\Transport\Smtp
     * @throws \Exception
     */
    public function getSmtpTransport($sender)
    {

        if (is_string($sender)) {
            $sender = $this->sendersFactory->create()->load($sender, 'sender_email');
        }

        if (is_numeric($sender)) {
            $sender = $this->sendersFactory->create()->load($sender);
        }

        if (!$sender->getId()) {
            throw new \Exception('No sender available');
        }

        if (strlen(trim($sender->getData('server'))) == 0) {
            return false;
        }

        $server = $sender->getData('server');

        $optionsData = [
            'name'             => 'localhost',
            'host'             => $server,
            'port'             => $sender->getData('port'),
            'connection_class' => $sender->getData('auth'),
        ];

        if ($sender->getData('auth') != 'none') {
            $optionsData['connection_config'] = [
                'username' => $sender->getData('username'),
                'password' => $sender->getData('password'),
            ];
        } else {
            unset($optionsData['auth']);
        }

        if ($sender->getData('ssl') != 'none') {
            $optionsData['connection_config']['ssl'] = $sender->getData('ssl');
        }

        $options = new \Laminas\Mail\Transport\SmtpOptions($optionsData);

        return new \Laminas\Mail\Transport\Smtp($options);
    }

    /**
     * @return bool|\Licentia\Panda\Model\Senders
     */
    public function getEmailSenderForInternalNotifications()
    {

        $senderId = $this->scopeConfig->getValue(
            'panda_nuntius/info/smtp_default_sender',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );

        $senderCol = $this->sendersCollection->create()->addFieldToFilter('type', 'email');

        if ($senderId) {
            $senderCol->addFieldToFilter('sender_id', $senderId);
        }

        $id = $senderCol->getFirstItem()->getId();

        if (!$id) {
            return false;
        }

        return $this->sendersFactory->create()->load($id);

    }

    /**
     * @param \Licentia\Panda\Model\Senders $sender
     *
     * @return bool|\Licentia\Panda\Model\Service\Sms\Core
     */
    public function getSmsTransport($sender)
    {

        try {
            if (is_numeric($sender)) {
                $sender = $this->sendersFactory->create()->load($sender);
            }

            /** @var \Licentia\Panda\Model\Service\Sms\Core $service */
            $service = $this->objectManager->get(
                '\Licentia\Panda\Model\Service\Sms\\' . ucfirst(trim($sender->getGateway()))
            );

            $service->setData($sender->getData());

            return $service;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param null $format
     * @param null $input
     *
     * @return string
     */
    public function gmtDate($format = null, $input = null)
    {

        return $this->dateTime->gmtDate($format, $input);
    }

    /**
     * @param null $input
     *
     * @return string
     */
    public function gmtDateTime($input = null)
    {

        return $this->dateTime->gmtDate('Y-m-d H:i:s', $input);
    }

    /**
     * @param $number
     *
     * @return bool|string
     */
    public function isPhoneNumberValid($number)
    {

        $result = preg_match(
            '/^(999|998|997|996|995|994|993|992|991|990|979|978|977|976|975|974|973|972|971|970|969|968|967|966|965' .
            '|964|963|962|961|960|899|898|897|896|895|894|893|892|891|890|889|888|887|886|885|884|883|882|881|880|879' .
            '|878|877|876|875|874|873|872|871|870|859|858|857|856|855|854|853|852|851|850|839|838|837|836|835|834|833' .
            '|832|831|830|809|808|807|806|805|804|803|802|801|800|699|698|697|696|695|694|693|692|691|690|689|688|687' .
            '|686|685|684|683|682|681|680|679|678|677|676|675|674|673|672|671|670|599|598|597|596|595|594|593|592|591' .
            '|590|509|508|507|506|505|504|503|502|501|500|429|428|427|426|425|424|423|422|421|420|389|388|387|386|385' .
            '|384|383|382|381|380|379|378|377|376|375|374|373|372|371|370|359|358|357|356|355|354|353|352|351|350|299' .
            '|298|297|296|295|294|293|292|291|290|289|288|287|286|285|284|283|282|281|280|269|268|267|266|265|264|263' .
            '|262|261|260|259|258|257|256|255|254|253|252|251|250|249|248|247|246|245|244|243|242|241|240|239|238|237' .
            '|236|235|234|233|232|231|230|229|228|227|226|225|224|223|222|221|220|219|218|217|216|215|214|213|212|211' .
            '|210|98|95|94|93|92|91|90|86|84|82|81|66|65|64|63|62|61|60|58|57|56|55|54|53|52|51|49|48|47|46|45|44|43|' .
            '41|40|39|36|34|33|32|31|30|27|20|7|1)-([0-9 \-()]{0,14})$/',
            $number,
            $matches
        );

        if ($result) {
            return $matches[1] . '-' . preg_replace('/\D/', '', $matches[2]);
        }

        return false;
    }

    /**
     * @param array $array
     *
     * @return bool|string
     */
    public static function arrayToCsv(array $array)
    {

        $csv = fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'r+');

        fputcsv($csv, $array);

        rewind($csv);

        return stream_get_contents($csv);
    }

    /**
     * @return bool
     */
    public function delayAjaxLoads()
    {

        return $this->scopeConfig->isSetFlag('panda_general/load/delay');
    }

    /**
     * @param $table
     *
     * @return string
     */
    public function tableHasRecords($table)
    {

        return $this->connection->fetchOne("SELECT * FROM " . $this->resource->getTable($table) . " LIMIT 1");
    }

    /**
     * @param \Exception $exception
     * @param string     $level
     */
    public function logException(\Exception $exception, $level = 'critical')
    {

        $data = [];
        $data['message'] = $exception->getMessage();
        $data['file'] = $exception->getFile();
        $data['line'] = $exception->getLine();
        $data['trace'] = $exception->getTraceAsString();

        $this->exceptionsFactory->create()->setData($data)->save();
        $this->_logger->$level($exception);
    }

    /**
     * @param \Exception $e
     */
    public function logWarning(\Exception $e)
    {

        $this->logException($e, 'warning');
    }

}
