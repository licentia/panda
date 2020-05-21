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
 * @modified   21/05/20, 20:58 GMT
 *
 */

namespace Licentia\Panda\Plugin;

/**
 * Class ApplyCoupon
 *
 * @package Licentia\Panda\Plugin
 */
class ApplyCoupon
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * ApplyCoupon constructor.
     *
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession
    ) {

        $this->customerSession = $customerSession;
    }

    /**
     * @param \Magento\SalesRule\Model\Utility $subject
     * @param \Closure                         $proceed
     * @param                                  $rule
     * @param                                  $address
     *
     * @return bool|\Closure
     */
    public function aroundCanProcessRule(
        \Magento\SalesRule\Model\Utility $subject,
        \Closure $proceed,
        $rule,
        $address
    ) {

        if ($rule->getCustomerId() && $rule->getCustomerId() != $this->customerSession->getCustomerId()) {
            return false;
        }

        return $proceed($rule, $address);

    }

}