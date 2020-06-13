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

namespace Licentia\Panda\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class AutoMatchCustomer
 *
 * @package Licentia\Panda\Observer
 */
class AutoMatchCustomer implements ObserverInterface
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected $subscribersFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * AutoMatchCustomer constructor.
     *
     * @param \Licentia\Panda\Helper\Data              $pandaHelper
     * @param \Magento\Framework\Registry              $registry
     * @param \Licentia\Panda\Model\SubscribersFactory $subscribersFactory
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory
    ) {

        $this->subscribersFactory = $subscribersFactory;
        $this->registry = $registry;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        try {
            if ($this->registry->registry('panda_auto_match')) {
                return $this;
            }
            /** @var \Magento\Customer\Model\Customer $customer */
            $customer = $event->getDataObject();

            if ($customer->getOrigData('email') == $customer->getData('email') &&
                $customer->getOrigData('firstname') == $customer->getData('firstname') &&
                $customer->getOrigData('lastname') == $customer->getData('lastname') &&
                $customer->getOrigData('store_id') == $customer->getData('store_id')
            ) {
                return $this;
            }

            /** @var \Licentia\Panda\Model\Subscribers $subscriber */
            $subscriber = $this->subscribersFactory->create()
                                                   ->loadSubscriber($customer->getEmail(), $customer->getStoreId());

            if ($subscriber && $subscriber->getId()) {
                $subscriber->setCustomerId($customer->getId());
                $subscriber->setEmail($customer->getEmail());

                if ($customer->getStoreId() > 0) {
                    $subscriber->setStoreId($customer->getStoreId());
                }
                $subscriber->save();
            }

            $this->registry->register('panda_auto_match', true);
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }

        return $this;
    }
}
