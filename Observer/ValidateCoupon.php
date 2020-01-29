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

use Licentia\Panda\Model;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class ValidateCoupon
 *
 * @package Licentia\Panda\Observer
 */
class ValidateCoupon implements ObserverInterface
{

    /**
     * @var \Licentia\Panda\Logger\Logger
     */
    protected $pandaLogger;

    /**
     * @var Model\CouponsFactory
     */
    protected $couponsFactory;

    /**
     * ValidateCoupon constructor.
     *
     * @param \Licentia\Panda\Logger\Logger $pandaLogger
     * @param Model\CouponsFactory          $couponsFactory
     */
    public function __construct(
        \Licentia\Panda\Logger\Logger $pandaLogger,
        \Licentia\Panda\Model\CouponsFactory $couponsFactory
    ) {

        $this->couponsFactory = $couponsFactory;
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
            $request = $event->getControllerAction()->getRequest();
            $coupon = $request->getParam('coupon_code');

            if ($request->getParam('remove') == 1) {
                return false;
            }

            /** @var \Licentia\Panda\Model\Coupons $model */
            $model = $this->couponsFactory->create();

            if (!$model->validateCoupon($coupon)) {
                $request->setParam('coupon_code', __('invalid') . ': ' . $coupon . ' ');
            }
        } catch (\Exception $e) {
            $this->pandaLogger->warning($e->getMessage());
        }
    }
}
