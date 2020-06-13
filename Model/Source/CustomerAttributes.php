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
 * Class CustomerAttributes
 *
 * @package Licentia\Panda\Model\Source
 */
class CustomerAttributes
{

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * CustomerAttributes constructor.
     *
     * @param \Magento\Customer\Model\Customer $customer
     */
    public function __construct(
        \Magento\Customer\Model\Customer $customer
    ) {

        $this->customer = $customer;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {

        $productAttributes = $this->customer->getAttributes();

        $attrToRemove = [
            'increment_id',
            'updated_at',
            'disable_auto_group_change',
            'updated_at',
            'entity_type_id',
            'failures_num',
            'lock_expires',
            'first_failure',
            'confirmation',
            'default_billing',
            'default_shipping',
            'password_hash',
        ];

        $return = [];
        $return[] = ['value' => '', 'label' => __('-- Ignore --'),];
        $attributesAccount = [];
        /** @var \Magento\Customer\Model\Attribute $attribute */
        foreach ($productAttributes as $attribute) {
            if (in_array($attribute->getAttributeCode(), $attrToRemove)) {
                continue;
            }

            if (strlen($attribute->getFrontendLabel()) == 0) {
                continue;
            }

            $attributesAccount[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        asort($attributesAccount);

        foreach ($attributesAccount as $k => $attribute) {
            $return[] = [
                'value' => $k,
                'label' => $attribute,
            ];
        }

        return $return;
    }
}
