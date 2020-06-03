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
 * @modified   03/06/20, 16:18 GMT
 *
 */

namespace Licentia\Panda\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class NewCustomer
 *
 * @package Licentia\Panda\Observer
 */
class NewCustomer implements ObserverInterface
{

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $coreSubscribersFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\AutorespondersFactory
     */
    protected $autorespondersFactory;

    /**
     * NewCustomer constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Magento\Newsletter\Model\SubscriberFactory        $coreSubscribersFactory
     * @param \Licentia\Panda\Helper\Data                        $pandaHelper
     * @param \Licentia\Panda\Model\AutorespondersFactory        $autorespondersFactory
     */
    function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Newsletter\Model\SubscriberFactory $coreSubscribersFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory
    ) {

        $this->autorespondersFactory = $autorespondersFactory;
        $this->coreSubscribersFactory = $coreSubscribersFactory;
        $this->scopeConfig = $scopeConfigInterface;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     *
     * @return bool
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        try {
            /** @var \Magento\Customer\Model\Customer $customer */
            $customer = $event->getEvent()->getCustomer();

            $this->autorespondersFactory->create()->newCustomer($event);

            if ($this->scopeConfig->isSetFlag('panda_nuntius/info/auto')) {
                $this->coreSubscribersFactory->create()->subscribe($customer->getEmail());
            }
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }

        return false;
    }
}
