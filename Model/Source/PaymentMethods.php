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

namespace Licentia\Panda\Model\Source;

/**
 * Class PaymentMethods
 *
 * @package Licentia\Panda\Model\Source
 */
class PaymentMethods
{

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @param \Magento\Payment\Helper\Data $paymentHelper
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper
    ) {

        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {

        $options = [];
        foreach ($this->toOptionHash() as $key => $option) {
            $options[] = ['value' => $key, 'label' => $option];
        }

        return $options;
    }

    /**
     * @return array
     */
    public function toOptionHash()
    {

        return $this->paymentHelper->getPaymentMethodList();
    }

}
