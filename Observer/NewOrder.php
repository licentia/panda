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
 * Class NewOrder
 *
 * @package Licentia\Panda\Observer
 */
class NewOrder implements ObserverInterface
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\ConversionstmpFactory
     */
    protected $conversionstmpFactory;

    /**
     * @var \Licentia\Panda\Model\CouponsFactory
     */
    protected $couponsFactory;

    /**
     * @var \Licentia\Panda\Model\AutorespondersFactory
     */
    protected $autorespondersFactory;

    /**
     * @var \Licentia\Panda\Model\SubscribersFactory
     */
    protected $subscribersFactory;

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
