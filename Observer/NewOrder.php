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
 * Class NewOrder
 *
 * @package Licentia\Panda\Observer
 */
class NewOrder implements ObserverInterface
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected \Licentia\Panda\Helper\Data $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\ConversionstmpFactory
     */
    protected \Licentia\Panda\Model\ConversionstmpFactory $conversionstmpFactory;

    /**
     * @var \Licentia\Panda\Model\CouponsFactory
     */
    protected \Licentia\Panda\Model\CouponsFactory $couponsFactory;

    /**
     * @var \Licentia\Panda\Model\AutorespondersFactory
     */
    protected \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory;

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected \Licentia\Panda\Model\SubscribersFactory $subscribersFactory;

    /**
     * NewOrder constructor.
     *
     * @param \Licentia\Panda\Model\SubscribersFactory    $subscribersFactory
     * @param \Licentia\Panda\Helper\Data                 $pandaHelper
     * @param \Licentia\Panda\Model\ConversionstmpFactory $conversionstmpFactory
     * @param \Licentia\Panda\Model\CouponsFactory        $couponsFactory
     * @param \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory
     */
    function __construct(
        \Licentia\Panda\Model\SubscribersFactory $subscribersFactory,
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\ConversionstmpFactory $conversionstmpFactory,
        \Licentia\Panda\Model\CouponsFactory $couponsFactory,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory
    ) {

        $this->conversionstmpFactory = $conversionstmpFactory;
        $this->autorespondersFactory = $autorespondersFactory;
        $this->pandaHelper = $pandaHelper;
        $this->couponsFactory = $couponsFactory;
        $this->subscribersFactory = $subscribersFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        try {
            $this->couponsFactory->create()->couponAfterOrder($event);

            $this->conversionstmpFactory->create()->afterOrder($event);

            $this->autorespondersFactory->create()->newOrder($event);

            $this->subscribersFactory->create()->afterOrderEvent($event);
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }
    }
}
