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
 * Class Attributes
 *
 * @package Licentia\Panda\Model\Source
 */
class Cellphone
{

    /**
     * @var \Magento\Eav\Model\Entity\TypeFactory
     */
    protected $typeFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory
     */
    protected $attributeCollection;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @param \Magento\Customer\Model\Customer                                    $customer
     * @param \Magento\Eav\Model\Entity\TypeFactory                               $typeFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attributeCollection
     */
    public function __construct(
        \Magento\Customer\Model\Customer $customer,
        \Magento\Eav\Model\Entity\TypeFactory $typeFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attributeCollection
    ) {

        $this->typeFactory = $typeFactory;
        $this->attributeCollection = $attributeCollection;
        $this->customer = $customer;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {

        $type = $this->typeFactory->create()->loadByCode('customer_address');
        $attributes = $this->attributeCollection->create()->setEntityTypeFilter($type);

        $productAttributes = $this->customer->getAttributes();

        $attrToRemove = [
            'increment_id',
            'updated_at',
            'website_id',
            'disable_auto_group_change',
            'gender',
            'group_id',
            'updated_at',
            'updated_at',
            'updated_at',
            'attribute_set_id',
            'entity_type_id',
            'confirmation',
            'default_billing',
            'default_shipping',
            'password_hash',
        ];

        $return = [];
        $return[] = ['value' => 'ac_', 'label' => __('-- Ignore --'),];
        $attributesAccount = [];
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
                'value' => 'ac_' . $k,
                'label' => __('Account') . ' / ' . $attribute,
            ];
        }

        foreach ($attributes as $attribute) {
            if (strlen($attribute['frontend_label']) == 0) {
                continue;
            }
            $return[] = [
                'value' => 'ad_' . $attribute['attribute_code'],
                'label' => __('Address') . ' / ' . $attribute['frontend_label'],
            ];
        }

        return $return;
    }
}
