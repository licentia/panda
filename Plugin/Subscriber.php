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

        if (!$this->scopeConfig->isSetFlag('panda_nuntius/info/enable')) {
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
