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
 * Class AddEmailFromAddress
 *
 * @package Licentia\Panda\Observer
 */
class AddEmailFromAddress implements ObserverInterface
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Licentia\Panda\Logger\Logger
     */
    protected $pandaLogger;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscribersFactory;

    /**
     * AddEmailFromAddress constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Magento\Newsletter\Model\SubscriberFactory        $subscribersFactory
     * @param \Licentia\Panda\Logger\Logger                      $pandaLogger
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Newsletter\Model\SubscriberFactory $subscribersFactory,
        \Licentia\Panda\Logger\Logger $pandaLogger
    ) {

        $this->subscribersFactory = $subscribersFactory;
        $this->scopeConfig = $scopeConfigInterface;
        $this->pandaLogger = $pandaLogger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     *
     * @return bool
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        try {
            /** @var \Magento\Quote\Model\Quote\Address $address */
            $address = $event->getDataObject();

            if (!$address->getEmail() || $address->getAddressType() != 'billing') {
                return false;
            }

            if (!$this->scopeConfig->isSetFlag('panda_nuntius/info/auto')) {
                return false;
            }

            $this->subscribersFactory->create()->subscribe($address->getEmail());
        } catch (\Exception $e) {
            $this->pandaLogger->warning($e->getMessage());
        }

        return true;
    }
}
