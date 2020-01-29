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
     * @var \Licentia\Panda\Logger\Logger
     */
    protected $pandaLogger;

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
     * @param \Licentia\Panda\Logger\Logger            $pandaLogger
     * @param \Magento\Framework\Registry              $registry
     * @param \Licentia\Panda\Model\SubscribersFactory $subscribersFactory
     */
    public function __construct(
        \Licentia\Panda\Logger\Logger $pandaLogger,
        \Magento\Framework\Registry $registry,
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory
    ) {

        $this->subscribersFactory = $subscribersFactory;
        $this->registry = $registry;
        $this->pandaLogger = $pandaLogger;
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
            $this->pandaLogger->warning($e->getMessage());
        }

        return $this;
    }
}
