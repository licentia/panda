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
