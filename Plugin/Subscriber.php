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

namespace Licentia\Panda\Plugin;

/**
 * Class Subscriber
 *
 * @package Licentia\Panda\Plugin
 */
class Subscriber
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected $subscribersFactory;

    /**
     * Subscriber constructor.
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface  $customerRepository
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManagerInterface
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface
     * @param \Licentia\Panda\Model\SubscribersFactory           $subscribersFactory
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory
    ) {

        $this->scopeConfig = $scopeInterface;
        $this->storeManager = $storeManagerInterface;
        $this->subscribersFactory = $subscribersFactory;
        $this->customerReposiroty = $customerRepository;
    }

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subject
     * @param                                      $result
     *
     * @return mixed
     */
    public function afterSave(\Magento\Newsletter\Model\Subscriber $subject, $result)
    {

        if (!$this->scopeConfig->isSetFlag('panda_nuntius/info/enabled')) {
            return $result;
        }

        if ($subject->getId() && !$subject->getData('in_panda') && $subject->dataHasChangedFor('subscriber_status')) {
            $subscriber = $this->subscribersFactory->create()
                                                   ->loadByEmail($subject->getSubscriberEmail())
                                                   ->setStatus($subject->getSubscriberStatus());

            try {
                $customerId = $this->customerReposiroty->get($subject->getEmail());
                if ($customerId->getId()) {
                    $subscriber->setCustomerId($customerId->getId());
                }
            } catch (\Exception $e) {

            }

            $subscriber->setEmail($subject->getSubscriberEmail());
            $subscriber->setCode($subject->getSubscriberConfirmCode());
            $subscriber->setStoreId($subject->getStoreId());

            $subscriber->save();
        }

        return $result;
    }
}
