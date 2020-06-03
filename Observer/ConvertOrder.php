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
 * Class ConvertOrder
 *
 * @package Licentia\Panda\Observer
 */
class ConvertOrder implements ObserverInterface
{

    /**
     * @var \Licentia\Panda\Helper\Data
     */
    protected $pandaHelper;

    /**
     * @var \Licentia\Panda\Model\ConversionsFactory
     */
    protected $conversionsFactory;

    /**
     * @var \Licentia\Panda\Model\AutorespondersFactory
     */
    protected $autorespondersFactory;

    /**
     * ConvertOrder constructor.
     *
     * @param \Licentia\Panda\Helper\Data                 $pandaHelper
     * @param \Licentia\Panda\Model\ConversionsFactory    $conversionsFactory
     * @param \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory
     */
    public function __construct(
        \Licentia\Panda\Helper\Data $pandaHelper,
        \Licentia\Panda\Model\ConversionsFactory $conversionsFactory,
        \Licentia\Panda\Model\AutorespondersFactory $autorespondersFactory
    ) {

        $this->autorespondersFactory = $autorespondersFactory;
        $this->conversionsFactory = $conversionsFactory;
        $this->pandaHelper = $pandaHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $event
     */
    public function execute(\Magento\Framework\Event\Observer $event)
    {

        try {
            $this->conversionsFactory->create()->convertOrder($event);

            $this->autorespondersFactory->create()->boughtProduct($event);

            $this->autorespondersFactory->create()->buildCycles($event);
        } catch (\Exception $e) {
            $this->pandaHelper->logWarning($e);
        }
    }
}
