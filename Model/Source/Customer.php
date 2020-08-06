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

namespace Licentia\Panda\Model\Source;

/**
 * Class Customer
 *
 * @package Licentia\Panda\Model\Source
 */
class Customer
{

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
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

        $customerAttributes = $this->customer->getAttributes();

        $attrToRemove = [
            'increment_id',
            'website_id',
            'disable_auto_group_change',
            'gender',
            'group_id',
            'updated_at',
            'attribute_set_id',
            'entity_type_id',
            'confirmation',
            'default_billing',
            'default_shipping',
            'password_hash',
        ];

        $attributesAccount = [];
        foreach ($customerAttributes as $attribute) {
            if (in_array($attribute->getAttributeCode(), $attrToRemove)) {
                continue;
            }

            if (strlen($attribute->getFrontendLabel()) == 0) {
                continue;
            }

            $attributesAccount[$attribute->getAttributeCode()] = $attribute->getFrontendLabel() .
                                                                 ' (' . $attribute->getAttributeCode() . ')';
        }

        asort($attributesAccount);

        return $attributesAccount;
    }
}
