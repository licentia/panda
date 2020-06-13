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
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscribersFactory;

    /**
     * AddEmailFromAddress constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Magento\Newsletter\Model\SubscriberFactory        $subscribersFactory
     * @param \Licentia\Panda\Helper\Data                        $pandaHelper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Newsletter\Model\SubscriberFactory $subscribersFactory,
        \Licentia\Panda\Helper\Data $pandaHelper
    ) {

        $this->subscribersFactory = $subscribersFactory;
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
            $this->pandaHelper->logWarning($e);
        }

        return true;
    }
}
