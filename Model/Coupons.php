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

namespace Licentia\Panda\Model;

/**
 * Class Coupons
 *
 * @package Licentia\Panda\Model
 */
class Coupons extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'panda_coupons';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'coupons';

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory
     */
    protected $coreCouponsCollection;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var ResourceModel\Coupons\CollectionFactory
     */
    protected $couponsCollection;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var SubscribersFactory
     */
    protected $subscribersFactory;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cartSingleton;

    /**
     * @var \Magento\SalesRule\Model\Coupon\MassgeneratorFactory
     */
    protected $massgeneratorFactory;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollection;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    protected $couponFactory;

    /**
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    protected $couponRole = [];

    /**
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface            $timezone
     * @param \Magento\Framework\Model\Context                                $context
     * @param \Magento\Customer\Model\Session                                 $customerSession
     * @param \Magento\Framework\Registry                                     $registry
     * @param \Magento\Customer\Model\CustomerFactory                         $customerFactory
     * @param \Magento\Checkout\Model\Cart                                    $cartSingleton
     * @param \Magento\SalesRule\Model\RuleFactory                            $ruleFactory
     * @param \Magento\SalesRule\Model\CouponFactory                          $couponFactory
     * @param \Magento\SalesRule\Model\Coupon\MassgeneratorFactory            $massgeneratorFactory
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory   $ruleCollection
     * @param \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory $coreCouponsCollection
     * @param ResourceModel\Coupons\CollectionFactory                         $couponsCollection
     * @param Session                                                         $sessionFactory
     * @param \Licentia\Panda\Helper\Data                                     $pandaHelper
     * @param SubscribersFactory                                              $subscribersFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null    $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null              $resourceCollection
     * @param array                                                           $data
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Model\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Checkout\Model\Cart $cartSingleton,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\SalesRule\Model\Coupon\MassgeneratorFactory $massgeneratorFactory,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollection,
        \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory $coreCouponsCollection,
        \Licentia\Panda\Model\ResourceModel\Coupons\CollectionFactory $couponsCollection,
        Session $sessionFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        SubscribersFactory $subscribersFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->couponFactory = $couponFactory;
        $this->customerSession = $customerSession;
        $this->ruleCollection = $ruleCollection;
        $this->coreCouponsCollection = $coreCouponsCollection;
        $this->couponsCollection = $couponsCollection;
        $this->customerFactory = $customerFactory;
        $this->ruleFactory = $ruleFactory;
        $this->massgeneratorFactory = $massgeneratorFactory;
        $this->cartSingleton = $cartSingleton;
        $this->session = $sessionFactory;
        $this->subscribersFactory = $subscribersFactory;
        $this->pandaHelper = $pandaHelper;
        $this->timezone = $timezone;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_init(ResourceModel\Coupons::class);
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {

        $model = $this->ruleCollection->create()
                                      ->addFieldToSelect('name')
                                      ->addFieldToSelect('rule_id');

        $return = [];

        foreach ($model as $rule) {
            $return[] = ['value' => $rule->getId(), 'label' => $rule->getName()];
        }

        return $return;
    }

    /**
     * @return array
     */
    public function toFormValues()
    {

        $values = $this->toOptionArray();

        $return = [];
        foreach ($values as $rule) {
            $return[$rule['value']] = $rule['label'];
        }

        return $return;
    }

    /**
     *
     * @param $event
     *
     * @return boolean
     */
    public function couponAfterOrder($event)
    {

        /** @var  \Magento\Sales\Model\Order $order */
        $order = $event->getEvent()->getOrder();

        if (!$order->getCouponCode()) {
            return true;
        }

        $coupon = $order->getCouponCode();

        $collection = $this->couponsCollection->create()->addFieldToFilter('coupon_code', $coupon);

        if ($collection->count() != 1) {
            return false;
        }

        $model = $collection->getFirstItem();

        $used = $this->pandaHelper->gmtDate();

        return $model->setData('times_used', 1)
                     ->setData('order_id', $order->getId())
                     ->setData('used_at', $used)
                     ->save();
    }

    /**
     *
     * @param $coupon
     *
     * @return boolean
     */
    public function validateCoupon($coupon)
    {

        $ruleCoupon = $this->couponFactory->create()->loadByCode($coupon);

        if ($ruleCoupon->getId()) {

            /** @var \Magento\SalesRule\Model\Rule $coreRule */
            $coreRule = $this->ruleFactory->create()->load($ruleCoupon->getRuleId());

            if ($coreRule->getCustomerId() && $coreRule->getCustomerId() != $this->customerSession->getCustomerId()) {
                return false;
            }

            if ($coreRule->getId()) {
                return true;
            }
        }

        $collection = $this->couponsCollection->create()
                                              ->addFieldToFilter('coupon_code', $coupon);

        if ($collection->count() != 1) {
            return true;
        }

        /** @var self $coupon */
        $coupon = $collection->getFirstItem();

        $curDate = $this->pandaHelper->gmtDate();
        if ($curDate >= $coupon->getExpiresAt() && $coupon->getExpiresAt()) {
            return false;
        }

        if ((int) $coupon->getTimesUsed() == 0 && !$coupon->getForce()) {
            return true;
        }

        $session = $this->session;
        $subscriberId = $session->getPandaSubscriber();

        /** @var Subscribers $subscriber */
        $subscriber = $this->subscribersFactory->create()->loadById($subscriberId);

        $cart = $this->cartSingleton;

        if ($subscriber) {
            $email = $subscriber->getEmail();
        } elseif ($this->customerSession->isLoggedIn()) {
            $customer = $this->customerSession->getCustomer();
            $email = $customer->getEmail();
        } elseif ($cart->getQuote()
                       ->getBillingAddress()
                       ->getData('email')) {
            $email = $cart->getQuote()
                          ->getBillingAddress()->getData('email');
        } else {
            return false;
        }

        if ((int) $coupon->getData('times_used') == 0 && $email == $coupon->getSubscriberEmail()) {
            return true;
        }

        return false;
    }

    /**
     * @param $coupon
     *
     * @return false|string
     */
    public function getCouponExpirationDate($coupon)
    {

        $coupon = $this->load($coupon, 'coupon_code');

        if (!$coupon->getId()) {
            return '';
        }

        return $this->timezone->date($coupon->getExpiresAt())->format('c');
    }

    /**
     * @param      $coupon
     * @param bool $hide
     *
     * @return bool
     */
    public function canShowCoupon($coupon, $hide = false)
    {

        if (!$hide) {
            return true;
        }

        $coupon = $this->load($coupon, 'coupon_code');

        if (!$coupon->getId()) {
            return false;
        }

        if ($coupon->getExpiresAt() && $this->timezone->date($coupon->getExpiresAt()) >= $this->timezone->date()) {
            return false;
        }

        return true;
    }

    /**
     * @param $hash
     *
     * @return \Magento\Framework\DataObject
     */
    public function getCouponFromHash($hash)
    {

        /** @var Subscribers $subscriber */
        $subscriber = $this->pandaHelper->getSubscriber();

        $coupon = $this->couponsCollection->create()
                                          ->addFieldToFilter('subscriber_email', $subscriber->getEmail())
                                          ->addFieldToFilter('hash', $hash);

        return $this->coreCouponsCollection->create()
                                           ->addFieldToFilter('code', $coupon->getFirstItem()->getCouponCode())
                                           ->getFirstItem();
    }

    /**
     * @param $hash
     *
     * @return \Magento\Framework\DataObject
     */
    public function getWidgetData($hash)
    {

        $resource = $this->getResource();
        $connection = $resource->getConnection();

        /** @var Subscribers $subscriber */
        $subscriber = $this->pandaHelper->getSubscriber();

        $data = $connection->fetchOne(
            $connection->select()
                       ->from($resource->getTable('panda_coupons_hash_list'), ['data'])
                       ->where('hash=?', $hash)
                       ->where('subscriber_email=?', $subscriber->getEmail())
        );

        $object = new \Magento\Framework\DataObject();

        $object->setData(json_decode($data, true));

        return $object;
    }

    /**
     * @param $params
     *
     * @return \Magento\Framework\DataObject|null
     * @throws \Exception
     */
    public function getCoupon($params)
    {

        $hash = sha1(json_encode($params));

        if (isset($this->couponRole[$hash])) {
            return $this->couponRole[$hash];
        }

        /** @var Subscribers $subscriber */
        $subscriber = $this->pandaHelper->getSubscriber();

        /** @var Campaigns $campaign */
        $campaign = $this->_registry->registry('panda_campaign');

        if (!$subscriber || !$subscriber->getId()) {
            return null;
        }

        if (!$campaign) {
            $campaign = new \Magento\Framework\DataObject();
        }

        $customerId = $subscriber->getCustomerId();

        if ((int) $customerId == 0) {
            $customer = new \Magento\Framework\DataObject;
        } else {
            $customer = $this->customerFactory->create()->load($customerId);
        }

        $rule = $this->ruleFactory->create()->load($params['rule']);

        if (!$rule->getId()) {
            return null;
        }

        $coupon = $this->couponsCollection->create()
                                          ->addFieldToFilter('subscriber_email', $subscriber->getEmail())
                                          ->addFieldToFilter('hash', $hash)
                                          ->addFieldToFilter('rule_id', $rule->getId());

        if ($coupon->count() > 1) {
            foreach ($coupon as $item) {
                $item->delete();
            }
        }
        if ($coupon->count() == 1) {
            return $this->coreCouponsCollection->create()
                                               ->addFieldToFilter('code', $coupon->getFirstItem()->getCouponCode())
                                               ->getFirstItem();
        }

        if ($rule->getUsesPerCoupon() != 1 || $rule->getUsesPerCustomer() != 1) {
            $rule->setUsesPerCoupon(1)
                 ->setUsesPerCustomer(1)
                 ->save();
        }

        $generator = $this->massgeneratorFactory->create();

        if (!isset($params['prefix'])) {
            $params['prefix'] = '';
        }
        if (!isset($params['suffix'])) {
            $params['suffix'] = '';
        }
        if (!isset($params['dash'])) {
            $params['dash'] = '';
        }
        if (!isset($params['force'])) {
            $params['force'] = 0;
        }
        if (!isset($params['hours'])) {
            $params['hours'] = 0;
        }
        $params['hours'] = (int) $params['hours'];

        $data = [
            'uses_per_customer' => 1,
            'uses_per_coupon'   => 1,
            'qty'               => 1,
            'length'            => (int) $params['length'] == 0 ? 10 : $params['length'],
            'to_date'           => $rule->getToDate(),
            'format'            => $params['format'],
            'suffix'            => $params['suffix'],
            'dash'              => $params['dash'],
            'prefix'            => $params['prefix'],
            'rule_id'           => $rule->getId(),
        ];

        $generator->validateData($data);

        $generator->setData($data);
        $generator->generatePool();
        $collection = $this->coreCouponsCollection->create()
                                                  ->addRuleToFilter($rule)
                                                  ->addGeneratedCouponsFilter()
                                                  ->setOrder('coupon_id', 'DESC')
                                                  ->setPageSize(1);

        if ($generator->getGeneratedCount() == 1 && $collection->count() == 1) {
            $couponRule = $collection->getFirstItem();

            $data = [];
            $data['coupon_code'] = $couponRule->getCode();
            $data['rule_id'] = $rule->getId();
            $data['subscriber_email'] = $subscriber->getEmail();
            $data['force'] = $params['force'];
            $data['customer_id'] = $customer->getId();
            $data['campaign_id'] = $campaign->getId();
            $data['created_at'] = $this->pandaHelper->gmtDate();
            $data['hash'] = $hash;

            if ($params['hours'] == 0) {
                $data['expires_at'] = date('Y-m-d H:i:s', strtotime('now +10years'));
            } else {
                $expires = new \DateTime($data['created_at']);
                $expires->add(new \DateInterval('PT' . $params['hours'] . 'H'));
                $data['expires_at'] = $expires->format('Y-m-d H:i:s');
            }

            $this->setData($data)->save();

            $hashList = [];
            $hashList['coupon_id'] = $this->getId();
            $hashList['hash'] = $hash;
            $hashList['data'] = json_encode($params);
            $hashList['subscriber_email'] = $subscriber->getEmail();

            $this->getResource()->getConnection()
                 ->insert($this->getResource()->getTable('panda_coupons_hash_list'), $hashList);

            $this->couponRole[$hash] = $couponRule;

            return $this->couponRole[$hash];
        } else {
            return null;
        }
    }

    /**
     * @param $customerId
     *
     * @return array
     */
    public function getUserCoupons($customerId)
    {

        $resource = $this->getResource();
        $connection = $resource->getConnection();

        $result = $connection->fetchAll(
            $connection->select()
                       ->from(
                           $resource->getTable('salesrule'),
                           [
                               'name',
                               'rule_id',
                               'description',
                               'from_date',
                               'to_date',
                           ]
                       )
                       ->join(
                           $resource->getTable('sales_order'),
                           'FIND_IN_SET(' . $resource->getTable('salesrule') . '.rule_id ,' .
                           $resource->getTable('sales_order') . '.applied_rule_ids)',
                           [
                               'customer_email',
                               'created_at',
                               'state',
                               'order_id' => 'entity_id',
                           ]
                       )
                       ->join(
                           $resource->getTable(
                               'salesrule_coupon'
                           ),
                           $resource->getTable('salesrule_coupon') . '.rule_id = ' .
                           $resource->getTable('salesrule') . '.rule_id',
                           [
                               'code',
                           ]
                       )
                       ->join(
                           $resource->getTable('customer_entity'),
                           $resource->getTable('customer_entity') . '.entity_id = ' .
                           $resource->getTable('sales_order') . '.customer_id',
                           [
                               'customer_id' => 'entity_id',
                           ]
                       )
                       ->where($resource->getTable('salesrule') . '.coupon_type = ?', 2)
                       ->where($resource->getTable('sales_order') . '.customer_id= ?', $customerId)
                       ->order($resource->getTable('sales_order') . '.entity_id DESC')
        );

        return $result;
    }

    /**
     * @param $couponId
     *
     * @return $this
     */
    public function setCouponId($couponId)
    {

        return $this->setData('coupon_id', $couponId);
    }

    /**
     * @param $couponCode
     *
     * @return $this
     */
    public function setCouponCode($couponCode)
    {

        return $this->setData('coupon_code', $couponCode);
    }

    /**
     * @param $subscriberEmail
     *
     * @return $this
     */
    public function setSubscriberEmail($subscriberEmail)
    {

        return $this->setData('subscriber_email', $subscriberEmail);
    }

    /**
     * @param $timesUsed
     *
     * @return $this
     */
    public function setTimesUsed($timesUsed)
    {

        return $this->setData('times_used', $timesUsed);
    }

    /**
     * @param $force
     *
     * @return $this
     */
    public function setForce($force)
    {

        return $this->setData('force', $force);
    }

    /**
     * @param $ruleId
     *
     * @return $this
     */
    public function setRuleId($ruleId)
    {

        return $this->setData('rule_id', $ruleId);
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
     * @param $campaignId
     *
     * @return $this
     */
    public function setCampaignId($campaignId)
    {

        return $this->setData('campaign_id', $campaignId);
    }

    /**
     * @param $usedAt
     *
     * @return $this
     */
    public function setUsedAt($usedAt)
    {

        return $this->setData('used_at', $usedAt);
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
     * @param $orderId
     *
     * @return $this
     */
    public function setOrderId($orderId)
    {

        return $this->setData('order_id', $orderId);
    }

    /**
     * @param $expiresAt
     *
     * @return $this
     */
    public function setExpiresAt($expiresAt)
    {

        return $this->setData('expires_at', $expiresAt);
    }

    /**
     * @return mixed
     */
    public function getCouponId()
    {

        return $this->getData('coupon_id');
    }

    /**
     * @return mixed
     */
    public function getCouponCode()
    {

        return $this->getData('coupon_code');
    }

    /**
     * @return mixed
     */
    public function getSubscriberEmail()
    {

        return $this->getData('subscriber_email');
    }

    /**
     * @return mixed
     */
    public function getTimesUsed()
    {

        return $this->getData('times_used');
    }

    /**
     * @return mixed
     */
    public function getForce()
    {

        return $this->getData('force');
    }

    /**
     * @return mixed
     */
    public function getRuleId()
    {

        return $this->getData('rule_id');
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
    public function getCampaignId()
    {

        return $this->getData('campaign_id');
    }

    /**
     * @return mixed
     */
    public function getUsedAt()
    {

        return $this->getData('used_at');
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
    public function getOrderId()
    {

        return $this->getData('order_id');
    }

    /**
     * @return mixed
     */
    public function getExpiresAt()
    {

        return $this->getData('expires_at');
    }

    /**
     * @param $hash
     *
     * @return $this
     */
    public function setHash($hash)
    {

        return $this->setData('hash', $hash);
    }

    /**
     * @return mixed
     */
    public function getHash()
    {

        return $this->getData('hash');
    }
}
