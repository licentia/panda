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