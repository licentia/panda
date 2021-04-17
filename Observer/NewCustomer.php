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
    protected \Magento\Newsletter\Model\SubscriberFactory $coreSubscribersFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected \Licentia\Panda\Helper\Data $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\AutorespondersFactory
     */
    protected \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory;

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
